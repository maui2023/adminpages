<?php
require_once 'auth.php';
require_login();
// Only admins and superadmins can create users
if($_SESSION['role'] === 'user'){
    header('location: welcome.php');
    exit;
}

$username = $password = $confirm_password = '';
$role = 'user';
$level_id = null;
$levels = [];
$res = mysqli_query($link, "SELECT id, name FROM user_levels ORDER BY name");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $levels[] = $row;
    }
    mysqli_free_result($res);
}
$username_err = $password_err = $confirm_password_err = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(empty(trim($_POST['username']))){
        $username_err = 'Please enter a username.';
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['username']))){
        $username_err = 'Username can only contain letters, numbers, and underscores.';
    } else {
        $sql = 'SELECT id FROM admin WHERE username = ?';
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, 's', $param_username);
            $param_username = trim($_POST['username']);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = 'This username is already taken.';
                } else{
                    $username = trim($_POST['username']);
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter a password.';
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = 'Password must have atleast 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }
    if(empty(trim($_POST['confirm_password']))){
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = 'Password did not match.';
        }
    }
    if($_SESSION['role'] === 'superadmin'){
        $role = in_array($_POST['role'], ['superadmin','admin','user']) ? $_POST['role'] : 'user';
    } else {
        $role = 'user';
    }
    $level_id = isset($_POST['level_id']) ? (int)$_POST['level_id'] : null;
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $sql = 'INSERT INTO admin (username, password, role, level_id) VALUES (?, ?, ?, ?)';
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, 'sssi', $param_username, $param_password, $param_role, $param_level);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_role = $role;
            $param_level = $level_id;
            if(mysqli_stmt_execute($stmt)){
                $newUserId = mysqli_insert_id($link);
                if($level_id){
                    $permStmt = mysqli_prepare($link, 'SELECT page, module, can_view, can_edit FROM user_level_permissions WHERE level_id=?');
                    if($permStmt){
                        mysqli_stmt_bind_param($permStmt, 'i', $level_id);
                        if(mysqli_stmt_execute($permStmt)){
                            mysqli_stmt_bind_result($permStmt, $p, $m, $v, $e);
                            while(mysqli_stmt_fetch($permStmt)){
                                $ins = mysqli_prepare($link, 'INSERT INTO permissions (user_id, page, module, can_view, can_edit) VALUES (?,?,?,?,?)');
                                if($ins){
                                    mysqli_stmt_bind_param($ins, 'issii', $newUserId, $p, $m, $v, $e);
                                    mysqli_stmt_execute($ins);
                                    mysqli_stmt_close($ins);
                                }
                            }
                        }
                        mysqli_stmt_close($permStmt);
                    }
                }
                header('location: welcome.php');
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<?php include 'header.php'; ?>
<h2>Create User</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
        <div class="invalid-feedback"><?php echo $username_err; ?></div>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        <div class="invalid-feedback"><?php echo $password_err; ?></div>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
        <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
    </div>
    <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
            <option value="user" <?php echo $role==='user'?'selected':''; ?>>User</option>
            <?php if($_SESSION['role']==='superadmin'): ?>
            <option value="admin" <?php echo $role==='admin'?'selected':''; ?>>Admin</option>
            <option value="superadmin" <?php echo $role==='superadmin'?'selected':''; ?>>Super Admin</option>
            <?php endif; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">User Level</label>
        <select name="level_id" class="form-select">
            <option value="">None</option>
            <?php foreach($levels as $lvl): ?>
            <option value="<?php echo $lvl['id']; ?>" <?php echo $level_id==$lvl['id']?'selected':''; ?>><?php echo htmlspecialchars($lvl['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <input type="submit" class="btn btn-primary" value="Create">
</form>
<?php include 'footer.php'; ?>
