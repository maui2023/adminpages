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

function has_permission($page, $action = 'view'){
    global $link;
    if($_SESSION['role'] === 'superadmin'){
        return true;
    }
    $sql = "SELECT can_view, can_edit FROM permissions WHERE user_id=? AND page=?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, 'is', $_SESSION['id'], $page);
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

function require_permission($page, $action = 'view'){
    require_login();
    if(!has_permission($page, $action)){
        header('location: welcome.php');
        exit;
    }
}
?>
