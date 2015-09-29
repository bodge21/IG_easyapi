<?php
class ig_api {
   
    function __construct() {
       // construct
        require_once("config.php");
    }

    function login($user="demo"){
        switch($user){
            case "demo":
                $user = DEMO_USER;
                $pass = DEMO_PASS;
                $apikey = DEMO_APIKEY;
                $scenario = "demo";
                break;
           case "live":
                $user = LIVE_USER;
                $pass = LIVE_PASS;
                $apikey = LIVE_APIKEY;
                $scenario = "live";
                break;
            default :
                echo "Error... User not found";
        }
        if ($apikey){
            $this->login_ig($user,$pass,$apikey,$scenario);
        }
    }
    
    function login_ig($user,$pass,$apikey,$scenario="demo"){
        if ($scenario=="demo"){ 
            $url = 'https://demo-api.ig.com/';
        }else{
            $url = 'https://api.ig.com/';
        }
        
        $data = array("identifier" => $user, "password" => $pass);
        $data_string = json_encode($data);
         
        $ch = curl_init($url."gateway/deal/session/");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json; charset=UTF-8',
            'X-IG-API-KEY: '.$apikey) );
         
        $result = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $header_arr = explode("\n",$header);
        for ($i=0;$i<count($header_arr);$i++){
            $var_arr = explode(":",$header_arr[$i]);
            $var_name = trim($var_arr[0]);
            if (trim($var_arr[0]) == "CST"){ $cst = trim($var_arr[1]); }
            if (trim($var_arr[0]) == "X-SECURITY-TOKEN"){ $xtoken = trim($var_arr[1]); }
        }
      
        curl_close($ch);

        $_SESSION['api_cst'] = $cst;
        $_SESSION['api_xtoken'] = $xtoken;
        $_SESSION['api_date'] = time();
        echo "API TOKEN : ".$_SESSION['api_xtoken'];

        $return_array['api_cst'] = $cst;
        $return_array['api_xtoken'] = $xtoken;
        $return_array['api_date'] =  time();




        return $return_array;
    }

    function __destruct() {
      // destruct
    }
}

$ig = new ig_api();

$ig->login("demo");

?>

