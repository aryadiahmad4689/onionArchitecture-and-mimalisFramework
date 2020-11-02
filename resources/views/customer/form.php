<?php include __DIR__."/../layout/header.html"; ?>

<div class="container">
    <div class="page-header clearfix">
        <h2><?= !empty($customer->getId()) ? 'Edit' : 'New' ?> Customer</h2>
    </div>
    
    <form role="form" action="<?= !empty($id = $customer->getId()) ? "/customers/update/{$id}": '/customers/create' ?>" method="post">
		<?php if (!empty($customer->getId())): ?>
        <div class="form-group">
            <label for="id">Id</label>
	   		<input type="text" class="form-control" name="id" id="id" readonly value="<?= $customer->getId() ?>">
	   </div>
	   <?php endif; ?>
	   
        <div class="form-group">
            <label for="name">Name:</label>
	   		<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?= $customer->getName() ?>">
	   </div>
	   <div class="form-group">
	       <label for="email">Email:</label>
	       <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?= $customer->getEmail() ?>">
	   </div>
	   <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<?php include __DIR__."/../layout/footer.html"; ?>