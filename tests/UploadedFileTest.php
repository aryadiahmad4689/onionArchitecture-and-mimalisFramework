<?php

use Phpfw\Component\Http\Stream;
use Phpfw\Component\Http\UploadedFile;

class UploadedFileTest extends PHPUnit\Framework\TestCase
{
	public function setUp(): void
	{
		$temporary = new Stream(__DIR__.'/psudo', 'w+');
		$temporary->write("lorem impsum dolor sit amet");
		$temporary->close();
		
		$this->temporary = $temporary;

		$tmpName = $temporary->getInfo()
			->getPathname();
		
		$fileName = $temporary->getInfo()
			->getFilename();


		$name = $_FILES['upload']['name'] = $fileName;
		$_FILES['upload']['tmp_name'] = $tmpName;
		$_FILES['upload']['type'] = 'plain/text';
		$_FILES['upload']['error'] = 0;
		$_FILES['upload']['size'] = $temporary->getSize();
		$this->uploader = new UploadedFile($_FILES['upload']);
	}
	
	public function teardown(): void
	{
		unset($this->uploader);
	}
	

	
	public function testInstanceOfStreamInterface()
	{
		$this->assertInstanceOf(
			"\Psr\Http\Message\StreamInterface", 
			$this->uploader->getStream()
		);
	}
	
	public function testMovedNameNull()
	{
		$this->assertNull($this->uploader->getMovedName());
	}
	
	public function testFileSize()
	{
		$this->assertEquals($this
			->temporary
			->getSize(), $this
			->uploader
			->getSize()
		);
	}
	
	public function testEqualsErrorOk()
	{
		$this->assertEquals(UPLOAD_ERR_OK, $this
			->uploader
			->getError()
		);
	}

	public function testEqualsContentType()
	{
		$this->assertEquals("plain/text", $this
			->uploader
			->getClientMediaType()
		);
	}
}