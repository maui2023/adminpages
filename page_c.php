<?php
require_once 'auth.php';
require_login();
include 'header.php';
?>
<h1>Page C</h1>
<?php if(has_permission('page_c','dashboard','view')): ?>
<h2>Dashboard</h2>
<p>Dashboard module content.</p>
<?php if(has_permission('page_c','dashboard','edit')): ?>
<form class="mb-4">
  <div class="mb-3">
    <label class="form-label">Edit Dashboard</label>
    <textarea class="form-control" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
</form>
<?php endif; ?>
<?php endif; ?>

<?php if(has_permission('page_c','application','view')): ?>
<h2>Application</h2>
<p>Application module content.</p>
<?php if(has_permission('page_c','application','edit')): ?>
<form class="mb-4">
  <div class="mb-3">
    <label class="form-label">Edit Application</label>
    <textarea class="form-control" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
</form>
<?php endif; ?>
<?php endif; ?>

<?php if(has_permission('page_c','letter','view')): ?>
<h2>Letter</h2>
<p>Letter module content.</p>
<?php if(has_permission('page_c','letter','edit')): ?>
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
