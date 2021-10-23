<?php
//userId,auth_token,currentpass,newpass

require_once("config/function.php");

try{

$requiredField =	array(
	  
	  'auth_token'  => assignDefault($_REQUEST['auth_token']),
	  'gem'  => assignDefault($_REQUEST['gem']),
);

$validResponse = checkRequired($requiredField);

if(count($validResponse) > 0){

throw new Exception(implode(' , ' , $validResponse));

}

$non_required =	array();	

$paramArray = array_merge($requiredField , $non_required); 

array_walk($paramArray , 'getSafeValue');	

extract($paramArray ,EXTR_OVERWRITE);

//
$table_name="users";
if(!dbExist($auth_token,'auth_token',$table_name)) {
	throw new Exception("Authentication Token does not match"); 
}

$gem = $_REQUEST['gem'];

$query1 = "UPDATE users
          SET
                gem = '$gem'
          WHERE
                auth_token= '$auth_token' ";

$queryResult1  = executeQuery( $query1, false , FAILURE_CODE , 'update Query Execution Failed ' , true);
		
$status['code'] = SUCCESS_CODE;
$status['message'] = 'Gem updated successfully';
$body =new stdClass();

sendResponse($status ,$body );  	

}
catch(Exception $e)
{			

$status['code'] = FAILURE_CODE;

$status['message'] = $e->getMessage();	

$body= new stdClass();
sendResponse($status,$body);  			
}
?>
