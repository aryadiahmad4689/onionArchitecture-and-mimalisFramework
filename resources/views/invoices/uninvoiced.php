<?php include __DIR__."/../layout/header.html"; ?>

<h2>Generate New Invoices</h2>
	<p>The following orders are available to be invoiced.</p>
	<?php if (empty($orders)): ?>
	<p class="alert alert-info">There are no orders available for invoice.</p>
	<?php else: ?>
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
			<td class="text-right">$ <?= number_format($order->getTotal(), 2) ?></td>
		</tr>
		<?php endforeach; ?>
		</table>
		
		<form action="/invoices/generate" method="post" class="text-right">
			<button type="submit" class="btn btn-primary">Generate Invoices</button>
		</form>
		<?php endif; ?>
		
<?php include __DIR__."/../layout/footer.html"; ?>