<?php
namespace Phpfw\Component\Contract\FileSystem;

interface FilesystemInterface
{
	public function extension(string $file, string $extension = '.php');
	
	public function file($files = null, int $separator = 0, string $extension='.php');
	
	public function files(array $files = array(), $separator = 0, $extension='.php');

	public function separator(int $separator = 0);

	public function putContent($path, $data, $lock = false);

	public function exists($name);

	public function getRequire($filepath);
	
	public function getRequireOnce($filepath);
}