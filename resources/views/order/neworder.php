<?php include __DIR__."/../layout/header.html"; ?>

<div class="page-header clearfix">
	<h2>Create Order</h2>
</div>

<form role="form" action="" method="post">
	<div class="form-group">
		<label for="customer_id">Customer:</label>
		<select class="form-control" name="customer_id" id="customer_id">
    		<option value=""></option>
    		<?php foreach ($customers as $customer): ?>
    		<option value="<?= $customer->getId() ?>">
    			<?= $customer->getName()?>
    		</option>
    		<?php endforeach; ?>
		</select>
	</div>

    <div class="form-group">
    	<label for="orderNumber">Order Number:</label>
    	<input type="text" class="form-control" name="order_number" id="order_number" placeholder="Enter Order Number">
    </div>

    <div class="form-group">
    	<label for="description">Description:</label>
    	<input type="text" class="form-control" name="description" id="description" placeholder="Enter Description">
    </div>

    <div class="form-group">
    	<label for="total">Total:</label>
    	<input type="text" class="form-control" name="total" id="total" placeholder="Enter Total">
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
</form>

<?php include __DIR__."/../layout/footer.html"; ?>