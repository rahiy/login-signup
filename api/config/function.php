<?php
require_once("phpmailer-master/class.phpmailer.php");
require_once("config.php");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

/*
  Common Constant Defined
*/

define("BASE_HTTP_URL"   , "http://aarvtech.com/bottleparties/admin/");		//print_r(BASE_HTTP_URL);
define("IMG_URL"   , "http://aarvtech.com/bottleparties/admin/");		//print_r(BASE_HTTP_URL);
//define("IMG_URL"   , "http://192.168.1.188/bottleparties/admin/");		//print_r(BASE_HTTP_URL);
define("FORGETPASSWORD"   , "http://aarvtech.com/bottleparties/admin/forgetpassword.php?cmFodWx3YWxpYQ=");		//print_r(BASE_HTTP_URL);

define("BASE_SERVER_URL" , dirname(dirname(__FILE__)));		//print_r(BASE_SERVER_URL);

define("APP_NAME" ,'mealy');

define("MAIL_FROM_EMAIL" ,'Support@'.APP_NAME.'.com');

define("SUCCESS_CODE"  ,200);

define("ADMIN_EMAIL"  , "testcqlsys90@gmail.com");

define("FAILURE_CODE"  ,400);

define("DEFAULT_IMAGE"  ,'');

define("GOOGLE_API_KEY", "AIzaSyAhOdSBg3CUZBX9U41hXve9vshiY5jrcJc"); // Place your Google API Key


define('ACCOUNT_SID', 'AC2f55ca95ea22b0f12b1fe5714f9112ad');
define('AUTH_TOKEN', '39a721f3401b3a23a0e16cb4c4c487d2');
define('SENDING_NUMBER', '+1202-858-0855'); 



//Notification Code






/**
 Checking if Variable Exist
*/

function  assignDefault(&$var , $def = ''){

  return ( isset($var) || trim($var) != "") ? $var : $def;

}


function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}


/**
 Required Field Validation
*/

function checkRequired( $requiredArray){

  $validationArray = array();

  foreach($requiredArray as $key=>$value){

    if(trim($requiredArray[$key]) == ""){

	   $validationArray[]= $key." field is required";
	     //$validationArray[]= " Please select Location";

	}

  }


  return $validationArray;

}

/**
  Database Exist Check
*/

function dbExist($value , $field_name , $table_name , $type = '=' , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error121 in the query while looking for field in database') {
	global $mysqli;
	 $_sql1="SELECT
					 *
					FROM
					  $table_name
					WHERE
					  $field_name = '".$value."'
					LIMIT 1 ";
		$__sql =  mysqli_query($mysqli,$_sql1);


		if(!$__sql){

			$status['code'] = $faliureCode;

			$status['message'] = $failureMessg;

			sendResponse($status);

		}

		return (mysqli_num_rows($__sql)>0);
		//return ($_sql1);

}



function dbExistEmail($email , $table_name , $type = '=' , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error in the query 1 while looking for field in database') {
		global $mysqli;
		$condition="email='$email' ";
		$sql1 = "SELECT
					 *
					FROM
					  $table_name
					WHERE
						$condition
					LIMIT 1 ";

		$sql= mysqli_query($mysqli,$sql1);


		if(!$sql){

			$status['code'] = $faliureCode;

			$status['message'] = $failureMessg;

			sendResponse($status);

		}

		return (mysqli_num_rows($mysqli,$sql)>0);

}




