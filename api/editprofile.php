<?php

require_once("config/function.php");

try{

	if($_SERVER['REQUEST_METHOD']!='POST') {
        throw new Exception('Invalid Method');
    }

	$requiredArray = array(
		'auth_token'		=>	assignDefault($_REQUEST['auth_token']),
		'first_name'			=>	assignDefault($_REQUEST['first_name']),
		'last_name'			=>	assignDefault($_REQUEST['last_name']),
		'email'			=>	assignDefault($_REQUEST['email']),
		'phone'			=>	assignDefault($_REQUEST['phone'])
	);

	array_walk($requiredArray , 'getSafeValue');

	$validResponse = checkRequired($requiredArray);

	if(count($validResponse) > 0){

		throw new Exception(implode(' , ' , $validResponse));
	}

	$notRequiredField =	array(
	    'address'		=>	assignDefault($_REQUEST['address']),
    	'dob'		=>	assignDefault($_REQUEST['dob']),
    	'latitude'		=>	assignDefault($_REQUEST['latitude']),
    	'longitude'		=>	assignDefault($_REQUEST['longitude']),
		'image'		 =>	assignDefault($_FILES['image']['name'])
	);
	$paramArray = array_merge($requiredArray , $notRequiredField);

	array_walk($paramArray , 'getSafeValue');

	extract($paramArray ,EXTR_OVERWRITE);
	$table_name='users';
	$url= IMG_URL;

	if(!dbExist($auth_token,'auth_token',$table_name)) {
	 throw new Exception("Authentication Token does not match"); 
	}

	$q = "SELECT image from users where `auth_token`='$auth_token'";
	$r  = executeQuery( $q, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	$medialink="";
	if($_FILES['image']['name']!="") {
	 	$file_name=time().$_FILES['image']['name'];
		$new=str_replace("/api","",BASE_SERVER_URL);
		$loc= $new."/admin/uploads/users/".$file_name;
		move_uploaded_file($_FILES['image']['tmp_name'],$loc);
		$medialink = "uploads/users/".$file_name;
	}
	else {
		$medialink=$r['image'];
	}

    $query12 = "UPDATE users SET username='$first_name',last_name='$last_name',email='$email',phone='$phone',image='$medialink',address='$address',dob='$dob',latitude='$latitude',longitude='$longitude' WHERE auth_token = '".$auth_token."' ";
	$queryResult12  = executeQuery( $query12, false , FAILURE_CODE , 'update Query Execution Failed ' , true);
	
	$userAuth = "Select `id`,`username`,`last_name`,`email`,`phone`,`address`, `dob`, `latitude`, `longitude`,CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image,`status`,`login_status`,`social_id`,`notification_status`,`login_type`,auth_token from users where  auth_token = '".$auth_token."' ";
    $data1  = executeQuery( $userAuth, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);

	$body = $data1;
	$status['code'] = SUCCESS_CODE;
	$status['message'] = 'Profile Successfully Updated';
	sendResponse($status ,$body);

}
catch(Exception $e)
{
	//$a = new stdClass();
	$body = new stdClass();
	$status['code'] = FAILURE_CODE;
	$status['message'] = $e->getMessage();

	sendResponse($status,$body);
}
?>
