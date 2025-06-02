<?php
require_once 'auth.php';
require_permission('page_b');
$canEdit = has_permission('page_b', 'edit');
include 'header.php';
?>
<h1>Page B</h1>
<p>You have access to view this page.</p>
<?php if($canEdit): ?>
<form class="mt-3">
    <div class="mb-3">
        <label class="form-label">Edit Content</label>
        <textarea class="form-control" rows="5"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
<?php endif; ?>
<?php include 'footer.php'; ?>
