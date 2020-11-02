<?php
namespace Phpfw\App\Events;

use Phpfw\Component\Contract\Event\Event;

class MemberWasRegistered implements Event
{
	private $member;
	
	public function __construct($member)
	{
		$this->member = $member;
	}
	
	public function getMember()
	{
		return $this->member;
	}
	
	public function getName()
	{
		return 'MemberWasRegistered';
	}
}