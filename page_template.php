<?php
$page = '{{page}}';
require_once 'auth.php';
require_login();
include 'header.php';
?>
<h1><?php echo ucfirst(str_replace('_',' ', $page)); ?></h1>
<?php if(has_permission($page,'dashboard','view')): ?>
<h2>Dashboard</h2>
<p>Dashboard module content.</p>
<?php if(has_permission($page,'dashboard','edit')): ?>
<form class="mb-4">
  <div class="mb-3">
    <label class="form-label">Edit Dashboard</label>
    <textarea class="form-control" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
</form>
<?php endif; ?>
<?php endif; ?>

<?php if(has_permission($page,'application','view')): ?>
<h2>Application</h2>
<p>Application module content.</p>
<?php if(has_permission($page,'application','edit')): ?>
<form class="mb-4">
  <div class="mb-3">
    <label class="form-label">Edit Application</label>
    <textarea class="form-control" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
</form>
<?php endif; ?>
<?php endif; ?>

<?php if(has_permission($page,'letter','view')): ?>
<h2>Letter</h2>
<p>Letter module content.</p>
<?php if(has_permission($page,'letter','edit')): ?>
<form class="mb-4">
  <div class="mb-3">
    <label class="form-label">Edit Letter</label>
    <textarea class="form-control" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
</form>
<?php endif; ?>
<?php endif; ?>
<?php include 'footer.php'; ?>
