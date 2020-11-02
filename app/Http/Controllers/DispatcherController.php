<?php
namespace Phpfw\App\Http\Controllers;

use Phpfw\Component\Event\Dispatcher;

class DispatcherController extends Controller
{
	private $dispatcher;
	
	public function __construct(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
		$this->dispatcher->listen(
			'MemberWasRegistered', 
			'\Phpfw\App\Events\WelcomeNewlyRegisteredMemberListener'
		);
	}
	
	public function register(int $uuid, $member)
	{
		$storage = ROOT_PATH . '/app/Cache/Dispatchers.php';

		$members = require_once $storage;
		$members = is_array($members)?$members:array();

		if (array_key_exists($uuid, $members)) {
			echo "Member with username ". $member. " already exist.";
			return;
		}

		$members = $members+array($uuid => $member);
		$members = "<?php\n\nreturn " .var_export($members, true) .";\n";
		
		file_put_contents($storage, $members);
		
		$welcome = $this->dispatcher->fire(
			'Phpfw\App\Events\MemberWasRegistered', 
			array('member' => $member)
		);
		
		echo $welcome;
//		echo $this->app['lazy.psudo'];
		echo $this->providerHook();
	}
	
	public function providerHook()
	{
		$this->app['event']->fire(
			'\Phpfw\App\Events\ProviderHook'
		);
		return $this->app['lazy.psudo'];
	}

}