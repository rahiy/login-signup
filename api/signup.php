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
		'username'			=>	assignDefault($_REQUEST['username']),
		'email'			=>	assignDefault($_REQUEST['email']),
		'password'		=>	assignDefault($_REQUEST['password'])
	);

	array_walk($requiredArray , 'getSafeValue');

	$validResponse = checkRequired($requiredArray);

	if(count($validResponse) > 0){

		throw new Exception(implode(' , ' , $validResponse));
	}

	$notRequiredField =	array(
		
	);

	$paramArray = array_merge($requiredArray , $notRequiredField);

	array_walk($paramArray , 'getSafeValue');

	extract($paramArray ,EXTR_OVERWRITE);
	$password1 = MD5($password);
	$table_name='users';
	$user_id='id';
	if(dbExist($email,'email',$table_name)) {
		throw new Exception('This Email is already used!');
	}
	if(dbExist($username,'username',$table_name)) {
		throw new Exception('This Username is already used!');
	}
	$insertcondition="
			(`username`, `email`, `password`, `device_token`, `device_type`)
	VALUES	('$username','$email','$password1','$device_token','$device_type')
	     ";
	$resultSet  = "INSERT  INTO $table_name  $insertcondition ";

	$queryResult  = executeQuery( $resultSet, false , FAILURE_CODE , 'Insert2 Query Execution Failed ' , true);
	global $mysqli;
	$userID = mysqli_insert_id($mysqli);
	$auth1=randomPassword();
	$auth_token=base64_encode($userID.$auth1);
	$query12 = "UPDATE $table_name SET login_status='1',auth_token = '$auth_token' WHERE id = '".$userID."' ";
	$queryResult12  = executeQuery( $query12, false , FAILURE_CODE , 'update Query Execution Failed ' , true);
	
	$userAuth = "Select `id`,`username`,`email`,`gem`,`login_status`,auth_token from users where   id = '".$userID."' ";
	$data1  = executeQuery( $userAuth, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
    $body = $data1;
	http_response_code(200);
	$status['code'] = SUCCESS_CODE;
	$status['message'] = 'SignUp Succesfully';
	sendResponse($status ,$body);

}
catch(Exception $e)
{
	//$a = new stdClass();
	$body = new stdClass();
	/* http_response_code(400);*/
	 $status['code'] = FAILURE_CODE;
	$status['message'] = $e->getMessage();

	sendResponse($status,$body);
}
?>
