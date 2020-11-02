<?php

if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_set')) {
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('str_contains')) {
    function str_contains($haystack,  $needles) {
        foreach((array) $needles as $needle) {
            if(($needle !== '') && (mb_strpos($haystack,  $needle) !== false)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('str_parse_callback')) {
    function str_parse_callback($callback, $default) {
        if (str_contains($callback,  '@')) {
        	return explode('@',  $callback,  2);
        }
        
        return array($callback,  $default);
    }
}