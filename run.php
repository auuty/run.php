<?php 
require 'api-coinmaster.php';
$class = new coinmaster;
popen('cls', 'w');
echo "AUUTYMIZK | TH\n";
echo "------------------ [ FB: AUUTY MiZk ] ------------------\n";
$link = readline(" Spin By.AuuTy :");
$count = readline(" Number | by.Auuty :");
popen('cls', 'w');
echo "------------------ [ 0/".$count." ] ------------------\n";
$bossnz = preg_match_all('/~[^}]*?s=m/', $link, $a);
if ($bossnz == NULL) {
	$bossnz = preg_match_all('/~[^}]*/', $link, $a);
	$edit1 = str_replace('~', '', $a[0]);
	$edit2 = str_replace('', '', $edit1[0]);
	$code = $edit2;
}else{
	$edit1 = str_replace('~', '', $a[0]);
	$edit2 = str_replace('?s=m', '', $edit1[0]);
	$code = $edit2;
}
if (empty($code)) {
	echo "ERROR : TryAgain";
	exit();
}
for ($i=0; $i < $count; $i++) {
	$start = $class->addspin($link);
	$number = $i+1;
	popen('cls', 'w');
	echo "================= [ FB: AUUTY MIZK🇹🇭 ] =================\n";
	print_r("[ ".$number."/".$count." ] - Code : ".$code."\n");
}
?>