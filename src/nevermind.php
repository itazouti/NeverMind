<?php
class nevermind
{
    public $urlStart;
    public $urlTest;
    public $token;
    public $name;
    public $quizz_id;
    public $size;
    public $to_find;
    public $aNumberStatus;
    public $current_value;
    public $current_ciffer;   
    public $current_column;
    public $current_row;
    public $end;
    public $rate_good = 0;
    private $aValidCiffer = array();
    private $validCiffer = "";
    private $aInvalidCiffer = array();
    private $invalidCiffer = "";
    
    
    function __construct() {
        //file_put_contents('log_txt', "");
        $this->urlStart = "http://172.16.37.129/api/start";
        $this->urlTest = "http://172.16.37.129/api/test";
        $this->token = "tokennm";
        $this->name = "NeverMind";
        $this->current_value = '';
        $this->ciffer = 0;
        $this->current_column = 0;
        $this->current_row = 0;
        $this->end = false;
        $this->aValidCiffer = array();
        $this->aInvalidCiffer = array();
        $this->validCiffer = '';
        $this->invalidCiffer = '';
        $this->aNumberStatus = array();
        //$this->set_TestStatusArray();
    }


    function init() {
        $this->size = 15;
        $this->quizz_id = 1;
        $random = rand(0, 99999); //"12345";
        $this->to_find = str_pad($random, $this->size, "0", STR_PAD_LEFT);
        $this->to_find = '135792468013579';
        echo "To find:".$this->to_find."\n";
        //$this->set_TestStatusArray();
    }
    
    function start() {
        //send start
        $json_result = $this->send_start();
        $result = json_decode($json_result,true);
        $this->size = $result['size'];
        $this->quizz_id = $result['quizz_id'];
        //$this->set_TestStatusArray();
    }
    
    public function log($str) {
        //file_put_contents('log_txt', $str."\n", FILE_APPEND);
        echo $str."\n";
    }
    
    function trace() {
        $this->log("COLUMN:".$this->current_column." ROW: ".$this->current_row." CIFFER: ".$this->ciffer." CUR:".$this->get_value_to_string());
    }
    
    public function send_start() {
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
        
        $this->log("SEND START");
        
        var_dump($json);
        
    	return $json;
    }
    
    public function send_test() {
       
        if ($this->current_value == $this->to_find){
            $this->log("FOUND : ".$this->current_value);
            
            exit();    
        }
        
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
        
        $this->log("SEND TEST : ".$this->current_value);
        $this->log("RESULT : ");
        var_dump($json);
        
    	return $json;
    }
        
    public function test_result() {
        
        if ($this->current_value == $this->to_find){
            $this->log("FOUND : ".$this->current_value);
        
            exit();
        }
        
        $good = 0;
        $wrong_place = 0;
        
        $aCurVal = str_split($this->current_value);
        $aToFind = str_split($this->to_find);
        
        for($i=0;$i<strlen($this->to_find);$i++) {
            if($aCurVal[$i] == $aToFind[$i]){
                $good++;
            } else {
                for($j=0;$j<strlen($this->to_find);$j++) {
                    if($aCurVal[$i] == $aToFind[$j]){
                        $wrong_place++;
                    }    
                }
            }
        }
        
        $this->log("SEND TEST : ".$this->current_value);
        $this->log("RESULT : ");

        $json = json_encode(array("good"=>$good,"wrong_place"=>$wrong_place));
        var_dump($json);
        
        return $json;
    }
    
