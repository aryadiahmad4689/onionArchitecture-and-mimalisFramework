<?php include __DIR__."/../layout/header.html"; ?>

<div class="container">
	<table class="table">
	   <thead>
	       <tr>
	           <th>#</th>
	           <th>Name</th>
	           <th>Email</th>
	       </tr>
	   </thead>
	   <?php foreach ($customers as $customer): ?>
	   <tr>
	       <td>
	           <a href="/customers/update/<?= $customer->getId() ?>"><?= $customer->getId() ?></a>
	       </td>
	       <td><?= $customer->getName() ?></td>
	       <td><?= $customer->getEmail() ?></td>
	   </tr>
	   <?php endforeach; ?>
	</table>
</div>

<?php include __DIR__."/../layout/footer.html"; ?>