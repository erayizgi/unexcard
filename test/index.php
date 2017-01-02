<?php 
$key = '6S00Kf7NsA99ZCgy7MKD8TVkQMFYvmcX';
function generateEmail()
{
	$tlds = array("com", "net", "gov", "org", "edu", "biz", "info");

	// string of possible characters
	$char = "0123456789abcdefghijklmnopqrstuvwxyz";

	// start output

	// main loop - this gives 1000 addresses
	for ($j = 0; $j < 1000; $j++) {

	  // choose random lengths for the username ($ulen) and the domain ($dlen)
	  $ulen = mt_rand(5, 10);
	  $dlen = mt_rand(7, 17);

	  // reset the address
	  $a = "";

	  // get $ulen random entries from the list of possible characters
	  // these make up the username (to the left of the @)
	  for ($i = 1; $i <= $ulen; $i++) {
	    $a .= substr($char, mt_rand(0, strlen($char)), 1);
	  }

	  // wouldn't work so well without this
	  $a .= "@";

	  // now get $dlen entries from the list of possible characters
	  // this is the domain name (to the right of the @, excluding the tld)
	  for ($i = 1; $i <= $dlen; $i++) {
	    $a .= substr($char, mt_rand(0, strlen($char)), 1);
	  }

	  // need a dot to separate the domain from the tld
	  $a .= ".";

	  // finally, pick a random top-level domain and stick it on the end
	  $a .= $tlds[mt_rand(0, (sizeof($tlds)-1))];

	  // done - echo the address inside a link
	  return $a;

	} 
}
$post = [
	'key' => '',
	'mail' => 'erayizgi@gmail.com',
	'password' => '123456',
	"sessionKey" => '5840139c6b257'
];
$test_cases = array(
	// "register" => array(
	// 	'key' => $key,
	// 	'mail' => generateEmail(),
	// 	'password' => '123456'
	// 	),
	// "firstLogin" => array(
	// 	'key' => $key,
	// 	'mail' => 'erayizgi@gmail.com',
	// 	'password' => '123456',
	// 	),
	// "loginCheck"=> array(
	// 	'key' => $key,		
	// 	"sessionKey" => '5840139c6b257'
	// 	)
	"createCard" => array(
		"key" =>$key,
		"sessionKey" => "5863adb5b2343",
		"is_address" => TRUE,
		"Country" => "Türkiye",
		"Province" => "İstanbul",
		"District" => "Bakırköy",
		"Address" => "Mahalle , sokak, numara",
		"cardName" => "Oktasoft Eray",
		"webSite" => "oktasoft.com",
		"phone1" => "+90(530) 067 81 08",
		"email" => "info@oktasoft.com",
		"title" => 5,
		"companyName" =>"Oktasoft",
		"name" => "Eray İzgi"
		),
	// "getTitles" => array(
	// 	"key" => $key
	// 	),
	// "getSocialMedia" => array(
	// 	"key" => $key
	// 	)
	);
foreach ($test_cases as $t => $v) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://82.196.9.196/unex/index.php/user/'.$t);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test_cases[$t]));
	$response = curl_exec($ch);
	$r = json_decode($response);
	if($r->result){
		echo "<pre style='padding:10px;background-color:green;color:white;'>Test URL: <a href='http://82.196.9.196/unex/index.php/user/".$t."'>http://82.196.9.196/unex/index.php/user/".$t."</a>";
		echo "<br>Test Passed";
		echo "<br>Result : \n <br>";
		echo $response."</pre>";
	}else{
		echo "<pre style='padding:10px;background-color:red;color:white;'>Test URL: <a href='http://82.196.9.196/unex/index.php/user/".$t."'>http://82.196.9.196/unex/index.php/user/".$t."</a>";
		echo "<br>Test Failed";
		echo "<br> Result : \n <br>";
		echo $response."</pre>";
	}
	
}
 ?>