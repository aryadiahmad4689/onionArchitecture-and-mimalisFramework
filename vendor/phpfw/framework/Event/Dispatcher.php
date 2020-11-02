<?php
namespace Phpfw\Component\Event;

use Phpfw\Component\Contract\Container\ContainerInterface;
use Phpfw\Component\Contract\Event\{Event, Listener};

class Dispatcher
{
	private $listeners = array();
	
	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
	}
	
	public function listen($event, $listener)
	{
		$events = (array) $event;;
		
		foreach ($events as $event) {
			$this->listeners[$event][] = $listener;
		}
	}
	
    public function hasListeners($event)
    {
        return isset($this->listeners[$event]);
    }

	public function getListeners($event)
	{
		if (!$this->hasListeners($event)) {
			return [];
		}

		return array_map(function($listener) {
			if ($listener instanceof \Closure) {
				return $listener;
			}
			
			return $this->app->make($listener);
		}, $this->listeners[$event]);
	}
	
	public function fire($events, $payloads = array())
	{
		if (is_array($events)) {
			return $this->fireEvents($events, $payloads);
		}
		
		return $this->fireEvent($events, $payloads);
	}
	
	private function fireEvents(array $events, $payloads = array())
	{
		foreach ($events as $event) {
			$this->fireEvent($event, $payloads);
		}
	}
	
    private function fireEvent($event, $payloads = array())
    {
        if (is_string($event)) {
            $event = $this->app->make($event, $payloads);
        }
        
        if (!$event instanceof Event) {
            $implement = Event::class;
            throw new \Exception("Event must be instance of [{$implement}]");
        }

		foreach ($this->getListeners($event->getName()) as $listener) {

			if ($listener instanceof \Closure) {
				return $listener();
			}
			
			if(!$listener instanceof Listener) {
				$implement = Listener::class;
				throw new \Exception("Listener must be instance of [{$implement}]");
			}
			
			return $listener->handle($event);
		}
    }
}
