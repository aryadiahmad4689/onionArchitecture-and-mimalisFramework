<?php

namespace Phpfw\Component\Http;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
	protected $info; // $_FILES[$field]
	protected $movedName = null;
	protected $stream;

	public function __construct(array $info)
	{
		$this->info = $info;
	}

	public function getStream()
	{
		if (!$this->stream) {
			if ($this->movedName) {
				$this->stream = new Stream($this->movedName);
			} else {
				$this->stream = new Stream($this->info['tmp_name']);
			}
		}
		return $this->stream;
	}

	public function moveTo($targetPath)
	{
		$tempFile = $this->info['tmp_name'] ?? false;

		if (!is_uploaded_file($tempFile)) {
			throw new Exception(
				"File {$tempFile} Not Found!"
			);
		}

		$target = $targetPath . '/' . $this->info['name'];
		$target = str_replace('//', '/', $target);

		if (!move_uploaded_file($tempFile, $target)) {
			throw new RuntimeException(
				"Uploaded File was Failed!"
			);
		}

		$this->movedName = $target;

		return true;
	}

	public function getMovedName()
	{
		return $this->movedName ?? null;
	}

	public function getSize()
	{
		return $this->info['size'] ?? null;
	}

	public function getError()
	{
		if (!$this->movedName) {
			return UPLOAD_ERR_OK;
		}
		return $this->info['error'];
	}

	public function getClientFilename()
	{
		return $this->info['name'] ?? null;
	}

	public function getClientMediaType()
	{
		return $this->info['type'] ?? null;
	}
}
