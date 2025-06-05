<?php
require_once 'auth.php';
require_login();
if($_SESSION['role'] !== 'superadmin'){
    header('location: welcome.php');
    exit;
}

$pages = ['page_a','page_b','page_c'];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permissions'])){
    foreach($_POST['permissions'] as $userId => $pagePerms){
        foreach($pages as $page){
            $level = isset($pagePerms[$page]) ? $pagePerms[$page] : 'none';
            // Remove existing permission
            $stmt = mysqli_prepare($link, 'DELETE FROM permissions WHERE user_id=? AND page=?');
            if($stmt){
                mysqli_stmt_bind_param($stmt, 'is', $userId, $page);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            if($level !== 'none'){
                $can_view = 1;
                $can_edit = $level === 'edit' ? 1 : 0;
                $stmt = mysqli_prepare($link, 'INSERT INTO permissions (user_id, page, can_view, can_edit) VALUES (?, ?, ?, ?)');
                if($stmt){
                    mysqli_stmt_bind_param($stmt, 'isii', $userId, $page, $can_view, $can_edit);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
    // Redirect to avoid resubmission
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
    $permissions[$user['id']] = array_fill_keys($pages, 'none');
    $stmt = mysqli_prepare($link, 'SELECT page, can_view, can_edit FROM permissions WHERE user_id=?');
    if($stmt){
        mysqli_stmt_bind_param($stmt, 'i', $user['id']);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_bind_result($stmt, $page, $can_view, $can_edit);
            while(mysqli_stmt_fetch($stmt)){
                if($can_edit){
                    $level = 'edit';
                } elseif($can_view){
                    $level = 'view';
                } else {
                    $level = 'none';
                }
                $permissions[$user['id']][$page] = $level;
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
<?php foreach($pages as $p): $val = $permissions[$u['id']][$p]; ?>
      <td>
        <select name="permissions[<?php echo $u['id']; ?>][<?php echo $p; ?>]" class="form-select">
          <option value="none" <?php echo $val==='none'?'selected':''; ?>>None</option>
          <option value="view" <?php echo $val==='view'?'selected':''; ?>>View</option>
          <option value="edit" <?php echo $val==='edit'?'selected':''; ?>>Edit</option>
        </select>
      </td>
<?php endforeach; ?>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<button type="submit" class="btn btn-primary">Save</button>
</form>
<?php include 'footer.php'; ?>
