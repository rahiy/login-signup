<?php
/*
* security_key *
* email	*
* password *
* username	*
* device_type		0->Android,1->IOS
* device_token
*/
// include("PHPMailer/class.phpmailer.php");
// include("PHPMailer/class.smtp.php");

require_once("config/function.php");

try{
	if($_SERVER['REQUEST_METHOD']!='POST') {
        throw new Exception('Invalid Method');
    }

	$requiredArray = array(
		'first_name'		=>	assignDefault($_REQUEST['first_name']),
		'last_name'			=>	assignDefault($_REQUEST['last_name']),
		'email'			    =>	assignDefault($_REQUEST['email']),
		'login_type'		=>	assignDefault($_REQUEST['login_type']),
		'socialId'		  =>	assignDefault($_REQUEST['socialId'])
	);

	array_walk($requiredArray , 'getSafeValue');

	$validResponse = checkRequired($requiredArray);

	if(count($validResponse) > 0){

		throw new Exception(implode(' , ' , $validResponse));
	}

	$notRequiredField =	array(
		'phone'			=>	assignDefault($_REQUEST['phone']),
		'address'		=>	assignDefault($_REQUEST['address']),
    	'dob'		=>	assignDefault($_REQUEST['dob']),
    	'latitude'		=>	assignDefault($_REQUEST['latitude']),
    	'longitude'		=>	assignDefault($_REQUEST['longitude']),
		'device_token'		=>	assignDefault($_REQUEST['device_token']),
		'device_type'		=>	assignDefault($_REQUEST['device_type'])
	);

	$paramArray = array_merge($requiredArray , $notRequiredField);

	array_walk($paramArray , 'getSafeValue');

	extract($paramArray ,EXTR_OVERWRITE);
	$password1 = MD5($password);
	$table_name='users';
	$user_id='id';
	$url= IMG_URL;
	$q = "SELECT id from users where `social_id`='$socialId'";
	$r  = executeQuery( $q, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	if(!empty($r)){
		$auth1 = randomPassword();
   		 $auth_token = base64_encode($auth1);
        $query12 = "UPDATE $table_name SET
                            login_status = '1',
                            login_type = '$login_type',
		                    auth_token = '$auth_token',
                            device_token = '$device_token',
                            device_type = '$device_type'
		          WHERE
		                  id = '" . $r['id'] . "' ";
         $queryResult12 = executeQuery($query12, false, FAILURE_CODE, 'update Query Execution Failed ', true);
   		$userAuth = "Select `id`,`username`,`last_name`,`email`,`phone`,CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image,`status`,`login_status`,`notification_status`,`login_type`,auth_token from users where   id = '".$r['id']."' ";
   		$data1  = executeQuery( $userAuth, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	    $body = $data1;
	}
	else {
		
		if(dbExist($email,'email',$table_name)) {
		throw new Exception('This Email is already used!');
		}
		$insertcondition="
				(`username`,`last_name`, `email`,`phone`,`address`, `dob`, `latitude`, `longitude`,`login_type`,`social_id`, `device_token`, `device_type`)
		VALUES	('$first_name','$last_name','$email','$phone','$address','$dob','$latitude','$longitude','$login_type','$socialId','$device_token','$device_type')
		     ";
		$resultSet  = "INSERT  INTO $table_name  $insertcondition ";
	
		$queryResult  = executeQuery( $resultSet, false , FAILURE_CODE , 'Insert2 Query Execution Failed ' , true);
		global $mysqli;
		$userID = mysqli_insert_id($mysqli);
		$auth1=randomPassword();
		$auth_token=base64_encode($userID.$auth1);
		$query12 = "UPDATE $table_name SET login_status='1',auth_token = '$auth_token' WHERE id = '".$userID."' ";
		$queryResult12  = executeQuery( $query12, false , FAILURE_CODE , 'update Query Execution Failed ' , true);
		$userAuth = "Select `id`,`username`,`last_name`,`email`,`phone`,`address`, `dob`, `latitude`, `longitude`,CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image,`status`,`login_status`,`social_id`,`notification_status`,`login_type`,auth_token from users where   id = '".$userID."' ";
   		$data1  = executeQuery( $userAuth, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
	    $body = $data1;
	 }
	http_response_code(200);
	$status['code'] = SUCCESS_CODE;
	$status['message'] = 'Login Succesfully';
	sendResponse($status ,$body);

}
catch(Exception $e)
{
	//$a = new stdClass();
	$body = new stdClass();
	 http_response_code(401);
	 $status['code'] = FAILURE_CODE;
	$status['message'] = $e->getMessage();

	sendResponse($status,$body);
}
?>
