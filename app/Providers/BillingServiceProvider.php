<?php
namespace Phpfw\App\Providers;

use Billing\Domain\Repository\CustomerRepositoryInterface;
use Billing\Domain\Repository\OrderRepositoryInterface;
use Billing\Domain\Repository\InvoiceRepositoryInterface;
use Billing\Persistence\CustomerRepository;
use Billing\Persistence\OrderRepository;
use Billing\Persistence\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;

class BillingServiceProvider extends AppServiceProvider
{
	protected $defer = false;
	
	public function register()
	{
		$this->registerCustomerRepository();
		$this->registerOrderRepository();
		$this->registerInvoiceRepository();
	}
	
	private function registerCustomerRepository()
	{
		$this->app->singleton(CustomerRepositoryInterface::class, function ($app) {
			return new CustomerRepository($app[EntityManagerInterface::class]);
		});
	}

	private function registerOrderRepository()
	{
		$this->app->singleton(OrderRepositoryInterface::class, function ($app) {
			return new OrderRepository($app[EntityManagerInterface::class]);
		});
	}

	private function registerInvoiceRepository()
	{
		$this->app->singleton(InvoiceRepositoryInterface::class, function ($app) {
			return new InvoiceRepository($app[EntityManagerInterface::class]);
		});
	}
}