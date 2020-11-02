<?php
namespace Phpfw\App\Events;

use Phpfw\Component\Contract\Event\Event;

class ProviderHook implements Event
{
	public function getName()
	{
		return 'ProviderHook';
	}
}