<?php
$urlStart = "http://172.16.37.129/api/start";
$urlTest = "http://172.16.37.129/api/test";
$token = "tokennm";
$name = "NeverMind";

//return int
function next_value($val) {
	do {
		$val++;
	} while (in_array($val))	
	return $val;
}

//return string
function get_value_to_string($aTestStatus) {
	$aValTmp="";
	for($i=0;$i<length($aTestStatus);$i++) {
		$aValTmp[] = $aTestStatus[$i][val];
	}	
	$val = implode ( "", $aValTmp);
	return $val;
}

//return string
function loop_vertical($aTestStatus, $current_column) {
	
	$current_val = get_value_to_string($aTestStatus);
	
	$val_to_inc = substr($current_val,$current_column,1);
	$val_inc = next_value($val_to_inc);
	if($val_inc; > 9) {
		echo "Error : row val ".val_inc." > 9";
		exit;
	}
	$current_val[$current_column] = $val_inc;

	return $val;
}

//return string
function loop_horizontal($aTestStatus, $current_column, $size) {
	
	$current_column++;
	if($current_column; > $size) {
		echo "Error : column ".$current_column." > size ".$size;
		exit;
	}

	$val = loop_vertical($aTestStatus, $current_column);

	return $val;
}

function set_position() {
	
}

function send_start($val) {
	
	return $result;
}

function send_test($val) {
	
	return $result;
}

function save_test() {

}

//Si ajouté à cet array on ne teste plus ce chiffre
$aBannedValue = array();

//send start
$json_result = send_start();
$result = json_decode($result,true);
$size = $result['size'];
$quizz_id = $result['quizz_id'];

//test no communcation
$to_find = rand(0, 99999); //"12345"; 
echo "To find:".$tofind;
$size = 5;
$quizz_id = 1;

//set array
$aTestStatus = array();
for($i=0;$i<=$size;$i++) {
	$aTestStatus[$i] = array(
		'ciffer' => 0,
		'status' => 0
	);
}


$current_value = get_value_to_string($aTestStatus); //"00000";
$current_column = 1;
$current_row = 1;
$end = false;

//set to find
//


do {

	$good = 0;
	$wrong_place = 0;

	//send test
	if ($current_test != $to_find) {
		$json_result = send_test($val);
		$result = json_decode($json_result,true);
		$good = $result['good'];
		$wrong_place = $result['wrong_place'];
	}

//json

	if($good == 0 && $wrong_place == 0) {
		
		//add banned value
		$aBannedValue[] = $ciffer; //checher le chiffre 

		$val = loop_vertical($aTestStatus, $current_column);

	} elseif($good == 1) {
		
		// ok -> next column
		$aTestStatus[$current_column]['status'] = 1;
		$val = loop_horizontal($aTestStatus, $current_column, $size);

	} elseif($wrong_place == 1) {
		
		// next column to find right column
		$val = loop_horizontal($aTestStatus, $current_column, $size);

	}

	if ($current_test == $to_find) {
		$end = true;
	}

} while (!$end)

echo "Finish"
?>


