<?php

return array (
  'providers' => 
  array (
    0 => 'Phpfw\\App\\Providers\\AppServiceProvider',
    1 => 'Phpfw\\App\\Providers\\PsudoServiceProvider',
    2 => 'Phpfw\\App\\Providers\\LazyPsudoServiceProvider',
    3 => 'Billing\\App\\DoctrineGateway\\DoctrineGatewayServiceProvider',
    4 => 'Phpfw\\App\\Providers\\BillingServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Phpfw\\App\\Providers\\AppServiceProvider',
    1 => 'Phpfw\\App\\Providers\\PsudoServiceProvider',
    2 => 'Billing\\App\\DoctrineGateway\\DoctrineGatewayServiceProvider',
    3 => 'Phpfw\\App\\Providers\\BillingServiceProvider',
  ),
  'deferred' => 
  array (
    'lazy.psudo' => 'Phpfw\\App\\Providers\\LazyPsudoServiceProvider',
  ),
  'when' => 
  array (
    'Phpfw\\App\\Providers\\LazyPsudoServiceProvider' => 
    array (
      0 => 'ProviderHook',
    ),
  ),
);
