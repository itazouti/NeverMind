<?php
class nevermind
{
    //private $urlStart;
    //private $urlTest;
    private $token;
    private $name;
    private $quizz_id;
    private $size;
    private $to_find;
    private $aNumberStatus;
    private $current_value;
    private $count_call_api;
    private $noInvalidCiffer;
    private $aValidCiffer = array();
    //private $validCiffer = "";
    private $aInvalidCiffer = array();
    //private $invalidCiffer = "";
    private $iSmallerOccurence;
    private $good;
    
    function __construct() {
        //file_put_contents('NM.log', "");
        //$this->urlStart = "http://172.16.37.129/api/start";
        //$this->urlTest = "http://172.16.37.129/api/test";
        $this->token = "tokennm";
        $this->name = "NeverMind";
        $this->current_value = '';
        $this->aValidCiffer = array();
        $this->aInvalidCiffer = array();
        //$this->validCiffer = '';
        //$this->invalidCiffer = '';
        $this->aNumberStatus = array();
        $this->count_call_api = 0;
        $this->noInvalidCiffer = false;
        $this->iSmallerOccurence = 0;
        $this->good = 0;
    }


    public function init() {
        $this->size = 8;
        $this->quizz_id = 1;
        $random = '';
        for($i=0;$i<$this->size;$i++) {
            $random .= rand(0, 9); //"12345";
        }
        //$this->to_find = $random; //str_pad($random, $this->size, "0", STR_PAD_LEFT);
        //$this->to_find = '135792468013579';
        //$this->to_find = '34680818';
        $this->to_find = '53375480'; //for test
        //$this->to_find = '217038454166809';
        if(!empty($this->to_find)) {
            $this->log("TO FIND:".$this->to_find);
        }
    }
    
    public function start() {
        //send start
        $json_result = $this->send_start();
        $result = json_decode($json_result,true);
        $this->size = (isset($result['size'])) ? $result['size'] : $this->size;
        $this->quizz_id = $result['quizz_id'];
    }
    
    private function log($str) {
        //file_put_contents('NM.log', $str."\n", FILE_APPEND);
        echo $str."\n";
    }
    
    private function send_start() {
        $url = "http://172.168.0.100:8000/api/start";
        //$url = "http://172.16.37.129/api/start";
        
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
        $this->log("RESPONSE => ".$json);
        
    	return $json;
    }
    
    private function send_test() {
        $url = "http://172.168.0.100:8000/api/test";
        //$url = "http://172.16.37.129/api/test";
        
        $this->count_call_api++;
        
        if ($this->current_value == $this->to_find){
            $this->log("FOUND : ".$this->current_value);
            exit();    
        }

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
        
        $this->log("SEND TEST : [".$this->current_value."]");
        $this->log("RESPONSE => ".$json);
        
    	return $json;
    }
        
    private function test_result() {
      
        $this->count_call_api++;
        
        $good = 0;

        $aCurVal = str_split($this->current_value);
        $aToFind = str_split($this->to_find);
        
        // FIND GOOD AND FIND WRONG PLACE
        for($i=0;$i<strlen($this->to_find);$i++) {
            $wrong_place = 0;
            if($aCurVal[$i] == $aToFind[$i]){
                //$this->log("I: ".$i." CURVAL:".$aCurVal[$i]." == TOFIND:".$aToFind[$i] );
                $good++;
            } else {
                //$this->log("I: ".$i." CURVAL:".$aCurVal[$i]." != TOFIND:".$aToFind[$i]);
                
                for($j=0;$j<strlen($this->to_find);$j++) {
                    
                    if($aCurVal[$i] == $aToFind[$j]){
                        //$this->log(">> J: ".$j." CURVAL:".$aCurVal[$i]." == TOFIND:".$aToFind[$j]);
                        $wrong_place++;
                    } 
                    //else {
                        //                       $this->log(">> J: ".$j." CURVAL:".$aCurVal[$i]." != TOFIND:".$aToFind[$j]);
                    //}    
                }
            }
        }
        $this->log("SEND TEST : [".$this->current_value."]");

        $json = json_encode(array("good"=>$good,"wrong_place"=>$wrong_place));
        $this->log("RESPONSE => ".$json);
        
        return $json;
    }
    
    private function check() {
        if($this->size == $this->good) {
            $this->stat();
            exit;
        }
        return false;
    }
    
