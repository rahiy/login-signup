<?php
/**
*  security_key	*
*	dealer_id *
*/
require_once("config/function.php");

try{

	if($_SERVER['REQUEST_METHOD']!='POST') {
		throw new Exception('Invalid Method');
	}

	$requiredArray = array(
		'auth_token'       => 	assignDefault(  $_REQUEST['auth_token']) 
	);
	
	
	array_walk($requiredArray , 'getSafeValue');

	$validResponse = checkRequired($requiredArray);
		  
	if(count($validResponse) > 0){
	  
		throw new Exception(implode(' , ' , $validResponse));
		
	}	
	
	$notRequiredField =	array();
	
	$paramArray = array_merge($requiredArray , $notRequiredField); 

	array_walk($paramArray , 'getSafeValue');

	extract($paramArray ,EXTR_OVERWRITE);

	

		$table_name="users";
		$id_field='id';


			$updatecondition=" 
		                    login_status = '0',
		                    device_token = '',
		                    device_type = '',
		                    auth_token =''		                     
		                    ";

	

if(!dbExist($auth_token,'auth_token',$table_name)) {

  throw new Exception("Authentication Token does not match"); 
}
else
 {

		 $query = " UPDATE
									$table_name
								SET
									$updatecondition
									
								WHERE	
									auth_token =	'$auth_token' ";
					
		$queryResult  = executeQuery( $query, false , FAILURE_CODE , 'Update1 Query Execution Failed ' , true);

	}
	
		
	/*$status['code'] = SUCCESS_CODE;*/
	http_response_code(200);
	
	 $status['code'] = SUCCESS_CODE;
	$status['message'] = 'Logout successfully';	
	
	$body = new stdClass();
	
	sendResponse($status , $body); 
			
			
}
catch(Exception $e){
		
	http_response_code(401);
	 $status['code'] = FAILURE_CODE;	
	/*$status['code'] = FAILURE_CODE;*/
	$status['message'] = $e->getMessage();	
	$body =  new stdClass();
	sendResponse($status , $body);
	
}
?>
