<?php 
function Curl($method, $url, $header, $data, $cookie){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36');
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	if ($header) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if ($data) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	}
	return curl_exec($ch);
}
$access_tokenfb = [
	'317114656075656|MhBD4r9o9aP-hwNI-ya0q2ryvdM',
	'1754582024695406|lNASfnK254LszhhQZoyjO8EigQM',
	'670835880297746|w4ZI3oD5c1a4C7RFWwbNzrOPENo',
	'3920543907968468|k7oYX0VcCpmMRFDMP2l2LI5MBaA',
	'2738314886393290|8R8L2EaJnHz8IbGnARmUu47PF-0'
];
$bz = 0;
do {
	$facebookgen = Curl("GET", "https://graph.facebook.com/670835880297746/accounts/test-users?access_token=".$access_tokenfb[$bz]."&installed=true&permissions=read_stream&method=post", false, false, false);
	$token = json_decode($facebookgen,true);
	$bz++;
	if ($bz > 4) {
		$bz = 0;
	}
	echo $bz."\n";
} while (empty($token['access_token']));
print_r($token);
?>