    private function stat() {
        $this->log('SIZE : '.$this->size);
        $this->log('GOOD : '.$this->good);
        if(!empty($this->to_find)) {
            $this->log('TO FIND : '.$this->to_find);
        }
        $this->log('FOUND   : '.$this->current_value);
        $this->log('COUNT ITERATION : '.$this->count_call_api);
    }
    
    public function test_ciffers() {
    
        $this->log('TEST CIFFERS');
        
        $count_good = 0;
        $this->iSmallerOccurence = 0;
        $smallerOccurence = $this->size+10;
        
        for($i=0;$i<10;$i++) {
    
            $this->good = 0;
            $wrong_place = 0;
    
            $this->current_value = str_repeat("$i",$this->size);
            //send test
            if ($this->current_value == $this->to_find) {
                $this->stat();
                exit;
            }
            
            $json_result = $this->send_test();
            //$json_result = $this->test_result();
            $result = json_decode($json_result,true);
            $this->good = $result['good'];
            $wrong_place = $result['wrong_place'];
            //$this->log("VALUE: ".$this->current_value." GOOD:".$this->good);
            $this->aNumberStatus[] = $this->good;
            if(isset($result['Error']) && !empty(isset($result['Error']))) {
                $this->log("Error Ciffers");
                break;
            }
            
            if($this->check()) {
                $this->stat();
                exit;
            }
    
            if($this->good==0) {
                $this->aInvalidCiffer[] = $i;
                //$this->invalidCiffer .= $i;
            }
            
            for($j=0;$j<$this->good;$j++) {
                $count_good++;
                $this->aValidCiffer[] = $i;
                //$this->validCiffer .= $i;
                if($this->good < $smallerOccurence) {
                    $smallerOccurence = $this->good;
                    $this->iSmallerOccurence = $i;
                }
            }
            
            //IF ALL GOOD => EXIT
            if($count_good == $this->size) {
                $this->log("ADD rest of invalid that not exist in array valid");
                break;
            }
            
            
        }

        // Si pas de chiffre invalide, on ajoute le chiffre avec le moins d'occurence
        if (empty($this->aInvalidCiffer)) {
                $this->noInvalidCiffer = true;
                $this->aInvalidCiffer[] = $this->iSmallerOccurence;
                //$this->invalidCiffer .= $this->iSmallerOccurence;
        }
        
        $this->log('VALID CIFFERS : '.implode(',',$this->aValidCiffer));
        $this->log('STATUS VALID CIFFERS: '.implode(",",$this->aNumberStatus));
        $this->log('INVALID CIFFERS : '.implode(',',$this->aInvalidCiffer));
        
        return;
    }
    
    public function test_positions_good() {
        
        $this->log('TEST POSITIONS');
        
        $aUniqueValidCiffer = array_unique($this->aValidCiffer);
        
        $this->current_value = str_repeat($this->aInvalidCiffer[0],$this->size);
        
        if($this->noInvalidCiffer) {
            $previous_good = $this->aNumberStatus[$this->iSmallerOccurence];
            $previous_value = "";
        } else {
            $previous_good = 0;
            $previous_value = "";
        }
        
        //test chaque chiffre puis passe a la position suivante lorsque goot est incrï¿½mentï¿½
        for($pos=0;$pos<$this->size;$pos++) {
            
            $iCiffer = 0;
            
            do {
                $this->log($this->count_call_api.'###########################################');
                $this->log('POSITION : '.$pos.' ICIFFER : '.$iCiffer.' VALID : '.implode(',',$this->aValidCiffer));
                
                // test ciffer ï¿½ la position pos
                $this->current_value[$pos] = $this->aValidCiffer[$iCiffer];

                if ($this->current_value == $this->to_find) {
                    $this->stat();
                    exit;
                }
                
                //send test
                //$json_result = $this->send_test();
                $json_result = $this->test_result();
                $result = json_decode($json_result,true);
                $this->good = $result['good'];
                
                if(isset($result['Error']) && !empty(isset($result['Error']))) {
                    exit;
                }

                if($this->check())
                {
                    $this->stat();
                    exit;
                }
                
                $iCiffer++;
                
                $this->log('PREV GOOD :'.$previous_good.' GOOD :'.$this->good.' POS : '.$pos.' ICIF : '.$iCiffer.' VALID : '.implode(",",$this->aValidCiffer).' INVALID : '.implode(",",$this->aInvalidCiffer));
                
                if ($previous_good > $this->good) {
                    if (empty($previous_value)) {
                        echo "PREVIOUS EMPTY\n";
                        $previous_good = $this->good;
                    } else {
                        $iCiffer--;
                        $this->current_value  = $previous_value;
                        break;
                    }
                }
                
                $previous_value = $this->current_value;
                
            //} while( $this->good == $pos );
            //} while( $pos == $pos + $this->good - $this->aNumberStatus[$this->aValidCiffer[$iCiffer-1]] );
            } while($previous_good == $this->good);
            
            
            $previous_good = $this->good;
            
            // ajouter le chiffre trouvï¿½ ï¿½ la liste des invalid.
            $this->aInvalidCiffer[] = $this->aValidCiffer[$iCiffer-1];
            //$this->invalidCiffer .= $this->validCiffer[$iCiffer-1];
            
            // retire le chiffre trouvï¿½ de la liste
            unset($this->aValidCiffer[$iCiffer-1]);
            $this->aValidCiffer = array_values($this->aValidCiffer);
            //substr($this->validCiffer, $iCiffer-1, 1);
            
        }      
        
        $this->stat();
        exit;
    } 
    
