<?php
namespace Phpfw\Component\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

class Message implements MessageInterface
{
	 /** The default body stream used for write and read stream **/
	const DEFAULT_BODY_STREAM = 'php://input';
	
	/**
	 * The body message container
	 * @var string $body
	 */
	protected $body;

	/**
	 * The http protocoll version
	 * @var string $version
	 */
	protected $version;

	/**
	 * Http headers container
	 * @var array $httpHeaders
	 */
	protected $httpHeaders = array();
	
	/**
	 * Determine if the request has body message
	 *
	 * @return bool
	 */
	public function hasBody()
	{
		if (!$this->body || $this->body === null) {
			return false;
		}
		
		return true;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::getBody()
	 */
	public function getBody()
	{
		if (!$this->body) {
			$this->body = new Stream(Message::DEFAULT_BODY_STREAM);
		}
		
		return $this->body;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::withBody()
	 */
	public function withBody(StreamInterface $body)
	{
		if (!$body->isReadable()) {
			throw new InvalidArgumentException(
				"Body Message Unreadable!"
			);
		}

		$this->body = $body;
		return $this;
	}

	/**
	 * Find the header by the given name
	 *  
	 * @return bool|string
	 */
	protected function findHeader($name)
	{
		$found = false;
		foreach (array_keys($this->getHeaders()) as $header) {
			if (stripos($header, $name) !== false) {
				$found = $header;
				break;
			}
		}

		return $found;
	}

	/**
	 * Get Http Headers
	 * 
	 * @return array
	 */
	protected function getHttpHeaders()
	{
		//header already sent
		if (!$this->httpHeaders) {
			if (function_exists('apache_request_headers')) {
				$this->httpHeaders = apache_request_headers();
			} else {
				$this->httpHeaders = $this->altApacheReqHeaders();
			}
		}

		return $this->httpHeaders;
	}

	/**
	 * Replace apc_request_headers functionality if not exist
	 *  
	 * @return array The list of http headers
	 */
	protected function altApacheReqHeaders()
	{
		$headers = array();
		
		foreach ($_SERVER as $key => $value) {
			if (stripos($key, 'HTTP_') !== false) {
				$headerKey = str_ireplace('HTTP_', '', $key);
				$headers[$this->explodeHeader($headerKey)] = $value;
			} elseif (stripos($key, 'CONTENT_') !== false) {
				$headers[$this->explodeHeader($key)] = $value;
			}
		}

		return $headers;
	}

	/**
	 * Remove underscore character with dash caharacter from the given header
	 *  
	 * @return array The list of http headers
	 */
	protected function explodeHeader($header)
	{
		$headerParts = explode('_', strtolower($header));
		$headerKey = ucwords(implode(' ', $headerParts));

		return str_replace(' ', '-', $headerKey);
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::getHeaders()
	 */
	public function getHeaders()
	{
		foreach ($this->getHttpHeaders() as $key => $value) {
			header(sprintf('%s: %s', $key, $value));
		}
		
		return $this->getHttpHeaders();
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::withHeader()
	 */
	public function withHeader($name, $value)
	{
		$found = $this->findHeader($name);

		if ($found) {
			$this->httpHeaders[$found] = $value;
		} else {
			$this->httpHeaders[$name] = $value;
		}
		
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::withAddedHeader()
	 */
	public function withAddedHeader($name, $value)
	{
		$found = $this->findHeader($name);
		if ($found) {
			$this->httpHeaders[$found] .= $value;
		} else {
			$this->httpHeaders[$name] = $value;
		}
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::withoutHeader()
	 */
	public function withoutHeader($name)
	{
		$found = $this->findHeader($name);
		if ($found) {
			unset($this->httpHeaders[$found]);
		}
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::hasHeader()
	 */	
	public function hasHeader($name)
	{
		return boolval($this->findHeader($name));
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::getHeaderLine()
	 */
	public function getHeaderLine($name)
	{
		$found = $this->findHeader($name);
		if ($found) {
			return $this->httpHeaders[$found];
		} else {
			return '';
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::withHeader()
	 */
	public function getHeader($name)
	{
		$line = $this->getHeaderLine($name);
		if ($line) {
			return explode(',', $line);
		} else {
			return array();
		}
	}

	/**
	 * Get captured headers as string
	 *  
	 * @return string The raw header
	 */
	public function getHeadersAsString()
	{
		$output = '';
		$headers = $this->getHeaders();
		if ($headers && is_array($headers)) {
			foreach ($headers as $key => $value) {
				if ($output) {
					$output .= "\r\n" . $key . ': ' . $value;
				} else {
					$output .= $key . ': ' . $value;
				}
			}
		}
		return $output;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::getProtocolVersion()
	 */
	public function getProtocolVersion()
	{
		if (!$this->version) {
			$this->version = $this->onlyVersion(
				$_SERVER['SERVER_PROTOCOL']
			);
		}
		return $this->version;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\MessageInterface::withProtocolVersion()
	 */
	public function withProtocolVersion($version)
	{
		$this->version = $this->onlyVersion($version);
		return $this;
	}

	/**
	 * Validate that the givven protocol version only contains desired number and dot pattern
	 * 
	 * @param string $version
	 *  
	 * @return null|string
	 */
	protected function onlyVersion($version)
	{
		if (!empty($version)) {
			return preg_replace('/[^0-9\.]/', '', $version);
		} else {
			return null;
		}
	}
}