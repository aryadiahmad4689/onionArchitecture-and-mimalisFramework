<?php
namespace Phpfw\Component\Http;

use Throwable;
use RuntimeException;
use SplFileInfo;
use Psr\Http\Message\StreamInterface;

class TextStream implements StreamInterface
{
	/**
	 * The text stream
	 * @var string $stream
	 */
	protected $stream;

	/**
	 * The pointer position
	 * @var integer $pos
	 */
	protected $pos = 0;

	/**
	 * @param string $input
	 */
	public function __construct(string $input = '')
	{
		$this->stream = $input;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::getStream()
	 */
	public function getStream()
	{
		return $this->stream;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::getInfo()
	 */
	public function getInfo()
	{
		return null;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::getContents()
	 */
	public function getContents()
	{
		return $this->stream;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::__toString()
	 */
	public function __toString()
	{
		return $this->getContents();
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::getSize()
	 */
	public function getSize()
	{
		return strlen($this->stream);
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::close()
	 */
	public function close()
	{
		//Do nothing
		//How can you 'close' string?
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::detach()
	 */
	public function detach()
	{
		return $this->close();
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::tell()
	 */
	public function tell()
	{
		return $this->pos;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::eof()
	 */
	public function eof()
	{
		return ($this->pos === strlen($this->stream));
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::isSeekable()
	 */
	public function isSeekable()
	{
		return true;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::seek()
	 */
	public function seek($offset, $whence= SEEK_SET)
	{
		if ($offset < $this->getSize()) {
			$this->pos = $offset;
		} else {
			throw new RuntimeException("The given offset not found");
		}
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::rewind()
	 */
	public function rewind()
	{
		$this->pos = 0;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::isWritable()
	 */
	public function isWritable()
	{
		return true;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::write()
	 */
	public function write($string)
	{
		$temp = substr($this->stream, 0, $this->pos);
		$this->stream = $temp . $string;
		$this->pos = strlen($this->stream);

		return $this;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::isSeekable()
	 */
	public function isReadable()
	{
		return true;
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::read()
	 */
	public function read($length)
	{
		return substr($this->stream, $this->pos, $length);
	}

 	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\StreamInterface::getMetadata()
	 */
	public function getMetadata($key = null)
	{
		return null;
	}
}