    public function test_positions() {
    
        $this->log('TEST POSITIONS');
    
        $aUniqueValidCiffer = array_unique($this->aValidCiffer);
        $aUniqueValidCiffer = array_values($aUniqueValidCiffer);
        
        $this->current_value = str_repeat($this->aInvalidCiffer[0],$this->size);
    
        if($this->noInvalidCiffer) {
            $previous_good = $this->aNumberStatus[$this->iSmallerOccurence];
            $previous_value = "";
            $previous_ciffer = "";
        } else {
            $previous_good = 0;
            $previous_value = "";
            $previous_ciffer = "";
        }
    
        //test chaque chiffre puis passe a la position suivante lorsque goot est incrémenté
        for($pos=0;$pos<$this->size;$pos++) {
    
            $iCiffer = 0;
            $ciffer_to_test = '';
            
            $aUniqueValidCiffer = array_unique($this->aValidCiffer);
            $aUniqueValidCiffer = array_values($aUniqueValidCiffer);
            
            do {
                $this->log($this->count_call_api.'###########################################');
                $this->log('POSITION : '.$pos.' ICIFFER : '.$iCiffer.' VALID : '.implode(',',$this->aValidCiffer).' UNIQUE : '.implode(',',$aUniqueValidCiffer));
    
                // test ciffer à la position pos
                $ciffer_to_test = $aUniqueValidCiffer[$iCiffer];
                $this->current_value[$pos] = $ciffer_to_test;
                
                if ($this->current_value == $this->to_find) {
                    $this->stat();
                    exit;
                }
    
                //send test
                $json_result = $this->send_test();
                //$json_result = $this->test_result();
                $result = json_decode($json_result,true);
                $this->good = $result['good'];
    
                if(isset($result['Error']) && !empty(isset($result['Error']))) {
                    exit;
                }
    
                if($this->check())
                {
                    $this->stat();
                    exit;
                }
    
                $iCiffer++;
    
                $this->log('PREV GOOD :'.$previous_good.' GOOD :'.$this->good.' POS : '.$pos.' PCIF : '.$previous_ciffer.' CIF : '.$ciffer_to_test.' ICIF : '.$iCiffer.' VALID : '.implode(",",$this->aValidCiffer).' INVALID : '.implode(",",$this->aInvalidCiffer));
    
                if ($previous_good > $this->good) {
                    if (empty($previous_value)) {
                        echo "PREVIOUS EMPTY\n";
                        $previous_good = $this->good;
                    } else {
                        $iCiffer--;
                        $this->current_value  = $previous_value;
                        $ciffer_to_test = $previous_ciffer;
                        break;
                    }
                }
    
                $previous_value = $this->current_value;
                $previous_ciffer = $ciffer_to_test;
                
            } while($previous_good == $this->good);
    
            $previous_good = $this->good;
    
            // ajouter le chiffre trouvé à la liste des invalid.
            $this->aInvalidCiffer[] = $ciffer_to_test;
    
            // retire le chiffre trouvé de la liste
            if (($key = array_search($ciffer_to_test, $this->aValidCiffer)) !== false) {
                unset($this->aValidCiffer[$key]);
            }
            $this->aValidCiffer = array_values($this->aValidCiffer);
        }
    
        $this->stat();
        exit;
    }
}
?>
