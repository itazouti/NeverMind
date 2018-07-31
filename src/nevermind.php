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
    public $to_find = '12345';
    public $aTestStatus = array();
    public $current_value;
    public $current_ciffer;   
    public $current_column;
    public $current_row;
    public $end;
    public $rate_good = 0;
    
    
    function __construct() {
        file_put_contents('log_txt', "");
        $this->urlStart = "http://172.16.37.129/api/start";
        $this->urlTest = "http://172.16.37.129/api/test";
        $this->token = "tokennm";
        $this->name = "NeverMind";
        $this->current_value = $this->get_value_to_string($this->aTestStatus); //"00000";
        $this->ciffer = 0;
        $this->current_column = 0;
        $this->current_row = 0;
        $this->end = false;
        $this->set_TestStatusArray();
    }

    function set_TestStatusArray() {
        $this->aTestStatus = array();
        for($i=0;$i<$this->size;$i++) {
            $this->aTestStatus[$i] = array(
                'ciffer' => 0,
                'status' => 0
            );
        }
        //var_dump($this->aTestStatus);
    }
    function trace() {
        $this->log("COLUMN:".$this->current_column." ROW: ".$this->current_row." CIFFER: ".$this->ciffer." CUR:".$this->get_value_to_string());
    }
    
    function init() {
        $this->to_find = rand(0, 99999); //"12345";
        echo "To find:".$this->to_find."\n";
        $this->size = 5;
        $this->quizz_id = 1;
        $this->set_TestStatusArray();
    }
    
    function start() {
        //send start
        $json_result = $this->send_start();
        $result = json_decode($json_result,true);
        $this->size = $result['size'];
        $this->quizz_id = $result['quizz_id'];
        $this->set_TestStatusArray();
    }
    
    //return int
    public function next_value($val) {
    	do {
    		$val++;
    	} while (in_array($val,$this->aBannedValue ));
    	return $val;
    }
    
    //return string
    public function get_value_to_string() {
    	$valTmp="";
    	for($i=0;$i<count($this->aTestStatus);$i++) {
    		$valTmp .= $this->aTestStatus[$i]['ciffer'];
    	}	
    	return $valTmp;
    }
    
    //return string
    public function loop_vertical() {
        $this->log('LOOP VERT');
        
        //$this->aTestStatus[$this->current_column]++;
    	//$current_val = $this->get_value_to_string($this->aTestStatus);
    	
    	//$val_to_inc = substr($current_val,$this->current_column,1);
    	$val_inc = $this->next_value($this->aTestStatus[$this->current_column]['ciffer']);
    	if($val_inc > 9) {
    		echo "Error : row val ".$val_inc." > 9";
    		exit;
    	}
    	$this->aTestStatus[$this->current_column]['ciffer'] = $this->ciffer = $val_inc; 
    	$this->ciffer = $val_inc;
    	$this->current_row = $val_inc;
    	
    	return;
    }
    
    //return string
    public function loop_horizontal() {
        $this->log('LOOP HORZ');
        
    	$this->current_column++;
    	if($this->current_column > $this->size) {
    		echo "Error : column ".$this->current_column." > size ".$this->size;
    		exit;
    	}
    
    	$val = $this->loop_vertical();
    
    	return;
    }
    
    public function set_position() {
    	
    }
    
    public function log($str) {
        file_put_contents('log_txt', $str."\n", FILE_APPEND);
        echo $str."\n";
    } 
    
    public function send_start() {
        //$content = array('token', 'tokennm');
        $url = "http://172.16.37.129/api/start";
        $getdata = http_build_query(array(
                'name' => $this->name,
                'token' => $this->token
            ));
        $opts = array('http' =>
            array(
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                "User-Agent:MyAgent/1.0\r\n",
                'method'  => 'POST',
                'content' => $getdata
            )
        );
        $params = stream_context_create($opts);
        
        $json = file_get_contents($url, false, $params);
        
        echo "Start result:";
        var_dump($json);
        
    	return $json;
    }
    
    public function send_test() {
       // $content = array('result' => '12345', 'token', 'tokennm');
        $url = "http://172.16.37.129/api/test";
        $getdata = http_build_query(
            array(
                'result' => $this->current_value,
                'token' => $this->token
            )
        );
        $opts = array('http' =>
            array(
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                "User-Agent:MyAgent/1.0\r\n",
                'method'  => 'POST',
                'content' => $getdata
            )
        );
        $params = stream_context_create($opts);
        
        $json = file_get_contents($url, false, $params);
        
        echo "Test result for : ".$this->current_value;
        var_dump($json);
        
    	return $json;
    }
    
    public function save_test() {
    
    }
    
    
    public function test() {
        
        do {
        
            $this->good = 0;
            $this->wrong_place = 0;
        
            $this->log('TEST');
            $this->current_value = $this->get_value_to_string();
            
            //send test
            if ($this->current_value != $this->to_find) {
                $json_result = $this->send_test();
                $result = json_decode($json_result,true);
                $this->good = $result['good'];
                $this->wrong_place = $result['wrong_place'];
                $this->error = $result['error'];
                if(!empty($this->error)) exit; 
                //"{"Error":"Answer already found"}"
            }
        
            if($this->good == $this->rate_good ) { //&& $this->wrong_place == 0
                $this->log('NO GOOD NO WRONG');
                
                //add banned value
                $this->aBannedValue[] = $this->ciffer; //checher le chiffre
                //var_dump($this->aBannedValue);
                
                $val = $this->loop_vertical();
        
            } elseif($this->good == $this->rate_good+1) {
                $this->log('GOOD');
                $this->rate_good++;
                
                if ($this->current_value == $this->to_find) {
                    $this->end = true;
                    $this->log('END');
                    exit;
                }
                
                // ok -> next column
                $aTestStatus[$this->current_column]['status'] = 1;
                $val = $this->loop_horizontal();
        
            } elseif($this->wrong_place != 0) {
                $this->log('WRONG PLACE');
                
                // next column to find right column
                //$val = $this->loop_horizontal();
        
            }
        
            if ($this->current_value == $this->to_find) {
                $this->end = true;
                $this->log('END');
            }

            $this->trace();
            
        } while (!$this->end);
        
        return;
    }
}

$NM = new neverMind();
$NM->init();
$NM->start();
$NM->test();

echo "Finish"
?>


