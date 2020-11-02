<?php
namespace Phpfw\Component\Foundation;

class ProviderRepository
{
	protected $manifestPath;
	
	public function __construct($manifestPath)
	{
		$this->manifestPath = $manifestPath;
	}
	
	public function load(Application $app,  array $providers)
	{
		$manifest = $this->loadManifest();
		
		if ($this->shouldRecompile($manifest, $providers)) {
			$manifest = $this->compileManifest($app, $providers);
		}
		
		if (php_sapi_name() === 'cli') {
			$manifest['eager'] = $manifest['providers'];
		}
		
		foreach ($manifest['when'] as $provider => $events) {
			$this->registerLoadEvents($app, $provider, $events);
		}
		
		foreach ($manifest['eager'] as $provider) {
			$app->register($this->createProvider($app, $provider));
		}

//		$app->setDeferredServices($manifest['deferred']);
	}
	
	protected function registerLoadEvents(Application $app, $provider, array $events)
	{
		if (count($events) < 1) {
			return;
		}
		
		$app->make('event')->listen($events, function() use ($app, $provider) {
			$app->register($provider);
		});
	}
	
	protected function compileManifest(Application $app,  $providers)
	{
		// The service manifest should contain a list of all of the providers for
		// the application so we can compare it on each request to the service
		// and determine if the manifest should be recompiled or is current.
		$manifest = $this->freshManifest($providers);

		foreach ($providers as $provider)
		{
			$instance = $this->createProvider($app, $provider);

			// When recompiling the service manifest, we will spin through each of the
			// providers and check if it's a deferred provider or not. If so we'll
			// add it's provided services to the manifest and note the provider.
			if ($instance->isDeferred()) {
				foreach ($instance->provides() as $service) {
					$manifest['deferred'][$service] = $provider;
				}

				$manifest['when'][$provider] = $instance->when();
			}

			// If the service providers are not deferred, we will simply add it to an
			// of eagerly loaded providers that will be registered with the app on
			// each request to the applications instead of being lazy loaded in.
			else {
				$manifest['eager'][] = $provider;
			}
		}

		return $this->writeManifest($manifest);
	}
	
	public function createProvider(Application $app,  $provider)
	{
		return new $provider($app);
	}
	
	public function shouldRecompile($manifest,  $providers)
	{
		return is_null($manifest) || ($manifest['providers'] != $providers);
	}
	
	public function loadManifest()
	{
		if (file_exists($this->manifestPath)) {
			$manifest = require($this->manifestPath);
			
			return array_merge(array('when' => array()), $manifest);
		}
	}
	
	public function writeManifest($manifest)
	{
		$content = "<?php\n\nreturn " .var_export($manifest, true) .";\n";

		file_put_contents($this->manifestPath, $content);

		return array_merge(array('when' => array()), $manifest);
	}
	
	protected function freshManifest(array $providers)
	{
		list($eager, $deferred) = array(array(), array());

		return compact('providers', 'eager', 'deferred');
	}
}