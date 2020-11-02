<?php
namespace Phpfw\Component\Config;

class Config
{
    protected static $settings = array();
    
    public static function all()
    {
        return static::$settings;
    }
    
    public static function has($key)
    {
        return ! is_null(array_get(static::$settings, $key));
    }
    
    public static function get($key, $default = null)
    {
        return array_get(static::$settings, $key, $default);
    }
    
    public static function set($key, $value)
    {
        array_set(static::$settings, $key, $value);
    }
}