    public function test_ciffers() {
    
        $this->log('TEST CIFFERS');
        
        $count_good = 0;
        $iSmallerOccurence = 0;
        $smallerOccurence = $this->size+10;
        
        for($i=0;$i<10;$i++) {
    
            $this->good = 0;
            $this->wrong_place = 0;
    
            $this->current_value = str_repeat($i,$this->size);
            
            //send test
            if ($this->current_value != $this->to_find) {
                //$json_result = $this->send_test();
                $json_result = $this->test_result();
                $result = json_decode($json_result,true);
                $this->good = $result['good'];
                $this->wrong_place = $result['wrong_place'];
                $this->error = $result['Error'];
                //$this->log("VALUE: ".$this->current_value." GOOD:".$this->good);
                $this->aNumberStatus[] = $this->good;
                if(!empty($this->error)) {
                    $this->log("Error Ciffers");
                    break;
                }
            }
    
            if($this->good==0) {
                $this->aInvalidCiffer[] = $i;
                $this->invalidCiffer .= $i;
            }
            
            for($j=0;$j<$this->good;$j++) {
                $count_good++;
                $this->aValidCiffer[] = $i;
                $this->validCiffer .= $i;
                if($this->good < $smallerOccurence) {
                    $smallerOccurence = $this->good;
                    $iSmallerOccurence = $i;
                }
            }
            //$this->trace();
            
            //IF ALL GOOD => EXIT
            if($count_good == $this->size) {
                //break;
                $this->log("ADD rest of invalid that not exist in array valid");
            }
        }

        // On ajoute le chiffre avec le moins d'occurence
        if (empty($this->aInvalidCiffer)) {
                $this->aInvalidCiffer[] = $iSmallerOccurence;
                $this->invalidCiffer .= $iSmallerOccurence;
        }
        
        $this->log('VALID CIFF : '.implode(',',$this->aValidCiffer));
        $this->log('VALID RATE : '.implode(",",$this->aNumberStatus));
        $this->log('INVALID CIFFERS : '.implode(',',$this->aInvalidCiffer));
        
        return;
    }
    
    public function test_positions() {
        
        $this->log('TEST POSITIONS');
        
        $this->current_value = str_repeat($this->aInvalidCiffer[0],$this->size);
        
        $this->previous_good = $this->aNumberStatus[0];
        $this->previous_value = "";
        
        //test chaque chiffre puis passe a la position suivante lorsque goot est incrémenté
        for($pos=0;$pos<$this->size;$pos++) {
            
            $iCiffer = 0;
            
            do {
                $this->log('###########################################');
                $this->log('POSITION : '.$pos.' ICIFFER : '.$iCiffer.' VALID : '.implode(',',$this->aValidCiffer));
                
                //var_dump($this->aValidCiffer);
                // test ciffer à la position pos
                $this->current_value[$pos] = $this->aValidCiffer[$iCiffer];

                //send test
                if ($this->current_value != $this->to_find) {
                    //$json_result = $this->send_test();
                    $json_result = $this->test_result();
                    $result = json_decode($json_result,true);
                    $this->good = $result['good'];
                    $this->error = $result['Error'];
                    if(!empty($this->error)) exit;
                } else {
                    $this->log('TO FIND : '.$this->to_find);
                    $this->log('FOUND   : '.$this->current_value);
                    return;
                }
                
                $iCiffer++;
                
                $this->log('GOOD :'.$this->good.' POSITION : '.$pos.' ICIFFER : '.$iCiffer.' VALID : '.implode(",",$this->aValidCiffer).' INVALID : '.implode(",",$this->aInvalidCiffer));
                
                //$this->log('pos == (pos + good - rate) POS :'.$pos.' + GOOD :'.$this->good.' - STATUS : '.$this->aNumberStatus[$this->aValidCiffer[$iCiffer-1]]);
                $this->log('PREV_GOOD :'.$this->previous_good.' == GOOD :'.$this->good);
                
                if ($this->previous_good > $this->good) {
                    $iCiffer--;
                    $this->current_value  = $this->previous_value;
                    break;    
                }
                
                $this->previous_value = $this->current_value;
                
            //} while( $this->good == $pos );
            //} while( $pos == $pos + $this->good - $this->aNumberStatus[$this->aValidCiffer[$iCiffer-1]] );
            } while($this->previous_good == $this->good);
            
            
            $this->previous_good = $this->good;
            
            // ajouter le chiffre trouvé à la liste des invalid.
            $this->aInvalidCiffer[] = $this->aValidCiffer[$iCiffer-1];
            $this->invalidCiffer .= $this->validCiffer[$iCiffer-1];
            // retire le chiffre trouvé de la liste
            unset($this->aValidCiffer[$iCiffer-1]);
            $this->aValidCiffer = array_values($this->aValidCiffer);
            substr($this->validCiffer, $iCiffer-1, 1);
            
        }      
        
        $this->log('TO FIND : '.$this->to_find);
        $this->log('FOUND   : '.$this->current_value);
        
    } 
}

//C:\Users\msa>cd /php
//C:\php>php c:\var\www\html\Nevermind\src\nevermind.php

$NM = new neverMind();
$NM->init();
//$NM->start();
$NM->test_ciffers();
$NM->test_positions();

echo "Finish"
?>


