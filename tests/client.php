<?php

require_once __DIR__."/../vendor/autoload.php";

use Phpfw\Component\Http\{
	Request,
	Stream
};

$body = new Stream(__DIR__.'/psudo.txt', 'w+');
$body->write("lorem impsum dolor sit amet");
$body->close();

$server = "http://localhost:8080/uploader.php";

$request = new Request();

$request->withRequestTarget($server)
		->withBody($body);

$tmpName = $request->getBody()
	->getInfo()
	->getPathname();

$fileName = $request->getBody()
	->getInfo()
	->getFilename();

$fileSize = $request->getBody()
	->getInfo()
	->getSize();

$name = $_FILES['upload']['name'][] = $fileName;
$_FILES['upload']['tmp_name'][] = $tmpName;
$_FILES['upload']['type'][] = 'plain/text';
$_FILES['upload']['error'][] = 0;
$_FILES['upload']['size'][] = $fileSize;

$fields = array();
$upload = $_FILES['upload'];
foreach($upload['error'] as $key => $error) {
	if($error === UPLOAD_ERR_OK) {
		$fields["upload[$key]"] = curl_file_create(
			$upload['tmp_name'][$key], 
			$upload['type'][$key], 
			$upload['name'][$key] 
		);
	}
}

$defaults = array(
	CURLOPT_URL => $request
		->getUri()
		->getUriString(), 
	CURLOPT_POST => true, 
	CURLOPT_RETURNTRANSFER => true, 
	CURLINFO_HEADER_OUT => true, 
	CURLOPT_POSTFIELDS => $fields,
);

$ch = curl_init();

curl_setopt_array($ch, $defaults);

$response = curl_exec($ch);

curl_close($ch);

var_dump($response);