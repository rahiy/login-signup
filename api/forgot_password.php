 <?php
/**
* email*
*/

require_once("config/function.php");
try
{
	if($_SERVER['REQUEST_METHOD']!='POST') {
        throw new Exception('Invalid Method');
    }
    $requiredField =	array(

		'email'    => assignDefault($_REQUEST['email']),
	);
					
	$validResponse = checkRequired($requiredField);
	
	if(count($validResponse) > 0){
	  
		throw new Exception(implode(' , ' , $validResponse));
		
    }
    
    extract($requiredField ,EXTR_OVERWRITE);
	
	
	   $table_name='users';
	
	
	$query="select * from $table_name where email='$email' ";
	$updateQueryResult  = executeQuery($query, false , FAILURE_CODE , 'selectd Query Execution Failed ' , false);
	if(count($updateQueryResult)==0){
		throw new Exception('Email is not exist!');
	}
   $userid=$updateQueryResult['id'];

   	$otp=rand(1000,10000);	
   	// $otp='1111';	
   	$idd=$updateQueryResult['id'];
   	// $link= FORGETPASSWORD.base64_encode($idd);

   	$subject="Randomizer";
	$headers='X-Mailer: PHP/' . phpversion().'\r\n';
	$headers.= 'MIME-Version: 1.0' . "\r\n";
	$headers.= 'Content-type: text/html; charset=iso-8859-1'. "\r\n";  
	$headers .= "From: demomailsphp@gmail.com"."\r\n";	
	$message='<h1> Hello!,</h1><br>'.'<h4>OTP for changing Password for the account: '.$email.' has been sent. </h4><br>';
	$message.='<h4>OTP: <b><u>'.$otp.'</u></b> </h4><br><br><br>';
	$message.='<h4>Regards,<h4>';
	$message.='<h4>Randomizer</h4>';
	
	$_params = [
		'to' => $email,
		'subject' => $subject ." ". date('Y-m-d h:i:s'),
		'body' => $message
					];
	 //print_r($_params);
	$mail=getmail($_params);
	if($mail==0) {
		throw new Exception('Mail not sent');
	}
	$table_name = 'reset_passwords';


	$query1 = "DELETE FROM $table_name WHERE email = '" . $_REQUEST['email'] . "' ";
   	$queryResult1 = executeQuery($query1, false, FAILURE_CODE, 'update Query Execution Failed ', true);
	
	$insertcondition="
			( `email`, `otp`)
	VALUES	('$email','$otp')
	     ";
	$resultSet  = "INSERT  INTO $table_name  $insertcondition ";

	$queryResult  = executeQuery( $resultSet, false , FAILURE_CODE , 'Insert2 Query Execution Failed ' , true);	
	
	$status['code'] = SUCCESS_CODE;

	$status['message'] = 'Forget password OTP has been sent successfully. Please check your Email';	
	$body=new stdClass();

	sendResponse($status,$body); 

 }
 catch(Exception $e)
{			
  
		
		$status['code'] = FAILURE_CODE;
		$status['message'] = $e->getMessage();	
		
		$body=new stdClass();

		sendResponse($status,$body);  			
}
?>
