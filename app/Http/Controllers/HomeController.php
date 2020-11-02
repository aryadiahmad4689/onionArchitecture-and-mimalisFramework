<?php
namespace Phpfw\App\Http\Controllers;

use Phpfw\Component\Support\Facades\View;

class HomeController extends Controller
{
	public function index($name, $old)
	{
		echo "You're in Home Controller ".$name.
		" .".$old.", Is it your old?";
	}

	public function html($name, $old)
	{
		return View::make('index')->with(array('name', 'old'), array($name,$old));
	}
}