<?php
require_once 'auth.php';
require_login();
include 'header.php';
?>
<h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
<p>Use the navigation menu to access pages.</p>
<?php if($_SESSION['role']==='superadmin'): ?>
<a href="register.php" class="btn btn-success me-2">Create User</a>
<a href="dashboard.php" class="btn btn-primary">Manage Permissions</a>
<?php endif; ?>
<?php include 'footer.php'; ?>