function dbExist_auth($id , $token ,$table, $filed , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error in the query while looking for field in database') 
{
		global $mysqli;
    	 $query="SELECT
					 *
					FROM
					 $table
					WHERE
					  auth_token ='$token'
					LIMIT 1 ";
		 $sql =  mysqli_query($mysqli,$query);


		if(!$sql){

			$status['code'] = $faliureCode;

			$status['message'] = $failureMessg;

			sendResponse($status);

		}

		return (mysqli_num_rows($mysqli,$sql)>0);
		//return ($query);

}


function check_newpass_exist($email,$otp , $table_name , $type = '=' , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error in the query while looking for field in database') {

			 $sql =  mysqli_query("SELECT
					 *
					FROM
					  $table_name
					WHERE
					  `email` $type '".$email."' AND `otp` $type '".$otp."'
					LIMIT 1 ");


		if(!$sql){

			$status['code'] = $faliureCode;

			$status['message'] = $failureMessg;

			sendResponse($status);

		}

		return (mysqli_num_rows($sql)>0);

}




function check_locationExist($user_id,$loc_type,$faliureCode =  FAILURE_CODE , $failureMessg = 'Error in the query while looking for field in database') {

			 $sql =  mysqli_query("SELECT
					 *
					FROM
						".FAV_LOCATION."
					WHERE
					  `user_id` = '".$user_id."' AND `loc_type` = '".$loc_type."'
					LIMIT 1 ");

		if(!$sql){

			$status['code'] = $faliureCode;

			$status['message'] = $failureMessg;

			sendResponse($status);

		}

		return (mysqli_num_rows($sql)>0);

}



function getmail($params = []) {
   	$to=$params['to'];
	$subject=$params['subject'];
	$msg=$params['body'];
	$response=sendmail2($subject,$msg,$to);
//	$response=1;
	return $response;
	//die;
}

/**
*
* Get a Proper Phone number, remvoing what space etc
*
*/

function getDigitPhoneNumber(&$num, $key){

   $num  =substr(preg_replace("/[^0-9]/","",$num) ,- 10);


}



/**
 * Actual Function Which Send Output to the App
 *
 *
 * $status is a array containing the status code and message
 * $status = array(
 *              'code'=> ,
 *              'message'=> ,
 *          )
 *
 * $body contain the actual json send to the app
 *
 *
*/


function sendResponse( $status,$body =''  ){

    $responseArray= $status;

/* 	$responseArray['status']['code'] = (string )$responseArray['status']['code']; */

    $responseArray['body'] = $body;
    // $responseArray['gallery'] = array("sa","s");


  $json = json_encode($responseArray);

  echo $x = preg_replace('/\\\r\\\n|\\\r|\\\n\\\r|\\\n/m', ' ', $json);

    die();

}



function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

/**
  Setting Push Notication to Users


   $device_type : type of device ios or android
   $user_id     : array of user to whom push notification need to be send
   $message     : the message to be displayed in notifcation bar
   $notification_code : type of notification
   $requestType   : extra information with the notification


*/
 function sendPushNotiFication( $notiArray , $message, $notification_code = 0 ,$body) {



    $notifDbDetails = array(
							"body"			=> $body,
                            "message"      => $message     ,
                            "title"        => "Bottle Party Notification",
                            "msgcnt"       => "1"          ,
                            "soundname"    => "beep.wav"   ,
                            "timeStamp"    => time()       ,
                            "notification_code"  => $notification_code
	);
    //echo "<pre>"; print_r($notifDbDetails);
   
	$userResultSet3 ="SELECT `device_type`,`device_token` FROM users WHERE `id` = '".$notiArray."'";
	$queryResult3  = executeQuery( $userResultSet3, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	$ds=$queryResult3;	

 $device_id=$ds['device_type'];

     $device_token=$ds['device_token'];

    if($device_id=='1' &&  $device_token!='') {

	
		$deviceToken = $device_token;

		$passphrase = '123456789';

		$message = $message;
		$ctx = stream_context_create();
		$ent=0; //$ent = 0 for sandbox , 1 for live

  if($ent==0){
 //echo "hello";
     stream_context_set_option($ctx, 'ssl', 'local_cert',dirname(__FILE__).'/TravelTag.pem');
     stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
    $fp = stream_socket_client(
                           'ssl://gateway.sandbox.push.apple.com:2195', $err,
                           $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

  }else{
  //	echo "hii";
   stream_context_set_option($ctx, 'ssl', 'local_cert',dirname(__FILE__).'/TravelTag.pem');
   stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
   $fp = stream_socket_client(
                              'ssl://gateway.push.apple.com:2195', $err,
                             $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
  }
			if (!$fp)

			exit("Failed to connect: $err $errstr" . PHP_EOL);

			PHP_EOL;

		$body=$notifDbDetails;

		$body['aps'] = array( 'badge' => '0',
		                      'alert' => $message,
		                      'sound' => 'default',
							  'content-available' => '1'
		);

		$payload = json_encode($body);

		// print_r($payload);

		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		$result = fwrite($fp, $msg, strlen($msg));

		//echo dirname(__FILE__).'/Sch_dev.pem';
		//echo "<pre>"; print_r($result); echo "</pre>"; 
		//die;

		if (!$result)

			PHP_EOL;
		else

			//return 1;

			PHP_EOL;

		fclose($fp);

    } elseif($device_id=='2' && $device_token!=''){

 //die("hiiii");

		$url = 'https://fcm.googleapis.com/fcm/send';

		$fields = array(
		                'registration_ids' => array($device_token),
		                'data'             => $notifDbDetails,
		);
		//print_r($fields);
		// echo '<pre>';
		// echo($fields);

		  $headers = array(
			'Content-Type: application/json',
		   'Authorization: key=AAAAp2NtoG4:APA91bFMuPX7SYkcsgDoY1TMMDagk5ogo2LbG_rGkAbmsqEZi2vAFfDTXZT3SbgnXgRP04NFX73okHLZRuJzBGaow6uaQEKX6xMqyiLbzCVWcw7bYYv-G3OO9i6HWb7rg85AfhyYHVJB'

		  );
		  // Back up - 2018-01-25 - AAAAmmgKBF8:APA91bGsP6QnAnsLLsB0v_LxP2n2E5jdn7SiaS1qR3aTNsP9C1q5dPFAnVe9OQP4NzJxNO2eR2DF1_jgpH_TIkVLfe60wj1RvkWu4wLg8vELjg0FC_MPOcmZRgIjY2ezycLZ227IpyYe
		  //

		 $ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			//curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );

	
			$res=json_decode($result, true);
			print_r($result);
		   

			//return $res['success'];

			curl_close( $ch );
    }
}

/**
 payout
*/

function _process_payout($sender_product_id=0,$product = []) {

   $data = get_access_token();
   // prx($data);
    if ($data) {
        $access_token = $data['access_token'];
    } else {  return false; }
  $sender_batch_header['sender_batch_header'] =
  [
            "sender_batch_id" => $sender_product_id,
            "email_subject" => "You have a new Payment!",
  ];

    $productS['productS'] = [];
    if(count($product))
    {
      $productS['productS'] = $product;
    }

  $final=[];
    $final = array_merge($sender_batch_header, $productS);
    $postdata = $final;
              // json_encode($postdata);
               // die();
               // Setup cURL
                $ch = curl_init('https://api.sandbox.paypal.com/v1/payments/payouts');
                curl_setopt_array($ch,
                                     array(
                                        CURLOPT_POST => TRUE,
                                        CURLOPT_RETURNTRANSFER => TRUE,
                                        CURLOPT_HTTPHEADER => array(
                                            'Authorization: Bearer ' . $access_token,
                                            'Content-Type: application/json'
                                        ),
                                        CURLOPT_POSTFIELDS => '{"sender_batch_header": {"sender_batch_id":"Payouts_2018_100007","email_subject": "You have a payout!","email_message": "You have received a payout! Thanks for using our service!"},"items": [{"recipient_type": "EMAIL","amount":{"value": "9.87",       "currency": "USD" },"note":"Thanks for your patronage!","sender_item_id": "201403140001","receiver":"receiver@example.com"}]}',
                               // CURLOPT_POSTFIELDS => $data )
                                    ));
                // Send the request
                 $response = curl_exec($ch);
                 //prx($response);
                 if ($response === FALSE) {  die(curl_error($ch)); }
                  $responseData = json_decode($response, TRUE);
                   return $responseData;
}

function get_access_token($value = '')
{
// The data to send to the API
  $postData = [
    'grant_type' => 'client_credentials'
  ];


  /*$username = "AbyXCZP2mcpJzEi9VQuvG5OCwxsnX1F3LNvQfKe3YTdUtKb-HrJeGrQtWjZb2grMcUT7qtEYcY-B3bh8";
  $password = "EJxeDYUhQvw1Pr0dRM3VeNYODqPWsfHYvDIfz2e0F4cUmQZ7pmh3omyVSXPCmXft12LcHT3JdtrbxOiy";
*/
  $username = "AV6a7CvL-OwZnbhQIFPCLtsOHmrE15MQDraHf4SWYRvdZZrfaU8VoWGriEjwgvTYAAqbLT8Kx0bLTbeu";
  $password = "EPojTjv1hVuWFKw6a-fCLTZoXyq64zLDdyVFTdFh5Xa30jNn8g8NAifhCsAl6GErNx4z754tQAtYDXs_";


  $ch = curl_init('https://api.sandbox.paypal.com/v1/oauth2/token');
  curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPAUTH, CURLAUTH_BASIC,
    CURLOPT_USERPWD => "{$username}:{$password}",
    CURLOPT_HTTPHEADER => array(
      // 'Authorization: Basic'.$authToken,
      'Content-Type: application/x-www-form-urlencoded'
    ),
    CURLOPT_POSTFIELDS => http_build_query($postData)

      // curl_setopt($ch, CURLOPT_USERPWD, "$username:$password")
  ));

  // Send the request
  $response = curl_exec($ch);
  // prx($response);
  // Check for errors
  if ($response === FALSE) {
    die(curl_error($ch));
  }


  // Decode the response
  $responseData = json_decode($response, TRUE);
  return $responseData;
  // Print the date from the response
  // $responseData['published'];

}


/**
 send push notification to Employee
*/

 function sendPushNotiFicationemp( $notiArray , $message=null , $notification_code = 0 ,$body) {



    $notifDbDetails = array(
			    "body"			=> $body,
                            "message"      => $message     ,
                            "title"        => APP_NAME." Notification",
                            "msgcnt"       => "1"          ,
                            "soundname"    => "beep.wav"   ,
                            "timeStamp"    => time()       ,
                            "notification_code"  => $notification_code
	);

     $deviceQuery = "SELECT `device_type` ,
						   `device_token`
							FROM
								".EMP."
							WHERE
								`emp_id` = '".$notiArray."'
    ";
   
    $result=mysqli_query($deviceQuery);

	$ds=mysqli_fetch_assoc($result);			
 	//echo "<pre>"; print_r($ds); echo "</pre>";
 //die;
 	$device_id=$ds['device_type'];

    $device_token=$ds['device_token'];

    if(($device_id ===  '2' || $device_id === 2) &&  $device_token!='') 
    {
      

		$deviceToken = $device_token;

		$passphrase = '123456789';

		$message = $message;
		//$body = $body;

		$ctx = stream_context_create();

		$ent=1; //$ent = 0 for sandbox , 1 for live 
	

		  if($ent==0)
		  {
			//echo dirname(__FILE__);
		     stream_context_set_option($ctx, 'ssl', 'local_cert',dirname(__FILE__).'/taxiDriver.pem');
		     stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		     $fp = stream_socket_client(
		                           'ssl://gateway.sandbox.push.apple.com:2195', $err,
		                           $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		 
		  }else{

		   stream_context_set_option($ctx, 'ssl', 'local_cert',dirname(__FILE__).'/taxiDriver24July.pem');
		   stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		   $fp = stream_socket_client(
		                              'ssl://gateway.push.apple.com:2195', $err,
		                             $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		         
		  }
		if (!$fp)

			exit("Failed to connect: $err $errstr" . PHP_EOL);

			PHP_EOL;

		$body=$notifDbDetails;
		//print_r($body);

		$body['aps'] = array( 'badge' => '0',
		                      'alert' => $message,
		                      'sound' => 'default',
							  'content-available' => '1'
		);

		$payload = json_encode($body);

	// print_r($payload);
// die;
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		$result = fwrite($fp, $msg, strlen($msg));

		//echo dirname(__FILE__).'/Sch_dev.pem';
		//echo "<pre>"; print_r($result); echo "</pre>"; //die('here');
		//print_r($result);
		if (!$result)

			PHP_EOL;
		else

			//return 1;

			PHP_EOL;

		fclose($fp);

    }elseif($device_id=='1' && $device_token!=''){

		if(count($device_token) > 0 ){

		$url = 'https://fcm.googleapis.com/fcm/send';

		$fields = array(
		                'registration_ids' => array($device_token),
		                'data'             => $notifDbDetails,
		);

		//echo '<pre>';
		//print_r($fields);

		  $headers = array(
			'Content-Type: application/json',
		   'Authorization: key=AAAAgFsXBDg:APA91bEKHxo2qEzB7DlR-LpZrHMvxfk4Gtzswx5Dtda1bheN9n7rhMj2kovMcTsHQQMbxrN9qyzAQ8aVWlM-G5LGYwvdAsV2_qTR8_S7zFbyaiTDOgcuvGg9VnhjJKrZiQRVacRn63WM'

		  );

		 $ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			//curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );

			//print_r($result);
			//$res=json_decode($result, true);

			//return $res['success'];

			curl_close( $ch );
		}
    }
}


/**
 Saving the push notification in the table
*/

function saveNotificationDetails($sender,$receiver,$notification_type,$receiver_type,$nottification_message){

	$query = "INSERT
			INTO
			  ".NOTIFICATION_TABLE."
			   (`sender`, `receiver`, `notification_type`,`receiver_type`,`nottification_time`,`nottification_message`)
			VALUES
			   ('$sender','$receiver','$notification_type','$receiver_type', '".time()."','$nottification_message')
			 ";

	$queryResult  = executeQuery( $query, false , FAILURE_CODE , 'Save Notification Query Execution Failed ' , true);
}


/**

 Random String

*/
function random_string(){
	$character_set_array = array( );
    $character_set_array[] = array( 'count' => 7, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
    $character_set_array[] = array( 'count' => 1, 'characters' => '0123456789' );
    $temp_array = array();
    foreach ( $character_set_array as $character_set )
    {
      for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
      {
        $temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
      }
    }
    shuffle( $temp_array );
    return implode( '', $temp_array );

  }


/*
  This is a debugging function
*/

function fileWriteResponse($custom){
   $file=fopen("response.html","w+");
   fwrite($file, 'Request Data ');
   $output =  print_r($_REQUEST,true);
   $output .= print_r($_FILES,true);
   fwrite($file, $output );
   fwrite($file, $custom );
   fclose($file);
}

function debugAny($var){

   echo '<pre>';

   print_r($var);

   echo '</pre>';

}






/**
  Exceute Data-Base query

  This function has been made to handle the common error associated with the query exection

*/


function executeQuery($query , $multiple = false , $faliureCode = FAILURE_CODE , $failureMessg = 'Query Execution Failed ' , $insertUpdate = false){
	global $mysqli;
   	$commonResuSet  = mysqli_query($mysqli,$query);

	if(!$commonResuSet){

		$status['code'] = $faliureCode;

		$status['message'] = $failureMessg.mysqli_error($mysqli);

		sendResponse($status);

	}


	if($insertUpdate == true){

	  return (mysqli_affected_rows($mysqli) > 0);

	}


	$tempArray = array();

	if($multiple == false){

	   if(mysqli_num_rows($commonResuSet) > 0){

	     return mysqli_fetch_assoc($commonResuSet);

	   }

	    return $tempArray;

	}



	while($row = mysqli_fetch_assoc($commonResuSet)){

	    $tempArray[]  = $row;

	}

	return $tempArray;



}


/**
  Safe Value for DB Use
*/

function getSafeValue(&$item, $key){
		global $mysqli;
   $item  = mysqli_real_escape_string($mysqli, $item);

}

/**
 Get Int Value
*/


function getIntValue(&$item, $key){

   $item  = (int) $item;

}



/**
*
*  Exception Class Extension
*
*/


class specialException extends Exception
{

    public function __construct($message, $code = FAILURE_CODE, Exception $previous = null) {

        parent::__construct($message, $code, $previous);

    }


    public function __toString() {

        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";

    }

	public function toJSON(){

		$status['code'] = (string) $this->code;

		$status['message'] = $this->message;

		sendResponse($status , array());   //returning a empty array , for ease of parsing on mobile end

	}

}
/****************************MAil Function*******************/
function sendmail($to,$headers,$message='random_code' ) {


		if(mail($to,$message, $headers)) {
			return true;
		} else {
            return false;
		}

	}


function getusercard($id,$card)
{

	$userResultSet = "SELECT *
							FROM
								`user_card`
							WHERE
								`user_id` = '$id' and card_no ='$card'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}






function getDigitPhoneNumbers($num){

   return substr(preg_replace("/[^0-9]/","",$num) ,- 10);


}
// Email Validation

function emailvalidation($num){

if (!filter_var($num, FILTER_VALIDATE_EMAIL)) {
  $emailErr = "Invalid email format";
	return true;
}
}
// URL Validation


/*-----------------get user Details by id  -----------------*/




function getDetailsbyph($code, $mobile , $table_name , $type = '=' , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error in the query 2 while looking for field in database') {


		if( $table_name == USER){

			$condition="mobile_code='$code' and user_mobile='$mobile' ";

		}elseif ($table_name == EMP) {

				$condition="mobile_code='$code' and emp_mobile='$mobile' ";

		}

			 $userResultSet = "SELECT *
							FROM
								$table_name
							WHERE
								$condition
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	 if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}

}

function getDetailsbyemail($email , $table_name , $type = '=' , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error in the query 2 while looking for field in database') {

			if( $table_name == USER){

			$condition="user_email='$email' ";

		}elseif ($table_name == EMP) {

				$condition=" emp_mobile='$email' ";

		}

			 $userResultSet = "SELECT *
							FROM
								$table_name
							WHERE
								$condition
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	 if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}

}



function getuserDetailsbyemail($email)
{

	$userResultSet = "SELECT *
							FROM
								`".USER."`
							WHERE
								`email` = '$email'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}




function getview($user_id)
{

	 $userResultSet = "SELECT count(`id`) as total, avg(`rating`) as avgrating,IFNULL(rating,0) as avgrating  from `ub_review`
								where
								`App`='1' and `to`='$user_id' ";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select1211 Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}






function getuserDetailsbyemaild($emp_email)
{

	$userResultSet = "SELECT *
							FROM
								`".EMP."`
							WHERE
								`emp_email` = '$emp_email'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}



function faq()
{

	$userResultSet = " SELECT *	FROM faq";

	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function help()
{

	$userResultSet = " SELECT *	FROM help";

	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function law($cat_id)
{
	$url= IMG_URL;
	$userResultSet = " SELECT id,law_name,category_id,description, concat('$url' ,image) as img_url FROM law where category_id='$cat_id'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	
	//$queryResult['ImgUrl'] = IMG_URL;

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getschedule($user_id,$status)
{
	$userResultSet = " SELECT * FROM tbschedule where user_id='$user_id' and status='$status'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	 //print_r($queryResult);die;
	//$queryResult['ImgUrl'] = IMG_URL;

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getscheduledetail($appointment_id)
{
	$userResultSet = " SELECT * FROM tbschedule where id='$appointment_id'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	 //print_r($queryResult);die;
	//$queryResult['ImgUrl'] = IMG_URL;

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getCategoryList()
{

	$userResultSet = " SELECT id,category_name	FROM Category";

	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getinterest()
{
	$url= IMG_URL;
	$userResultSet = " SELECT id,title,concat('$url' ,image) as img_url	FROM category where status='1'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getallCommunity($id)
{
	$url= IMG_URL;
	$userResultSet = " SELECT id,name,concat('$url' ,image) as img_url	FROM community where status='1' and user_id='$id'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getallreviews($userId)
{
	$url= IMG_URL;
	$userResultSet = "SELECT `id`,`sender_id`,(select username from users where id= sender_id) as sender_user_name, (select concat('$url' ,image) as img_url from users where id= sender_id) as sender_user_img,`comment`,`rating`,UNIX_TIMESTAMP(STR_TO_DATE(`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date FROM review where reciever_id='$userId'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function gethistorytrip($userId)
{
	$url= IMG_URL;
	$userResultSet = "SELECT `id`,(SELECT CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image  FROM `users` WHERE id='$userId')  as userimage,SUBSTRING_INDEX(SUBSTRING_INDEX(location, ',', 2), ',', -1) as location,SUBSTRING_INDEX(SUBSTRING_INDEX(from_location, ',', 2), ',', -1) as from_location,UNIX_TIMESTAMP(STR_TO_DATE(`date_from`, '%Y-%m-%d')) as date_from  ,UNIX_TIMESTAMP(STR_TO_DATE(`date_to`, '%Y-%m-%d')) as date_to ,`number_of_people`,`looking_for`,UNIX_TIMESTAMP(STR_TO_DATE(`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date  FROM trip where  date(date_to) < date(NOW()) and `status`='1' and user_id='$userId'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function gettravelplan($userId)
{
	$url= IMG_URL;
	$userResultSet = "SELECT `id`,`location`,`from_location`,SUBSTRING_INDEX(SUBSTRING_INDEX(location, ',', 4), ',', -1) as country,UNIX_TIMESTAMP(STR_TO_DATE(`date_from`, '%Y-%m-%d')) as date_from  ,UNIX_TIMESTAMP(STR_TO_DATE(`date_to`, '%Y-%m-%d')) as date_to ,`number_of_people`,`looking_for`,UNIX_TIMESTAMP(STR_TO_DATE(`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date  FROM trip where  date(date_to) >=  date(NOW()) and `status`='1' and user_id='$userId' group by country";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function gettravelplandetail($userId,$country)
{
	$url= IMG_URL;
	$userResultSet = "SELECT `id`,SUBSTRING_INDEX(SUBSTRING_INDEX(location, ',',2), ',', -1) as location,SUBSTRING_INDEX(SUBSTRING_INDEX(location, ',',4), ',', -1) as country,SUBSTRING_INDEX(SUBSTRING_INDEX(from_location, ',', 2), ',', -1) as from_location,UNIX_TIMESTAMP(STR_TO_DATE(`date_from`, '%Y-%m-%d')) as date_from  ,UNIX_TIMESTAMP(STR_TO_DATE(`date_to`, '%Y-%m-%d')) as date_to ,`number_of_people`,`looking_for`,UNIX_TIMESTAMP(STR_TO_DATE(`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date  FROM trip where  date(date_to) >=  date(NOW()) and `status`='1' and user_id='$userId' having  country ='$country'";
	
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getuserfriendlist($userid)
{
	$url= IMG_URL;
	$userResultSet = "SELECT CASE WHEN receiver_user_id = '$userid' THEN  sender_user_id WHEN sender_user_id = '$userid' THEN  receiver_user_id  ELSE  '0' END AS userid,(SELECT username FROM `users` WHERE id=userid) as username,(SELECT CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image  FROM `users` WHERE id=userid)  as img_url  FROM `follow` WHERE `sender_user_id`='$userid' or `receiver_user_id`='$userid' and `status`=1";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function userRequestList($userid)
{
	$url= IMG_URL;
	$userResultSet = "SELECT  sender_user_id  AS userid,(SELECT username FROM `users` WHERE id=userid) as username,(SELECT CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image  FROM `users` WHERE id=userid)  as img_url  FROM `follow` WHERE  `receiver_user_id`='$userid' and `status`='0'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function gettradingusers()
{
	$url= IMG_URL;
	$userResultSet = "Select id,username,email,nationality,city,country,phone,latitude,longitude,gender,concat('$url' ,image) as img_url,language,user_type from users where trending_status = '1'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function gettradingnews()
{
	$url= IMG_URL;
	$userResultSet = "Select id,name,description,concat('$url' ,image) as img_url from news where trending_status = '1'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getallpostlikes($postid)
{
	$url= IMG_URL;
	$userResultSet = "SELECT likes.`status`,UNIX_TIMESTAMP(STR_TO_DATE(likes.`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date,likes.post_id,users.username,concat('$url' ,users.image) as img_url	FROM likes JOIN users ON likes.sender_id=users.id WHERE likes.post_id=1";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	} else{

		return false;
	}
}
function getallpostcomments($postid)
{
	$url= IMG_URL;
	$userResultSet = "SELECT comment.`comment`,UNIX_TIMESTAMP(STR_TO_DATE(comment.`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date,comment.post_id,users.username,CASE WHEN users.image !='' THEN  concat('$url' ,users.image) ELSE '' END as img_url	FROM comment JOIN users ON comment.sender_id=users.id WHERE comment.post_id='$postid'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getdiscoverUsers($lat,$long,$auth_token)
{
	$q = "SELECT id from users where `auth_token`='$auth_token'";
	 $r  = executeQuery( $q, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	$url= IMG_URL;
	$userResultSet = "SELECT id,username,concat('$url' ,image) as profileimage,ROUND((((acos(sin(('$lat'*pi()/180)) * sin((`latitude`*pi()/180))+cos(('$lat'*pi()/180)) * cos((`latitude`*pi()/180)) * cos((('$long'- `longitude`) *pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance FROM `users` where id != ". $r['id']." having distance <=70";
	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	
	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function searchuser($userId,$auth_token,$lat,$long,$date)
{
	$url= IMG_URL;
	$userResultSet = "SELECT trip.location, trip.latitude, trip.longitude,trip.from_lat,trip.from_lng,trip.from_location,trip.number_of_people,UNIX_TIMESTAMP(STR_TO_DATE(trip.date_from, '%Y-%m-%d')) as date_from,UNIX_TIMESTAMP(STR_TO_DATE(trip.date_to, '%Y-%m-%d')) as date_to,users.id,users.username,CASE WHEN users.image !='' THEN  concat('$url' ,image) ELSE '' END AS userimage,CASE WHEN users.coverimage !='' THEN  concat('$url' ,coverimage) ELSE '' END AS coverimage,users.user_type,users.phone,CASE WHEN users.id = '$userId' THEN  1  ELSE  0 END AS userid,ROUND((((acos(sin(('$lat'*pi()/180)) * sin((trip.latitude*pi()/180))+cos(('$lat'*pi()/180)) * cos((trip.latitude*pi()/180)) * cos((('$long'- trip.longitude) *pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance FROM trip JOIN users ON trip.user_id=users.id where  MONTH(trip.date_from) >= '$date' AND MONTH(trip.date_from) <= '$date' having distance <=70";
	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	
	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function searchguide($userId,$auth_token,$lat,$long,$date)
{
	$url= IMG_URL;
	$userResultSet = "SELECT users.id as userid,users.username,CASE WHEN users.image !='' THEN  concat('$url' ,image) ELSE '' END AS userimage,CASE WHEN users.coverimage !='' THEN  concat('$url' ,coverimage) ELSE '' END AS coverimage,users.user_type,users.phone,(SELECT  count(*)  as packagecount FROM package where user_id=userid) as packagecount,(SELECT  IFNULL(MIN(`price`),'0') as minprice  FROM package where user_id=userid) as minprice,(SELECT  IFNULL(MAX(`price`),'0') as maxprice  FROM package where user_id=userid) as maxprice,(SELECT CASE WHEN ROUND(AVG(rating),2) IS NULL THEN '0' ELSE ROUND(AVG(rating),2) END FROM `review` where `reciever_id`=userid) as rateavg,(SELECT CASE WHEN count(*) IS NULL THEN '0' ELSE count(*) END FROM `review` where `reciever_id`=userid) as reviwecount,ROUND((((acos(sin(('$lat'*pi()/180)) * sin((package.latitude*pi()/180))+cos(('$lat'*pi()/180)) * cos((package.latitude*pi()/180)) * cos((('$long'- package.longitude) *pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance FROM package JOIN users ON package.user_id=users.id where users.user_type=2 and users.id !='$userId' group by users.id  having distance <=70 ";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getallnoti($userId)
{
	$url= IMG_URL;
	$userResultSet = "SELECT `reciver_id`,noti_msg,noti_type,data,UNIX_TIMESTAMP(STR_TO_DATE(`created_at`, '%Y-%m-%d %H:%i:%s')) as upload_date FROM user_notification where reciver_id='$userId'";
	$queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);
/*foreach ($queryResult as $key => $value) {
		$new_queryResult[$key] = $value;
		$new_queryResult[$key] += json_decode($value['data'],true);
		unset($new_queryResult[$key]['data']);
	}*/
	if($queryResult > 0 ){
		return $new_queryResult;

	}else{

		return false;
	}
}

function getAllpost($userId,$auth_token)
{

	$url= IMG_URL;
	$userResultSet = "SELECT users.id as uid,users.username as uname,concat('$url',users.image) as uimage ,(select count(0) from comment where post_id=post.id) as totalcomment,(select count(0) from likes where post_id=post.id and status=1) as totallike, post.id as post_id, post.content as desce ,UNIX_TIMESTAMP(STR_TO_DATE(post.created_at, '%Y-%m-%d %H:%i:%s')) as upload_date FROM users JOIN post ON users.id=post.user_id  where post.user_id in (select receiver_user_id from follow where sender_user_id='$userId' and status=1 union all select sender_user_id from follow where receiver_user_id ='$userId' and status=1 union all select '$userId') and post.status=1 ORDER BY post.created_at DESC";

	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function law_detail($id)
{
	$url= IMG_URL;
	$userResultSet = " SELECT *, '$url' as img_url FROM law where id='$id'";

	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function auth_token($emp_id)
{

	echo $userResultSet = " SELECT *
							FROM
								`".EMPAUTH."`  where id= $emp_id";

	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}



/*-----------------get EMP Details by id  -----------------*/

function getemployeDetails($id)
{

	$userResultSet = "SELECT *
							FROM
								`".EMP."`
							WHERE
								`emp_id` = '$id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}


/*-----------------get user Details by id  -----------------*/

function getuserDetails($id)
{

	$userResultSet = "SELECT *
							FROM
								users
							WHERE
								`id` = '$id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function getuserDetails2($id)
{

	$userResultSet = "SELECT *
							FROM
								guide_detail
							WHERE
								`user_id` = '$id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function getuserpackdetail($id)
{

	$userResultSet = "SELECT *
							FROM
								package
							WHERE
								`id` = '$id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function getuserDetailsbyauth($auth_token)
{

	$userResultSet = "SELECT *
							FROM
								users
							WHERE
								`auth_token` = '$auth_token'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}


function getuserid($emp_id)
{

	$userResultSet = "SELECT `".EMP."`.* ,`".EMPAUTH."`.auth_token
							FROM `".EMP."`
							join ".EMPAUTH." on
							`".EMP."`.emp_id=`".EMPAUTH."`.emp_id
							WHERE
								`".EMP."`.`emp_id` = '$emp_id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function getemp($emp_id)
{

	$userResultSet = "SELECT username as emp_name ,emp_image
							FROM
								`".EMP."`
							WHERE
								`emp_id` = '$emp_id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}



/*-----------------get request Details by id  -----------------*/


function getuserDetail($id)
{

	$userResultSet = "SELECT  *
							FROM
								`".EMP."`
							WHERE
								`emp_id` = '$id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}


/*-----------------get request Details by id  -----------------*/


function get_requestID_Details($id)
{

	$userResultSet = "SELECT *
							FROM
								`".REQUEST."`
							WHERE
								`request_id` = '$id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}




function getdata($emp_id)
{

	$userResultSet = "SELECT *
							FROM
								`".REQUEST."`
							WHERE
								`emp_id` = '$emp_id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}





function get_requestID_Detail($requestID)
{

	$userResultSet = "SELECT *
							FROM
								`".REQUEST."`
							WHERE
								`request_id` = '$requestID'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}


function request_calcle()
{

	$userResultSet = "SELECT *
							FROM
								`".REQUEST."`
							WHERE
								`status` = 0
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}




function get_request_Detail($emp_id)
{

	 $userResultSet = "SELECT *
							FROM
								`".REQUEST."`
							WHERE
                 emp_id = '$emp_id' and
								`status` in (3,4)
								";
	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}




function get_request_Details($user_id)
{


	   $userResultSet = "SELECT *
							FROM
								`".REQUEST."`
							WHERE
								`status`in (3,4) and user_id= '$user_id'
								";
	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}




function pafast($user_id)
{


	  $userResultSet = "SELECT *
							FROM
								payfast
							WHERE   user_id= '$user_id' order by id desc limit 1
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}



function pafast_emp($emp_id)
{


	  $userResultSet = "SELECT *
							FROM
								payfast
							WHERE   emp_id= '$emp_id' order by id desc limit 1
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}








function last_trip_user($user_id)
{


	 $userResultSet = "SELECT * FROM `".REQUEST."` where status= 4 and user_id= '$user_id' order by request_id desc limit 1
								";
	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}







function get_payment_Details($user_id)
{


 	 $userResultSet = "SELECT *
							FROM
								`".PAYMENT."`
							WHERE
								 user_id = $user_id
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}



function panding($emp_id)
{


 	 $userResultSet = "SELECT *
						FROM
								`".REQUEST."`
							WHERE
								 status = 0 and emp_id= '$emp_id'
								";
	 $queryResult  = executeQuery( $userResultSet, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}




function get_payment_Detail()
{


 	  $userResultSet = "SELECT *
							FROM
								`".PAYMENT."`

								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function get_amount($emp_id)
{


 	   $userResultSet = "SELECT *
							FROM
								`".INVOICE."` where emp_id= '$emp_id'

								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}
	elseif($queryResult = null ){

		return 0;
	}
	else{

		return false;
	}
}




function GetDrivingDistanceAndtime($lat1, $lat2, $long1, $long2)
{
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    // print_r($response);die;
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = @$response_a['rows'][0]['elements'][0]['distance']['text'];
    $time = @$response_a['rows'][0]['elements'][0]['duration']['text'];

    return array('distance' => $dist, 'time' => $time);
}




function Get_invoice(){
			 $time= time()-24*60*60*7;
			   //$time=$time-24*60*60;

	 $sql = "SELECT * FROM `".INVOICE."` WHERE created <= ".time()." and created>='$time' ";
	$queryResult  = executeQuery( $sql, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){


		return $queryResult;

	}else{

		return false;
	}
}

function withdral($emp_id){


	 $sql = "SELECT * FROM `withdral` where emp_id= $emp_id";
	$queryResult  = executeQuery( $sql, true , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){


		return $queryResult;

	}else{

		return false;
	}
}











function Get_invoice1($emp_id){
			 $time= time()-24*60*60*30;
			   //$time=$time-24*60*60;

	 $sql = "SELECT * FROM ub_invoice WHERE created <= ".time()." and created>='$time' and emp_id='$emp_id' ";
	$queryResult  = executeQuery( $sql, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		$review = "SELECT SUM(total_amt) as monthly_amount,IFNULL(total_amt,0) as monthly_amount FROM `ub_invoice` WHERE created <= ".time()." and created>='$time' and emp_id='$emp_id'
								";
	 $queryreview  = executeQuery( $review, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
			//print_r( $queryreview);
		if(empty($queryreview)){
				$queryreview['monthly_amount']='0';

			}

		return array_merge($queryResult,$queryreview);





	}else{

		return false;
	}
}


function Get_invoice11($emp_id){
			$time= time()-24*60*60*7;
			   //$time=$time-24*60*60;

	 $sql = "SELECT * FROM ub_invoice WHERE created <= ".time()." and created >='$time' and emp_id='$emp_id' ";
	$queryResult  = executeQuery( $sql, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

	$review = "SELECT SUM(total_amt) as weekly_amount FROM `ub_invoice` WHERE created <= ".time()." and created>='$time' and emp_id='$emp_id'
								";
	 $queryreview  = executeQuery( $review, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
			//print_r( $queryreview);

		if(empty($queryreview)){
				$queryreview['weekly_amount']='0';

			}

		return array_merge($queryResult,$queryreview);





	}else{

		return false;
	}
}


function accepted_request($emp_id)
{


 	  $userResultSet = "SELECT count(*) as request_accepted
							FROM
								`".REQUEST."` where status in(1,3,4) and emp_id= $emp_id

								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}

function trip_canceled($emp_id)
{


 	  $userResultSet = "SELECT count(*) as trip_canceled
							FROM
								`".REQUEST."` where status = 2 and emp_id='$emp_id'
								";
	 $queryResult  = executeQuery( $userResultSet, false , FAILURE_CODE , 'Select12 Query Execution Failed ' , false);

	if($queryResult > 0 ){

		return $queryResult;

	}else{

		return false;
	}
}
function sendmail2($subject,$msg,$to) 
{
			
			$count=0;
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth = TRUE;
			$mail->SMTPSecure = "tls";
			$mail->Port     = 587;  
			$mail->Username = "btlparties@bottleparties.com";
			$mail->Password = "btlparties@123@";
			$mail->Host = 'mail.bottleparties.com';
			$mail->setFrom('btlparties@bottleparties.com', 'Admin');
			$mail->AddAddress($to);
			$mail->Subject =$subject;
			$mail->WordWrap   = 80;
			$mail->MsgHTML($msg);
			$mail->IsHTML(true);

			if(!$mail->Send()) 
			{
				$count=0;
			}
			else
			{
				$count=1;
			}
			return $count;
}

function countactus($subject,$msg,$from,$name) 
{
			
			$count=0;
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth = TRUE;
			$mail->SMTPSecure = "ssl";
			$mail->Port     = 465;  
			$mail->Username = "";
			$mail->Password = "";
			$mail->Host = '';
			$mail->setFrom($from, $name);
			$mail->AddReplyTo($from, $name);
			$mail->AddAddress('rahulwalia@cqlsys.co.uk');
			$mail->Subject =$subject;
			$mail->WordWrap   = 80;
			$mail->MsgHTML($msg);
			$mail->IsHTML(true);

			if(!$mail->Send()) 
			{
				$count=0;
			}
			else
			{
				$count=1;
			}
			return $count;
}
function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2) {
	// Calculate the distance in degrees
	$degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));
 
	// Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
	switch($unit) {
		case 'km':
			$distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
			break;
		case 'mi':
			$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
			break;
		case 'nmi':
			$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
	}
	return round($distance, $decimals);
}
function otpcheck($email,$value , $field_name , $table_name , $type = '=' , $faliureCode =  FAILURE_CODE , $failureMessg = 'Error121 in the query while looking for field in database') {
	global $mysqli;
	 $_sql1="SELECT
					 *
					FROM
					  users
					WHERE
					  otp = '".$value."' and  email= '".$email."'
					LIMIT 1 ";
		$__sql =  mysqli_query($mysqli,$_sql1);


		if(!$__sql){

			$status['code'] = $faliureCode;

			$status['message'] = $failureMessg;

			sendResponse($status);

		}

		return (mysqli_num_rows($__sql)>0);
		//return ($_sql1);

}
function stripe_payment($json_request) {
		include("stripe/init.php");

    //$stripe_data = array('secret_key'  => 'sk_test_51EX3dXEX0zbJLZdeHHpBliYRIjsxbnQlhDeCvbNZ5p210dN0ncZYzZT5Dsrs5QNXeft6wo0AM8T7Vq2MJ5dXzr6L00OdgIJxdW', 'publishable_key' => 'pk_test_YxQenuCEu7twp2zh7PoiiFq600EvqjGqhI');
    $stripe_data = array('secret_key'  => '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', 'publishable_key' => 'pk_live_WLd73aqT9ttqwN4omt7kDl0W005U6aZwvq');
      	 $return = [
            'charge' => [],
            'message' => '',
            'code' => 0,
            'status' => 'error'
        ];
   $transfer_number = rand(10,1000000);
        \Stripe\Stripe::setApiKey($stripe_data['secret_key']);
        try {
            //create token
            $token = \Stripe\Token::create(array(
                        "card" => array(
                            "number" => $json_request['cardnumber'],
                            "exp_month" => $json_request['exp_month'],
                            "exp_year" => $json_request['exp_year'],
                            "cvc" =>$json_request['cvc']
                        )
            ));
			$payamount = $json_request['amount']; 
			$customer = \Stripe\Customer::create(array(
                        'source' => $token,
                        'description' => "user book a Order"
            ));
  
          $charge = \Stripe\Charge::create(array(
                        'customer' => $customer->id,
                        'amount' => $payamount * 100,
                        'currency' => 'usd'
			));
           

            $chargeArray = $charge->__toArray(true);
            
            $return['charge'] = $chargeArray;
            $return['code'] = 1;
            $return['status'] = $charge['status'];
            $return['message'] = 'Charge is successful';
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API

            $return['message'] = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)

            $return['message'] = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            $return['message'] = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
             $return['message'] = $e->getMessage();
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe

            $return['message'] = $e->getMessage();
        }
        return $return;
 }

if(!function_exists('pr')) {
	function pr($a, $x = false) {
		echo '<pre>';
		print_r($a);
		echo '</pre>';
		if($x) {
			die();
		}
	}
}
