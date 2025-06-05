<?php
require_once 'auth.php';
require_login();

$new_password = $confirm_password = '';
$new_password_err = $confirm_password_err = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(empty(trim($_POST['new_password']))){
        $new_password_err = 'Please enter the new password.';
    } elseif(strlen(trim($_POST['new_password'])) < 6){
        $new_password_err = 'Password must have atleast 6 characters.';
    } else{
        $new_password = trim($_POST['new_password']);
    }
    if(empty(trim($_POST['confirm_password']))){
        $confirm_password_err = 'Please confirm the password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = 'Password did not match.';
        }
    }
    if(empty($new_password_err) && empty($confirm_password_err)){
        $sql = 'UPDATE admin SET password=? WHERE id=?';
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, 'si', $param_password, $param_id);
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION['id'];
            if(mysqli_stmt_execute($stmt)){
                session_destroy();
                header('location: login.php');
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<?php include 'header.php'; ?>
<h2>Profile</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
        <div class="invalid-feedback"><?php echo $new_password_err; ?></div>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
        <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
    </div>
    <input type="submit" class="btn btn-primary" value="Change">
    <a class="btn btn-link" href="welcome.php">Cancel</a>
</form>
<?php include 'footer.php'; ?>
