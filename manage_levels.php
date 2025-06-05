<?php
require_once 'auth.php';
require_login();
if(!in_array($_SESSION['role'], ['admin','superadmin'])){
    header('location: welcome.php');
    exit;
}

// Fetch pages and modules
$pages = [];
$res = mysqli_query($link, "SELECT name FROM pages ORDER BY id");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $pages[] = $row['name'];
    }
    mysqli_free_result($res);
}
$modules = [];
$res = mysqli_query($link, "SELECT name FROM modules ORDER BY id");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $modules[] = $row['name'];
    }
    mysqli_free_result($res);
}

// Handle level creation
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['level_name']);
    if($name!==''){
        $stmt = mysqli_prepare($link,'INSERT INTO user_levels (name) VALUES (?)');
        if($stmt){
            mysqli_stmt_bind_param($stmt,'s',$name);
            if(mysqli_stmt_execute($stmt)){
                $level_id = mysqli_insert_id($link);
                foreach($pages as $p){
                    foreach($modules as $m){
                        $v = isset($_POST['perm'][$p][$m]['view'])?1:0;
                        $e = isset($_POST['perm'][$p][$m]['edit'])?1:0;
                        if($v || $e){
                            $ins = mysqli_prepare($link,'INSERT INTO user_level_permissions (level_id,page,module,can_view,can_edit) VALUES (?,?,?,?,?)');
                            if($ins){
                                mysqli_stmt_bind_param($ins,'issii',$level_id,$p,$m,$v,$e);
                                mysqli_stmt_execute($ins);
                                mysqli_stmt_close($ins);
                            }
                        }
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    header('location: manage_levels.php');
    exit;
}

// Fetch existing levels
$levels = [];
$res = mysqli_query($link, "SELECT id, name FROM user_levels ORDER BY name");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $levels[] = $row;
    }
    mysqli_free_result($res);
}

include 'header.php';
?>
<h2>Manage User Levels</h2>
<table class="table mb-4">
  <thead><tr><th>ID</th><th>Name</th></tr></thead>
  <tbody>
<?php foreach($levels as $l): ?>
<tr><td><?php echo $l['id']; ?></td><td><?php echo htmlspecialchars($l['name']); ?></td></tr>
<?php endforeach; ?>
  </tbody>
</table>
<h3>Create Level</h3>
<form method="post">
  <div class="mb-3">
    <label class="form-label">Level Name</label>
    <input type="text" name="level_name" class="form-control" required>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>Page</th>
<?php foreach($modules as $m): ?>
        <th><?php echo htmlspecialchars($m); ?></th>
<?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
<?php foreach($pages as $p): ?>
      <tr>
        <td><?php echo htmlspecialchars($p); ?></td>
<?php foreach($modules as $m): ?>
        <td>
          <div class="form-check">
            <label class="form-check-label me-2">
              <input type="checkbox" class="form-check-input" name="perm[<?php echo $p; ?>][<?php echo $m; ?>][view]"> V
            </label>
            <label class="form-check-label">
              <input type="checkbox" class="form-check-input" name="perm[<?php echo $p; ?>][<?php echo $m; ?>][edit]"> E
            </label>
          </div>
        </td>
<?php endforeach; ?>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
  <button type="submit" class="btn btn-primary">Create Level</button>
</form>
<?php include 'footer.php'; ?>
