<?php
//userId,auth_token,currentpass,newpass

require_once("config/function.php");

try{
if($_SERVER['REQUEST_METHOD']!='POST') {
        throw new Exception('Invalid Method');
    }
$requiredField =array(
        // 'email' => assignDefault($_REQUEST['email']), 
	  'otp'  => assignDefault($_REQUEST['otp']),
	  'newpass'      => assignDefault($_REQUEST['newpass']), 
	  'confirm_password'      => assignDefault($_REQUEST['confirm_password']) 
);

$validResponse = checkRequired($requiredField);

if(count($validResponse) > 0){

throw new Exception(implode(' , ' , $validResponse));

}

if($_REQUEST['newpass'] != $_REQUEST['confirm_password']){

    throw new Exception('New password and confirm password didn`t match!');

}

$non_required =	array(

					  
);	


$paramArray = array_merge($requiredField , $non_required); 

array_walk($paramArray , 'getSafeValue');	

extract($paramArray ,EXTR_OVERWRITE);

$table_name = 'reset_passwords';
$email = $_REQUEST['email'];
$otp = $_REQUEST['otp'];

$resultSet1 = " SELECT *
							FROM
								$table_name
							WHERE
								-- `email` = '$email'
								-- AND
                                `otp` = '$otp' 
                                
                                ";


    $queryResult1 = executeQuery($resultSet1, false, FAILURE_CODE, 'SELECT  Query Execution Failed ', false);
    if (count($queryResult1) == 0) {
        throw new Exception('Invalid OTP');
    }

//
$table_name="users";
if(!empty($_REQUEST['newpass'])){
	$pas = md5($newpass);
}


$query1 = "UPDATE users
          SET
                    password = '$pas'
      
          WHERE
                   email= '$email'";

$queryResult1  = executeQuery( $query1, false , FAILURE_CODE , 'update Query Execution Failed ' , true);
		
/*http_response_code(200);*/
$status['code'] = SUCCESS_CODE;
$status['message'] = 'Password updated successfully';	
$body =new stdClass();

sendResponse($status ,$body );  	

}
catch(Exception $e)
{			

/*http_response_code(400);*/
$status['code'] = FAILURE_CODE;
$status['message'] = $e->getMessage();	

$body= new stdClass();
sendResponse($status,$body);  			
}
?>
