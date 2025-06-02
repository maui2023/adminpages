<?php
require_once 'auth.php';
require_login();
if($_SESSION['role'] !== 'superadmin'){
    header('location: welcome.php');
    exit;
}

$username = $password = $confirm_password = $role = '';
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
    $role = isset($_POST['role']) && $_POST['role'] === 'superadmin' ? 'superadmin' : 'user';
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $sql = 'INSERT INTO admin (username, password, role) VALUES (?, ?, ?)';
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, 'sss', $param_username, $param_password, $param_role);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_role = $role;
            if(mysqli_stmt_execute($stmt)){
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
            <option value="superadmin" <?php echo $role==='superadmin'?'selected':''; ?>>Super Admin</option>
        </select>
    </div>
    <input type="submit" class="btn btn-primary" value="Create">
</form>
<?php include 'footer.php'; ?>
