<?php
require_once 'auth.php';
require_login();
if($_SESSION['role'] !== 'superadmin'){
    header('location: welcome.php');
    exit;
}

$pages = ['page_a','page_b','page_c'];
$modules = ['dashboard','application','letter'];

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
<?php include 'footer.php'; ?>
