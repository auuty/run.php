<?php 
class coinmaster{
	const CURL_TIMEOUT = 3600;
	const CONNECT_TIMEOUT = 30;
	private function Curl($method, $url, $header, $data, $cookie){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
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
	private function header(){
		$header = array(
			"Expect: 100-continue",
			"Connection: keep-alive",
			"Host: vik-game.moonactive.net"
		);
		return $header;
	}
	private function headerwhittoken($devicetoken){
		$header = array(
			"Expect: 100-continue",
			"Connection: keep-alive",
			"X-CLIENT-VERSION: 3.5.191",
			"Cookie: cme=global;",
			"Content-Type: application/x-www-form-urlencoded",
			"Authorization: Bearer ".$devicetoken,
			"Host: vik-game.moonactive.net"
		);
		return $header;
	}
	private function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	private function gettokenfb(){
		$access_tokenfb = [
			'178718623906981|2nFcme7CTnIc27k86SRwLB0h',
			'111660834022401|WhdA3EIzKPynFarF23m3M1nd4SQ',
            '2299913480152618|XZfHCSON7wSEMLSHq3ffrhp4EGY',
            '4103385286344225|q_SwQP09cL7ipMIzUD57Wqi4WlA',
            '425782572130545|c1DT4N0-zySnyAZryb0iOFPh5SE',
            '3460783750672092|Fvtafm9px70t9m2hT-M_b75ypJ8',
			
		];
		$bz = 0;
		do {
			$facebookgen = $this->Curl("GET", "https://graph.facebook.com/670835880297746/accounts/test-users?access_token=".$access_tokenfb[$bz]."&installed=true&permissions=read_stream&method=post", false, false, false);
			$token = json_decode($facebookgen,true);
			$bz++;
			if ($bz > 2) {
				$bz = 0;
			}
			echo "AUUTY MIZK | สปิน ไทยแลนด์ ✅สำเร็จ🇹🇭: ".$bz."\n";
		} while (empty($token['access_token']));
		$this->fb['access_token'] = $token['access_token'];
		return $this->fb['access_token'];
	}
	private function Login($deviceID,$devicetoken){
		$data = "Device%5budid%5d=".$deviceID."&API_KEY=viki&API_SECRET=coin&Client%5bversion%5d=3.5_fband&Device%5bchange%5d=20201105_5&fbToken=&seq=0";
		$login = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/login", $this->headerwhittoken($devicetoken), $data, false);
		$info = json_decode($login,true);
		$res = array(
			"deviceID" => $deviceID,
			"info" => array(
				"change_timestamp" => $info['change_timestamp'],
				"profile" => $info['profile'],
				"sessionToken" => $info['sessionToken'],
				"userId" => $info['userId']
			)
		);
		return json_encode($res,JSON_UNESCAPED_SLASHES);
	}
	private function Loginfbgame($deviceID,$devicetoken,$userid,$fbtoken){
		$data = "Device%5budid%5d=".$deviceID."&API_KEY=viki&API_SECRET=coin&User%5bfb_token%5d=".$fbtoken."&p=fb&Client%5bversion%5d=3.5.191_fband&Device%5bchange%5d=20201105_5";
		$startlogin = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$userid."/update_fb_data", $this->headerwhittoken($devicetoken), $data, false);
		return $startlogin;
	}
	private function Start(){
		$deviceID = $this->gen_uuid();
		$data = array( 
			'deviceId' => $deviceID 
		);
		$start = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/authentication/register", $this->header(), $data, false);
		$register = json_decode($start, true);
		$startlogin = $this->Login($deviceID,$register['deviceToken']); //รับค่าทั่งหมด
		$startlogin = json_decode($startlogin,true); //แปลงเป็น array
		$this->deviceID = $startlogin['deviceID'];
		$this->nonfbuserId = $startlogin['info']['userId'];
		$this->sessionToken = $startlogin['info']['sessionToken'];
	}
	private function Start2($link){
		$facetoken = $this->gettokenfb();
		$startloginfb = $this->Loginfbgame($this->deviceID,$this->sessionToken,$this->nonfbuserId,$facetoken);
		$startloginfb = json_decode($startloginfb,true);
		
		if (empty($startloginfb['userId'])) {
			$this->addspin($link);
			exit();
		}
		$this->userId = $startloginfb['userId'];
		$this->fbUserId = $startloginfb['fbUserId'];
		$this->fbToken = $startloginfb['fbToken'];
	}
	public function addspin($link){
		$this->Start();
		$this->Start2($link);
		$bossnz = preg_match_all('/~[^}]*?s=m/', $link, $a);
		if ($bossnz == NULL) {
			$bossnz = preg_match_all('/~[^}]*/', $link, $a);
			$edit1 = str_replace('~', '', $a[0]);
			$edit2 = str_replace('', '', $edit1[0]);
			$link = $edit2;
		}else{
			$edit1 = str_replace('~', '', $a[0]);
			$edit2 = str_replace('?s=m', '', $edit1[0]);
			$link = $edit2;
		}
		//หาuseridของคนแชร์ลิ้ง
		$getuseridaddlink = $config = $this->Curl("GET", "https://vik-game.moonactive.net/external/users/~".$link."/invite?s=m", false, false, false);
		$getuseridaddlinkpor = preg_match_all('/&amp;c=[^}]*/', $getuseridaddlink, $pora);
		$getuseridaddlink1 = str_replace('&amp;c=', '', $pora[0]);
		$getuseridaddlink2 = str_replace('', '', $getuseridaddlink1[0]);
		//data post
		$data = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=th&1604586433725=delete";
		$data2 = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=th";
		$data3 = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=th&item=House&state=0&include%5b0%5d=pets";
		$data4 = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=th&item=House&state=1&include%5b0%5d=pets";
		$data5 = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=th&item=Farm&state=0&include%5b0%5d=pets";
		$data6 = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=th&item=Ship&state=0&include%5b0%5d=pets";
		$dataconfig = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_5&fbToken=".$this->fbToken."&locale=th&map%5blocale%5d=th";
		$balanceconfig = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_5&fbToken=&locale=en&Device%5bos%5d=Android&Client%5bversion%5d=3.5.210&extended=true&config=all&segmented=true&include%5b0%5d=pets&include%5b1%5d=vquestRewards";
		$datafriends = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_5&fbToken=".$this->fbToken."&locale=en&non_players=500&p=fb&snfb=true";
		$dataaccept_invitation = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_5&fbToken=&locale=en&inviter=".$getuseridaddlink2;
		//เริ่มเล่นเกม
		$accept_invitation = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/accept_invitation", $this->headerwhittoken($this->sessionToken), $dataaccept_invitation, false);
		$config = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/config", $this->headerwhittoken($this->sessionToken), $dataconfig, false);
		$balance = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/balance", $this->headerwhittoken($this->sessionToken), $balanceconfig, false);
		$friends = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/friends", $this->headerwhittoken($this->sessionToken), $datafriends, false);
		$upgread = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/upgrade", $this->headerwhittoken($this->sessionToken), $data3, false);
		$coun = 1;
		for ($i=0; $i < 18; $i++) { 
			$coun++;
			$dataspin = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_4&fbToken=".$this->fbToken."&locale=en&seq=".$coun."&auto_spin=False&bet=1&Client%5bversion%5d=3.5.210_fband";
			$startspin = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/spin", $this->headerwhittoken($this->sessionToken), $dataspin, false);
		}
		$start = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/read_sys_messages", $this->headerwhittoken($this->sessionToken), $data, false);
		$upgread2 = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/upgrade", $this->headerwhittoken($this->sessionToken), $data4, false);
		$upgread3 = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/upgrade", $this->headerwhittoken($this->sessionToken), $data5, false);
		$upgread4 = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/upgrade", $this->headerwhittoken($this->sessionToken), $data6, false);
		$dataconfigloop = "Device%5budid%5d=".$this->deviceID."&API_KEY=viki&API_SECRET=coin&Device%5bchange%5d=20201105_5&fbToken=".$this->fbToken."&locale=th&map%5bMaxXP%5d=4";
		$configloop = $this->Curl("POST", "https://vik-game.moonactive.net/api/v1/users/".$this->userId."/config", $this->headerwhittoken($this->sessionToken), $dataconfigloop, false);
		return $accept_invitation;
	}
}
?>