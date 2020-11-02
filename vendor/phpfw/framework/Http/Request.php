<?php
namespace Phpfw\Component\Http;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{	
	/**
	 * HTTP Method lists
	 * @var array
	 */
	const HTTP_METHODS = array(
		'get', 
		'put', 
		'post', 
		'delete', 
		'patch', 
		'head', 
		'options'
	);
	
	 /** The Header Host **/
	const HEADER_HOST = 'Host';

	 /** The default request target path **/
	const DEFAULT_REQUEST_TARGET = '/';
	
	/**
	 * Request target
	 * @var string $target
	 */
	 protected $target;

	/**
	 * UriInterface instance
	 * @var \psr\Http\Message\UriInterface $uri
	 */
	protected $uri;

	/**
	 * HTTP Method
	 * 
	 * @var string $method
	 */
	protected $method;
	
	/**
	 * @param string $target Request target
	 * @param string $method HTTP method request
	 * @param \Psr\Http\Message\STreamINterface $body HTTP body message
	 * @param array $headers Http headers list
	 * @param string $targetHTTP protocol version
	 */
	public function __construct(
		$target = null, 
		$method = null, 
		StreamInterface $body = null, 
		$headers = null, 
		$version = null)
	{
		$this->target = $target;
		$this->body = $body;
		$this->method = $this->checkMethod($method);
		$this->httpHeaders = $headers;
		$this->version = $this->onlyVersion($version);
	}
	
	/**
	 * Check HTTP method validity
	 * 
	 * @param string $method
	 * 
	 * @throw InvalidArgumentException
	 * 
	 * @return string
	 */
	protected function checkMethod($method)
	{
		if (!$method === null) {
			if (!in_array(strtolower($method), Request::HTTP_METHODS)) {
				throw new InvalidArgumentException(
					"HTTP Method [$method] Not Allowed!"
				);
			}
		}

		return $method;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::getRequestTarget()
	 */
	public function getRequestTarget()
	{
		return $this->target ?? Request::DEFAULT_REQUEST_TARGET;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::withRequestTarget()
	 */
	public function withRequestTarget($requestTarget)
	{
		$this->target = $requestTarget;
		$this->getUri();
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::getMethod()
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::withMethod()
	 */
	public function withMethod($method)
	{
		$this->method = $this->checkMethod($method);
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::getUri()
	 */
	public function getUri()
	{
		if (!$this->uri) {
			$this->uri = new Uri($this->target);
		}

		return $this->uri;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::withUri()
	 */
	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		if ($preserveHost) {
			$found = $this->findHeader(Request::HEADER_HOST);

			if (!$found && $uri->getHost()) {
				$this->httpHeaders[Request::HEADER_HOST] = $uri->getHost();
			}
		} elseif ($uri->getHost()) {
			$this->httpHeaders[Request::HEADER_HOST] = $uri->getHost();
		}
		
		$this->uri = $uri;
		return $this;
	}
}