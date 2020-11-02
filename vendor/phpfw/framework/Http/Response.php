<?php
namespace Phpfw\Component\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response extends Message implements ResponseInterface
{
	/** The default status code response **/
	const DEFAULT_STATUS_CODE = 200;

	/**
	 * HTTP status codes
	 * @var array $statusCodes
	 */
	const STATUS_CODES = array(
		100 => 'Continue',  
		101 => 'Switching Protocols', 
		200 => 'OK', 
		201 => 'Created', 
		202 => 'Accepted',  
		203 => 'Non-Authoritative Information', 
		204 => 'No Content', 
		205 => 'Reset Content',  
		206 => 'Partial Content', 
		300 => 'Multiple Choices', 
		301 => 'Moved Permanently', 
		302 => 'Moved Temporarily',  
		303 => 'See Other', 
		304 => 'Not Modified', 
		305 => 'Use Proxy', 
		400 => 'Bad Request', 
		401 => 'Unauthorized',  
		402 => 'Payment Required', 
		403 => 'Forbidden', 
		404 => 'Not Found', 
		405 => 'Method Not Allowed',  
		406 => 'Not Acceptable', 
		407 => 'Proxy Authentication Required', 
		408 => 'Request Time-out',  
		409 => 'Conflict', 
		410 => 'Gone', 
		411 => 'Length Required', 
		412 => 'Precondition Failed',  
		413 => 'Request Entity Too Large', 
		414 => 'Request-URI Too Large', 
		415 => 'Unsupported Media Type',  
		500 => 'Internal Server Error', 
		501 => 'Not Implemented', 
		502 => 'Bad Gateway', 
		503 => 'Service Unavailable',  
		504 => 'Gateway Time-out', 
		505 => 'HTTP Version not supported', 
	);

	/**
	 * HTTP status code
	 * @var integer $statusCode
	 */
	protected $statusCode;

	/**
	 * @param int $statuscode
	 * @param \Psr\Http\Message\StreamInterface $body The body response
	 * @param array $headers Header response list
	 * @param float $version The http protocol version
	 */
	public function __construct(
		$statusCode = null,
		StreamInterface $body = null,
		$headers = array(),
		$version = null)
	{
		$this->body = $body;
		$this->status['code'] = $statusCode ?? self::DEFAULT_STATUS_CODE;
		$this->status['reason'] = self::STATUS_CODES[$statusCode] ?? '';
		$this->httpHeaders = $headers;
		$this->version = $this->onlyVersion($version);
		
		if ($statusCode) { 
			$this->setStatusCode();
		}
	}
	
	/**
	 * Set the response status code to the http response header.
	 *
	 * @return void
	 */
	public function setStatusCode()
	{
		http_response_code($this->getStatusCode());
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getStatusCode()
	 */
	public function getStatusCode()
	{
		return $this->status['code'];
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::withStatus()
	 */
	public function withStatus($statusCode, $reasonPhrase = '')
	{
		if (!isset(self::STATUS_CODES[$statusCode])) {
			throw new InvalidArgumentException(
				"Invalid Status Code ". $statusCode
			);
		}
		
		$this->status['code'] = $statusCode;
		$this->status['reason'] = ($reasonPhrase)? self::STATUS_CODES[$statusCode] : null;

		$this->setStatusCode();

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getReasonPhrase()
	 */
	public function getReasonPhrase()
	{
		return $this->status['reason']?? self::STATUS_CODES[$this->status['code']] ?? '';
	}
}