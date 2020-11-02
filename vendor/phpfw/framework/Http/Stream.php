<?php

namespace Phpfw\Component\Http;

use SplFileInfo;
use Throwable;
use RuntimeException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
	/** The default body stream used for write and read stream **/
	const DEFAULT_BODY_STREAM = 'php://input';

	/**
	 * The stream with Write-read mode
	 * @const string 
	 */
	const MODE_WRITE_READ = 'w+';

	/**
	 * The stream with Write only mode
	 * @const string 
	 */
	const MODE_WRITE = 'w';

	/**
	 * The stream with read only mode
	 * @const string 
	 */
	const MODE_READ = 'r';

	/**
	 * The input stream
	 * @var $stream
	 */
	protected $stream;

	/**
	 * The stream metadata
	 * @var array $metadata
	 */
	protected $metadata;

	/**
	 * SplFileInfo instance
	 * @var \SplFileInfo $info
	 */
	protected $info;

	/**
	 * @param string $input The input stream
	 * @param string $mode The input stream mode, Ex. `r`, `r+`, `rb`, `w`, w+
	 */
	public function __construct(
		$input = Stream::DEFAULT_BODY_STREAM,
		$mode = Stream::MODE_READ
	) {
		$this->stream = fopen($input, $mode);
		$this->metadata = stream_get_meta_data($this->stream);
		$this->info = new SplFileInfo($input);
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getStream()
	 */
	public function getStream()
	{
		return $this->stream;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getInfo()
	 */
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::read()
	 */
	public function read($length)
	{
		$stream = $this->stream;
		$ex = "Couldn't read to the stream [$stream]";

		if (!($stream = fread($stream, $length))) {
			throw new RuntimeException($ex);
		}

		return $stream;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::write()
	 */
	public function write($string)
	{
		$stream = $this->stream;

		if (!fwrite($stream, $string)) {
			throw new RuntimeException("Couldn't write to the stream [$stream]");
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::rewind()
	 */
	public function rewind()
	{
		$stream = $this->stream;

		if (!rewind($stream)) {
			throw new RuntimeException("Couldn't rewind the stream [$stream]");
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::eof()
	 */
	public function eof()
	{
		return eof($this->stream);
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::tell()
	 */
	public function tell()
	{
		try {
			return ftell($this->stream);
		} catch (Throwable $e) {
			throw new RuntimeException($e->getMessage());
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::seek()
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		try {
			fseek($this->stream, $offset, $whence);
		} catch (Throwable $e) {
			throw new RuntimeException($e->getMessage());
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::close()
	 */
	public function close()
	{
		if ($this->stream) {
			fclose($this->stream);
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::detach()
	 */
	public function detach()
	{
		return $this->close();
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getMetadata()
	 */
	public function getMetadata($key = null)
	{
		if ($key) {
			return $this->metadata[$key] ?? null;
		} else {
			return $this->metadata;
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getSize()
	 */
	public function getSize()
	{
		return $this->info->getSize();
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::isSeekable()
	 */
	public function isSeekable()
	{
		return boolval($this->metadata['seekable']);
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::isWritable()
	 */
	public function isWritable()
	{
		return $this->stream->isWritable();
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getReadable()
	 */
	public function isReadable()
	{
		return $this->info->isReadable();
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getContents()
	 */
	public function getContents()
	{
		ob_start();
		fpassthru($this->stream);
		return ob_get_clean();
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::__toString()
	 */
	public function __toString()
	{
		$this->rewind();
		return $this->getContents();
	}
}
