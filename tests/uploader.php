<?php
require_once __DIR__."/../vendor/autoload.php";

use Phpfw\Component\Http\TextStream;
use Phpfw\Component\Http\UploadedFile;
use Phpfw\Component\Http\ServerRequest;
use Phpfw\Component\Http\Response;

$server = new ServerRequest();
$uploadedFileInfo = $server->getUploadedFileInfo();

try {
	$message = true;
	$uploadedFiles = array();

	if (isset($uploadedFileInfo)) {
	
		$upload = &$uploadedFileInfo['upload'];

		foreach ($upload['error'] as $key => $info) {
			if($upload['error'][$key] === UPLOAD_ERR_OK) {
				$uploadedFileInfo['tmp_name'] = $upload['tmp_name'][$key];
				$uploadedFileInfo['name'] = $upload['name'][$key];
				$uploadedFileInfo['size'] = $upload['size'][$key];
				$uploadedFileInfo['type'] = $upload['type'][$key];
				$uploadedFileInfo['error'] = $upload['error'][$key];
				unset($uploadedFileInfo['upload']);
				
				$uploadedFiles[$key] = new UploadedFile($uploadedFileInfo);
				$uploadedFiles[$key]->moveto(__DIR__);
			}
		}
	}
} catch (Throwable $e) {
	$message = $e->getMessage();
}

$body = new TextStream;
$response = new Response;

if ($message) {
	foreach($uploadedFiles as $key => $uploader) {
		$message =  array(
			"File with name ". $uploader
			->getClientFilename().
			" succesfully uploaded\n");

		$body = $body->write(json_encode($message));

		$response->withHeader(
			"Content-Type", 
			"application/json")
			->withStatus(200)
			->withBody($body);

		echo $response->getBody()->getContents();
	}

} else {
	$body = $body->write(
		json_encode("File failed to upload: ". $message)
	);
	
	$response->withHeader(
		"Content-Type", 
		"application/json")
		->withStatus(200)
		->withBody($body);

	echo $response->getBody()->getContents();
}