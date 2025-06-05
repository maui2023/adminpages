<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(){
    if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
        header('location: login.php');
        exit;
    }
}

// Check if the current user has permission for a module/function on a page
function has_permission($page, $module, $action = 'view'){
    global $link;
    if($_SESSION['role'] === 'superadmin'){
        return true;
    }
    $sql = "SELECT can_view, can_edit FROM permissions WHERE user_id=? AND page=? AND module=?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, 'iss', $_SESSION['id'], $page, $module);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_bind_result($stmt, $view, $edit);
            if(mysqli_stmt_fetch($stmt)){
                mysqli_stmt_close($stmt);
                if($action === 'edit') return (bool)$edit;
                return (bool)$view || (bool)$edit;
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Convenience wrapper that forces a permission check
function require_permission($page, $module, $action = 'view'){
    require_login();
    if(!has_permission($page, $module, $action)){
        header('location: welcome.php');
        exit;
    }
}
?>
