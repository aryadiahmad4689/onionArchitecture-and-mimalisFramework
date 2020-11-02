<?php
namespace Phpfw\Component\Config;

use Phpfw\Component\Contract\Loader\LoaderInterface;

class Repository implements \ArrayAccess
{
    protected $loader;

    protected $items = array();
	
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }
	
    public function has($key)
    {
        $default = microtime(true);

        return $this->get($key, $default) !== $default;
    }
	
    public function get($key, $default = false)
    {
        @list($group, $item) = $this->parseKey($key);
        
        $this->load($group);

        if (empty($item)) {
            return $this->items[$group];
        }

        return array_get($this->items[$group], $item, $default);
    }
	
    public function set($key, $value)
    {
        @list($group, $item) = $this->parseKey($key);
        
        $this->load($group);

        if (empty($item)) {
            $this->items[$group] = $value;
        } else {
            array_set($this->items[$group], $item, $value);
        }

        $this->loader->set($key, $value);
    }
	
    public function load($group)
    {
        if (isset($this->items[$group])) return;
 
        $this->items[$group] = $this->loader->load($group);
    }
	
    public function parseKey($key)
    {
		if (strpos($key, '::' )=== false) {
			return $this->parseSegments($key);		
		}
    }
	
	public function parseSegments($key)
	{
        $segments = explode('.', $key);

        $group = $segments[0];

        unset($segments[0]);

        $segments = implode('.', $segments);

        return array($group, $segments);
	}
	
    public function getItems()
    {
        return $this->items;
    }
	
    public function getLoader()
    {
        return $this->loader;
    }
	
    public function offsetExists($key)
    {
        return $this->has($key);
    }
	
    public function offsetGet($key)
    {
        return $this->get($key);
    }
	
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }
	
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}