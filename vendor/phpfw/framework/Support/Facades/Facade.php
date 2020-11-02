<?php
namespace Phpfw\Component\Support\Facades;

use RuntimeException;

abstract class Facade
{
	protected static $app;

	protected static $resolvedFacadeInstance;

	protected static $aliases = array();
	
	public static function getFacadeRootAccess()
	{
		return static::resolveInstance(static::getFacadeAccesor());
	}
	
	public static function resolveInstance( $name )
	{
		//when $name variable is object
		if (is_object( $name )) {
			return $name;
		}

		//when facade instance have been sent
		if (isset(static::$resolvedFacadeInstance[ $name ])) {
			return static::$resolvedFacadeInstance[ $name ];
		}

		//resolve facade instance
		static::$resolvedFacadeInstance[ $name ] =	static::$app->make(static::getConcreteFacade( $name ));

		return static::$resolvedFacadeInstance[$name];
	}
	
	public static function getConcreteFacade( $app )
	{
		return static::getAliases( $app );
	}
	
	public static function getAliases( $name )
	{
		$aliases = static::$app['config']['application']['aliases'];
		//mapping facade aliases
		array_map (function( $facadeKey,  $facadeValue ) {
			//make new fresh facade aliases
			static::$aliases[strtolower( $facadeKey )] = $facadeValue;

		},  array_keys($aliases),  array_values($aliases));

		return ( 
			is_array( static::$aliases ) && 
			array_key_exists( $name,  static::$aliases ))? 
			static::$aliases[ $name ] : 
			false;
	}
	
	public static function clearResolvedInstance( $name )
	{
		unset(static::$resolvedFacadeInstance[$name]);
	}
	
	public static function clearResolvedInstances()
	{
		static::$resolvedFacadeInstance = [];
	}
	
	public static function setFacadeApplication( $app )
	{
		static::$app = $app;
	}
	
	public static function getFacadeApplication()
	{
		return static::$app;
	}
	
	public static function getFacadeAccesor()
	{
		Throw new RuntimeException("Facade doesn't implement getFacadeAccesor");
	}
	
	public static function __callStatic( $method,  $args )
	{
		$instance = static::getFacadeRootAccess();

		if (! $instance) {
			throw new RuntimeException('A facade root has not been set.');
		}

		switch (count($args)) {
			case 0:
				return $instance->$method();
			case 1:
				return $instance->$method($args[0]);
			case 2:
				return $instance->$method($args[0], $args[1]);
			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);
			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);
			default:
				return call_user_func_array([$instance, $method], $args);
		}
	}
}