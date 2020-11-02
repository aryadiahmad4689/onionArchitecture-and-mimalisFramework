<?php
namespace Phpfw\Component\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
	 /** The Http POST Method **/
	const POST = 'post';
	
	 /** The Header Encoded Form **/
	const FORM_ENCODED =  'application/x-www-form-urlencoded';

	 /** The Header Multiple Form **/
	const MULTI_FORM = 'multipart/form-data';

	/** The mime type of json **/
	const CONTENT_TYPE_JSON = "application/json";

	/** The mime type of hal+json **/
	const CONTENT_TYPE_HAL_JSON = "application/hal+json";
	
	/**
	 * $_SERVER list
	 * @var array $serverParams
	 */
	protected $serverParams;

	/**
	 * $_COOKIE list
	 * @var array $cookies
	 */
	protected $cookies;

	/**
	 * Content-Type Header
	 * @var string $contentType
	 */
	protected $contentType;

	/**
	 * $_GET list
	 * @var array $queryParams
	 */
	protected $queryParams;

	/**
	 * Request message parsed body
	 * @var mixed $parsedBody
	 */
	protected $parsedBody;

	/**
	 * Additional attribute
	 * 
	 * @var array $attributes
	 */
	protected $attributes;

	/**
	 * Request Method
	 * @var string $method
	 */
	protected $method;

	/**
	 * $_FILES list
	 * @var array $uploadedFileInfo
	 */
	protected $uploadedFileInfo;

	/**
	 * UploadedFile instance
	 * @var \Psr\Request\Message\UploadedFileInterface $uploadedFile
	 */
	protected $uploadedFile;
	
	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getServerParams()
	 */
	public function getServerParams()
	{
		if (!$this->serverParams) {
			$this->serverParams = $_SERVER;
		}

		return $this->serverParams;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getCookieParams()
	 */
	public function getCookieParams()
	{
		if (!$this->cookies) {
			$this->cookies = $_COOKIE;
		}

		return $this->cookies;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getQueryParams()
	 */
	public function getQueryParams()
	{
		if (!$this->queryParams) {
			$this->queryParams = $_GET;
		}
		
		return $this->queryParams;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getUploadedFileInfo()
	 */
	public function getUploadedFileInfo()
	{
		if (!$this->serverParams) {
			$this->uploadedFileInfo = $_FILES;
		}
		
		return $this->uploadedFileInfo;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getRequestMethod()
	 */
	public function getRequestMethod()
	{
		$method = $this->getServerParams()['REQUEST_METHOD'] ?? '';
		$this->method = strtolower($method);
		
		return $this->method;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getContentType()
	 */
	public function getContentType()
	{
		if (!$this->contentType) {
			$this->contentType = $this->getServerParams()['CONTENT_TYPE'] ?? '';
			$this->contentType = strtolower($this->contentType);
		}

		return $this->contentType;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getUploadedFile()
	 */
	public function getUploadedFiles()
	{
		if (!is_null($this->uploadedFile)) {
			foreach ($this->getUploadedFileInfo() as $field => $value) {
				$this->uploadedFile[$field] = new UploadedFile($field, $value);
			}
		}
		return $this->uploadedFile;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withCookieParams()
	 */
	public function withCookieParams(array $cookies)
	{
		array_merge($this->getCookieParams(), $cookies);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withQueryParams()
	 */
	public function withQueryParams(array $query)
	{
		array_merge($this->getQueryParams(), $query);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withUploadedFile()
	 */
	public function withUploadedFiles(array $uploadedFiles)
	{
		if (!count($uploadedFiles)) {
			throw new InvalidArgumentException(
				"No Uploaded File was Added"
			);
		}
		
		foreach ($uploadedFiles as $uploadedFile) {
			if (!$uploadedFile instanceof UploadedFileInterface) {
				throw new InvalidArgumentException(
					"Uploaded File must be Instance of ".
					UploadedFileInterface::class
				);
			}
		}
		
		$this->uploadedFile = $uploadedFiles;
		
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getParsedBody()
	 */
	public function getParsedBody()
	{
		$contentType = $this->getContentType();
		$requestMethod = mb_strtoupper($this->getRequestMethod());

		$form = ((
			$contentType === self::FORM_ENCODED || 
			$contentType === self::MULTI_FORM) &&
			$requestMethod === mb_strtoupper(self::POST)
		);
		
		$json = (
			$contentType === self::CONTENT_TYPE_JSON ||
			$contentType === self::CONTENT_TYPE_HAL_JSON
		);

		switch (!$this->parsedBody) {
			case $form: $this->parsedBody = $_POST;
			break;
			case $json:
				$this->parsedBody = json_decode(file_get_contents('php://input'), true);
			break;
			case !empty($_REQUEST):
				$this->parsedBody = $_REQUEST;
			break;
			default:
				ini_set("allow_url_fopen", true);
				$this->parsedBody = file_get_contents('php://input');
			break;
		}

		return $this->parsedBody;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withParsedBody()
	 */
	public function withParsedBody($data)
	{
		$this->parsedBody = $data;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getAttributes()
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getAttribute()
	 */
	public function getAttribute($name, $default = null)
	{
		return $this->attributes[$name] ?? $default;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withAttribute()
	 */
	public function withAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withoutAttribute()
	 */
	public function withoutAttribute($name)
	{
		if (isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		}
		return $this;
	}

	/**
	 * Initialize Request parameter lists
	 * @return \Psr\Http\Message\ServerRequest
	 */
	public function initialize()
	{
		$this->getServerParams();
		$this->getCookieParams();
		$this->getQueryParams();
		$this->getUploadedFiles();
		$this->getRequestMethod();
		$this->getContentType();
		$this->getParsedBody();
		return $this;
	}
}