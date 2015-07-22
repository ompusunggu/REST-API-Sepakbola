<?php
 
require_once 'include/DbHandler.php';
require_once 'include/PassHash.php';
require 'libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------
 */
 
//hasil
$app->post('/hasil', function () use ($app){
	$response = array();
	//$allPostVars = $app->request->post();
	//var_dump($allPostVars);
	$request = $app->request();
    $body = $request->getBody();
    $input = json_decode($body);
    $data	=($request->post("data"));
	$apikey	=($request->post("apikey"));
	$json = json_decode((urldecode($data)));
	if($json->activity == "Add"){
		$db = new DbHandler();
		$Att = $json->Attributes;
		var_dump($db->insertData($json->uniquid,$Att->code,$Att->gender,$Att->age));
		$httpStatus = 200;
	$response = "<DATASET><TYPE>success</TYPE></DATASET>";
	}else if($json->activity == "Update"){
		$db = new DbHandler();
		$Att = $json->Attributes;
		var_dump($db->updateData($json->uniquid,$Att->code,$Att->gender,$Att->age));
		$httpStatus = 200;
	$response = "<DATASET><TYPE>success</TYPE></DATASET>";
	}else{
		$httpStatus = 400;
	$response = "<DATASET><TYPE>Error</TYPE></DATASET>";
	}
 	
	echoRespnse($httpStatus, $response);
});


$app->post('/pekerjaan', function () {
	$response = array();

	$db = new DbHandler();

	// fetching all hasil
	$result = $db->getPekerjaan();
		//print_r($result);


	$response["error"] = false;
	$response["hasil"] = array();

	// looping through result and preparing materi array
	while ($strData = $result->fetch_assoc()) {
		$tmp = array();
	    $tmp["NIM"] = utf8_encode($strData["NIM"]);
	    $tmp["NAMA_PERUSAHAAN"] = utf8_encode($strData["NAMA_PERUSAHAAN"]);
	    $tmp["BIDANG_PEKERJAAN"] = utf8_encode($strData["BIDANG_PEKERJAAN"]);
	    $tmp["GAJI"] = utf8_encode($strData["GAJI"]);
	 
	    array_push($response["hasil"], $tmp);
	}

	echoRespnse(200, $response);
});

$app->post('/kamus', function () {
	$response = array();

	$db = new DbHandler();

	// fetching all hasil
	$result = $db->getKamus();
		//print_r($result);


	$response["error"] = false;
	$response["hasil"] = array();

	// looping through result and preparing materi array
	while ($strData = $result->fetch_assoc()) {
		$tmp = array();
	    $tmp["WORD"] = utf8_encode($strData["WORD"]);
	    $tmp["KETERANGAN"] = utf8_encode($strData["KETERANGAN"]);
	 
	    array_push($response["hasil"], $tmp);
	}

	echoRespnse(200, $response);
});
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 * Daftar response
 * 200	OK
 * 201	Created
 * 304	Not Modified
 * 400	Bad Request
 * 401	Unauthorized
 * 403	Forbidden
 * 404	Not Found
 * 422	Unprocessable Entity
 * 500	Internal Server Error
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('text/html');

	//print_r($response);
	//sleep(10);
    echo ($response);
}


$app->run();
?>