<?php include __DIR__."/../layout/header.html"; ?>

<div class="page-header clearfix">
	<h2 class="pull-left">Orders</h2>
	<a href="/orders/purchase" class="btn btn-success pull-right">
		Create Order
	</a>
</div>

<table class="table table-striped clearfix">
	<thead>
		<tr>
			<th>#</th>
			<th>Order Number</th>
			<th>Customer</th>
			<th>Description</th>
			<th class="text-right">Total</th>
		</tr>
	</thead>
	<?php foreach ($orders as $order): ?>
	<tr>
		<td><a href="/orders/view/<?= $order->getId() ?>"><?= $order->getId() ?></a></td>
		<td><?= $order->getOrderNumber() ?></td>
		<td><a href="/customers/update/<?= $order->getCustomer()->getId() ?>"><?= $order->getCustomer()->getName() ?></a></td>
		<td><?= $order->getDescription() ?></td>
		<td class="text-right">IDR <?= number_format($order->getTotal(), 2) ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php include __DIR__."/../layout/footer.html"; ?>