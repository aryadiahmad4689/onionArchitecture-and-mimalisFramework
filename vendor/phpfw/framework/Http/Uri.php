<?php
namespace Phpfw\Component\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
	/**
	 * URI string
	 * @var string $uri
	 */
	protected $uri;

	/**
	 * URI Components
	 * @var array $uriComponents
	 */
	protected $uriComponents = array();

	/**
	 * Query parameter
	 * @var array $queryParams
	 */
	protected $queryParams = array();

	/**
	 * HTTP Standard ports
	 * @var array
	 */
	const STANDARD_PORTS = array(
		'ftp' => 21, 
		'tls' => 25, 
		'ssh' => 22, 
		'http'=> 80, 
		'https'=> 443
	);

	/**
	 * @param string $uri
	 */
	public function __construct($uri = null)
	{
		$this->uriComponents = parse_url($uri);
		
		if (!$this->uriComponents) {
			throw new InvalidArgumentException(
				"URI [$uri] Invalid!"
			);
		}

 		$this->uri = $uri;
 	}
 
 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getScheme()
	 */
	public function getScheme()
	{
		if (isset($this->uriComponents['scheme'])) {
			return strtolower($this->uriComponents['scheme']) ?? '';
		}
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getAuthority()
	 */
	public function getAuthority()
	{
		$authority = '';

		if (!empty($this->getUserInfo())) {
			$authority .= $this->getUserInfo() . '@';
		}
		
		$authority .= $this->uriComponents['host'] ?? '';
		
		if ($this->getPort() !== null) {
			$authority .= ':' . $this->getPort();
		}
		
		return $authority;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getUserInfo()
	 */
	public function getUserInfo()
	{
		if (!isset($this->uriComponents['user'])) {
			return null;
	 	}

		$userInfo = $this->uriComponents['user'];

	 	if (isset($this->uriComponents['pass'])) {
	 		$userInfo .= ':' . $this->uriComponents['pass'];
		}

	 	return $userInfo;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getHost()
	 */
	public function getHost()
	{
		if (!isset($this->uriComponents['host'])) {
			return null;
 		}
 		
		return strtolower($this->uriComponents['host']);
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getPort()
	 */
	public function getPort()
	{
		if (!isset($this->uriComponents['port'])) {
			return null;
	 	} else {
			if ($this->getScheme()) {
				if ($this->uriComponents['port'] === Uri::STANDARD_PORTS[$this->getScheme()]) {
					return null;
				}
			}
			
		 	return (int) $this->uriComponents['port'];
		}
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getPath()
	 */
	public function getPath()
	{
	 	if (!isset($this->uriComponents['path'])) {
			return null;
	 	}

		$path = implode('/', array_map(
			"rawurlencode",  
			explode('/', $this->uriComponents['path']))
		);
		
		return $path;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getQueryParams()
	 */
	public function getQueryParams($reset = false)
	{
		 if ($this->queryParams && !$reset) {
		 	return $this->queryParams;
		 }

		 $this->queryParams = [];

		 if (isset($this->uriComponents['query'])) {
			 if (is_string($this->uriComponents['query'])) {
				foreach (explode('&', $this->uriComponents['query']) as $keyPair) {
					list($param, $value) = explode('=', $keyPair);
					$this->queryParams[$param] = $value;
				 }
			 }

			 if (is_array($this->uriComponents['query'])) {
				foreach ($this->uriComponents['query'] as $param => $value) {
					$this->queryParams[$param] = $value;
				 }
			 }
		}

		 return $this->queryParams;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getQuery()
	 */
	public function getQuery()
	{
		if (!$this->getQueryParams()) {
			return null;
 		}

		$output = '';

		foreach ($this->getQueryParams() as $key => $value) {
			 $output .= rawurlencode($key) . '='  . rawurlencode($value) . '&';
 		}
 		
 		return substr($output, 0, -1);
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getFragment()
	 */
	public function getFragment()
	{
		if (!isset($this->uriComponents['fragment'])) {
			return null;
 		}

		return rawurlencode($this->uriComponents['fragment']);
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::withScheme()
	 */
	public function withScheme($scheme)
	{
		if (empty($scheme) && $this->getScheme()) {
			unset($this->uriComponents['scheme']);
		} else {
			if (isset(Uri::STANDARD_PORTS[strtolower($scheme)])) {
				$this->uriComponents['scheme'] = $scheme;
			} else {
				$ex = "[$scheme] scheme not available";
				throw new InvalidArgumentException($ex);
			}
		}

		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getUserInfo()
	 */
	public function withUserInfo($user, $password = null)
	{
		if (empty($user) && $this->getUserInfo()) {
 
 			unset($this->uriComponents['user']);
 
 		} else {
			$this->uriComponents['user'] = $user;

			if ($password) {
				$this->uriComponents['pass'] = $password;
 			}
		}

		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::withHost()
	 */
	public function withHost($host)
	{
		if (empty($host) && $this->getHost() !== null) {
 			unset($this->uriComponents['host']);
 		} else {
			 $this->uriComponents['host'] = $host;
		}
		
		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getPort()
	 */
	public function withPort($port)
	{
		if (empty($port) && $this->getPort() !== null) {
 			unset($this->uriComponents['port']);
 		} else {
			 $this->uriComponents['port'] = $port;
		}
		
		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getPath()
	 */
	public function withPath($path)
	{
		if (empty($path) && $this->getPath()) {
 			unset($this->uriComponents['path']);
 		} else {
			 $this->uriComponents['path'] = $path;
		}

		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::getFragment()
	 */
	public function withFragment($fragment)
	{
		if (empty($fragment) && $this->getFragment()) {
 			unset($this->uriComponents['fragment']);
 		} else {
			 $this->uriComponents['fragment'] = $fragment;
		}

		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::withQuery()
	 */
	public function withQuery($query)
	{
		if (empty($query) && $this->getQuery()) {
			unset($this->uriComponents['query']);
		} else {
			$this->uriComponents['query'] = $query;
 		}

	 	//Reset query params array
		$this->getQueryParams(true);

		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UriInterface::__toString()
	 */
	public function __toString()
	{
		$uri = ($this->getScheme()) ? $this->getScheme() . '://' : '';
		
		if ($this->getAuthority()) {
 			$uri .= $this->getAuthority();
		} else {
 			$uri .= ($this->getHost()) ? $this->getHost() : '';
 			$uri .= ($this->getPort()) ? ':' . $this->getPort() : '';
		}
		
		$path = $this->getPath();
		
		if ($path) {
 			if ($path[0] != '/') {
 				$uri .= '/' . $path;
 				} else {
 					$uri .= $path;
 			}
		}
		
		$uri .= ($this->getQuery()) ? '?' . $this->getQuery() : '';
		$uri .= ($this->getFragment()) ? '#' . $this->getFragment() : '';

		return $uri;
	}

	/**
	 * Get parsed uri string
	 * 
	 * @return string
	 */
	public function getUriString()
	{
		return $this->__toString();
	}
}