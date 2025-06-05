<?php
require_once 'auth.php';
require_login();
if($_SESSION['role'] !== 'superadmin'){
    header('location: welcome.php');
    exit;
}

// Fetch pages and modules from database
$pages = [];
$result = mysqli_query($link, "SELECT name FROM pages ORDER BY id");
if($result){
    while($r = mysqli_fetch_assoc($result)){
        $pages[] = $r['name'];
    }
    mysqli_free_result($result);
}

$modules = [];
$result = mysqli_query($link, "SELECT name FROM modules ORDER BY id");
if($result){
    while($r = mysqli_fetch_assoc($result)){
        $modules[] = $r['name'];
    }
    mysqli_free_result($result);
}

// Handle new page creation
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_page'])){
    $pageName = preg_replace('/[^a-zA-Z0-9_]+/', '_', strtolower(trim($_POST['new_page'])));
    if($pageName !== ''){
        $stmt = mysqli_prepare($link, 'INSERT INTO pages (name) VALUES (?)');
        if($stmt){
            mysqli_stmt_bind_param($stmt, 's', $pageName);
            if(mysqli_stmt_execute($stmt)){
                $template = file_get_contents('page_template.php');
                $content = str_replace('{{page}}', $pageName, $template);
                file_put_contents($pageName . '.php', $content);
                $pages[] = $pageName;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permissions'])){
    foreach($_POST['permissions'] as $userId => $pagesData){
        foreach($pages as $page){
            foreach($modules as $module){
                $view = isset($pagesData[$page][$module]['view']) ? 1 : 0;
                $edit = isset($pagesData[$page][$module]['edit']) ? 1 : 0;

                $stmt = mysqli_prepare($link, 'DELETE FROM permissions WHERE user_id=? AND page=? AND module=?');
                if($stmt){
                    mysqli_stmt_bind_param($stmt, 'iss', $userId, $page, $module);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                if($view || $edit){
                    $stmt = mysqli_prepare($link, 'INSERT INTO permissions (user_id, page, module, can_view, can_edit) VALUES (?, ?, ?, ?, ?)');
                    if($stmt){
                        mysqli_stmt_bind_param($stmt, 'issii', $userId, $page, $module, $view, $edit);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                }
            }
        }
    }
    header('location: dashboard.php');
    exit;
}

// Fetch users
$users = [];
$result = mysqli_query($link, "SELECT id, username FROM admin ORDER BY username");
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $users[] = $row;
    }
    mysqli_free_result($result);
}

// Fetch permissions
$permissions = [];
foreach($users as $user){
    foreach($pages as $p){
        foreach($modules as $m){
            $permissions[$user['id']][$p][$m] = ['view'=>0,'edit'=>0];
        }
    }
    $stmt = mysqli_prepare($link, 'SELECT page, module, can_view, can_edit FROM permissions WHERE user_id=?');
    if($stmt){
        mysqli_stmt_bind_param($stmt, 'i', $user['id']);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_bind_result($stmt, $page, $module, $can_view, $can_edit);
            while(mysqli_stmt_fetch($stmt)){
                $permissions[$user['id']][$page][$module] = [
                    'view'=>(int)$can_view,
                    'edit'=>(int)$can_edit
                ];
            }
        }
        mysqli_stmt_close($stmt);
    }
}

include 'header.php';
?>
<h2>User Permissions</h2>
<form method="post">
<table class="table">
  <thead>
    <tr>
      <th>User</th>
<?php foreach($pages as $p): ?>
      <th><?php echo htmlspecialchars($p); ?></th>
<?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
<?php foreach($users as $u): ?>
    <tr>
      <td><?php echo htmlspecialchars($u['username']); ?></td>
<?php foreach($pages as $p): ?>
      <td>
<?php foreach($modules as $m): $perm = $permissions[$u['id']][$p][$m]; ?>
        <div class="form-check">
          <label class="form-check-label me-2">
            <input type="checkbox" class="form-check-input" name="permissions[<?php echo $u['id']; ?>][<?php echo $p; ?>][<?php echo $m; ?>][view]" <?php echo $perm['view']?'checked':''; ?>><?php echo htmlspecialchars($m); ?> V
          </label>
          <label class="form-check-label">
            <input type="checkbox" class="form-check-input" name="permissions[<?php echo $u['id']; ?>][<?php echo $p; ?>][<?php echo $m; ?>][edit]" <?php echo $perm['edit']?'checked':''; ?>>E
          </label>
        </div>
<?php endforeach; ?>
      </td>
<?php endforeach; ?>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<button type="submit" class="btn btn-primary">Save</button>
</form>

<hr>
<h3>Create Page</h3>
<form method="post" class="mb-4">
  <div class="mb-3">
    <label class="form-label">Page Name</label>
    <input type="text" name="new_page" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-secondary">Create Page</button>
</form>
<?php include 'footer.php'; ?>
