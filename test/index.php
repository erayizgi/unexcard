<?php 

$post = [
	'key' => '6S00Kf7NsA99ZCgy7MKD8TVkQMFYvmcX',
	'mail' => 'erayizgi@gmail.com',
	'password' => '123456',
	"sessionKey" => '5840139c6b257'
];
$test_cases = array("register","loginCheck","firstLogin");
foreach ($test_cases as $t) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://82.196.9.196/unex/index.php/user/'.$t);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	$response = curl_exec($ch);
	$r = json_decode($response);
	if($r->result){
		echo "<pre>Test URL: ".$t." </pre>";
		echo "<pre>Test Passed</pre>";
		echo "<pre> Result : \n <br>";
		echo $response."</pre>";
	}else{
		echo "<pre>Test URL: ".$t." </pre>";
		echo "<pre>Test Failed</pre>";
		echo "<pre> Result : \n <br>";
		echo $response."</pre>";
	}
	
}
 ?>