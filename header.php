<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
// Fetch pages for navigation
$nav_pages = [];
$res = mysqli_query($link, "SELECT name FROM pages ORDER BY id");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $nav_pages[] = $row['name'];
    }
    mysqli_free_result($res);
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Pages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="welcome.php">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <?php foreach($nav_pages as $np): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>.php"><?php echo ucwords(str_replace('_',' ', $np)); ?></a></li>
                <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <button id="themeToggle" type="button" class="btn btn-secondary me-2">Toggle Theme</button>
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                <a href="login.php" class="btn btn-outline-light">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-4">
