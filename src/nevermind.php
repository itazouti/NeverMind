<?php
class nevermind
{
    public $urlStart = "http://172.16.37.129/api/start";
    public $urlTest = "http://172.16.37.129/api/test";
    public $token = "tokennm";
    public $name = "NeverMind";
    public $aBannedValue = array();
    public $quizz_id = 1;
    public $size = 5;
    public $to_find;
    public $aTestStatus = array();
    public $current_value;
    public $current_column;
    public $current_row;
    public $end;
    
    function __construct() {
        $this->urlStart = "http://172.16.37.129/api/start";
        $this->urlTest = "http://172.16.37.129/api/test";
        $this->token = "tokennm";
        $this->name = "NeverMind";
        $this->current_value = get_value_to_string($this->aTestStatus); //"00000";
        $this->current_column = 1;
        $this->current_row = 1;
        $this->end = false;
    }

    function set_Array() {
        $this->aTestStatus = array();
        for($i=0;$i<=$size;$i++) {
            $this->aTestStatus[$i] = array(
                'ciffer' => 0,
                'status' => 0
            );
        }
        
    }
    
    function init() {
        $this->to_find = rand(0, 99999); //"12345";
        echo "To find:".$this->tofind;
        $this->size = 5;
        $this->quizz_id = 1;
    }
    
    function start() {
        //send start
        $json_result = $NM->send_start();
        $result = json_decode($result,true);
        $size = $result['size'];
        $quizz_id = $result['quizz_id'];        
    }
    
    //return int
    public function next_value($val) {
    	do {
    		$val++;
    	} while (in_array($val));	
    	return $val;
    }
    
    //return string
    public function get_value_to_string() {
    	$aValTmp="";
    	for($i=0;$i<count($this->aTestStatus);$i++) {
    		$aValTmp[] = $this->aTestStatus[$i][val];
    	}	
    	$val = implode ( "", $aValTmp);
    	return $val;
    }
    
    //return string
    public function loop_vertical() {
    	
    	$current_val = get_value_to_string($this->aTestStatus);
    	
    	$val_to_inc = substr($current_val,$this->current_column,1);
    	$val_inc = next_value($val_to_inc);
    	if($val_inc > 9) {
    		echo "Error : row val ".val_inc." > 9";
    		exit;
    	}
    	$current_val[$this->current_column] = $val_inc;
    
    	return $current_val;
    }
    
    //return string
    public function loop_horizontal() {
    	
    	$this->current_column++;
    	if($this->current_column > $this->size) {
    		echo "Error : column ".$this->current_column." > size ".$this->size;
    		exit;
    	}
    
    	$val = $this->loop_vertical();
    
    	return $val;
    }
    
    public function set_position() {
    	
    }
    
    public function send_start($val) {
    	
    	return $result;
    }
    
    public function send_test($val) {
    	
    	return $result;
    }
    
    public function save_test() {
    
    }
    
    
    public function test() {
        
        do {
        
            $this->good = 0;
            $this->wrong_place = 0;
        
            //send test
            if ($this->current_test != $this->to_find) {
                $json_result = $this->send_test($val);
                $result = json_decode($json_result,true);
                $this->good = $result['good'];
                $this->wrong_place = $result['wrong_place'];
            }
        
            //json
        
            if($this->good == 0 && $this->wrong_place == 0) {
        
                //add banned value
                $this->aBannedValue[] = $ciffer; //checher le chiffre
        
                $val = $this->loop_vertical();
        
            } elseif($this->good == 1) {
        
                // ok -> next column
                $aTestStatus[$this->current_column]['status'] = 1;
                $val = $this->loop_horizontal();
        
            } elseif($this->wrong_place == 1) {
        
                // next column to find right column
                $val = $this->loop_horizontal();
        
            }
        
            if ($this->current_test == $this->to_find) {
                $this->end = true;
            }
        
        } while (!$this->end);
        
        return;
    }
}

$NM = new neverMind();
$NM->init();
//$NM->start();
$NM->test();

echo "Finish"
?>


