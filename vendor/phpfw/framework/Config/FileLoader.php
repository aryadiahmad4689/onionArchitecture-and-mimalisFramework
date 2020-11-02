<?php
namespace Phpfw\Component\Config;

use Phpfw\Component\Contract\Loader\LoaderInterface;

class FileLoader implements LoaderInterface
{
    function __construct()
    {
    }
    
    public function load($group)
    {
        return Config::get($group, array());
    }
    
    public function set($key, $value)
    {
        Config::set($key, $value);
    }
}