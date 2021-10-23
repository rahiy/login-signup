<?php

/**
 * email	*
 * password	*
 * device_type
 * device_token
 */
require_once("config/function.php");
ini_set("display_errors", "1");


try {
    if($_SERVER['REQUEST_METHOD']!='POST') {
        throw new Exception('Invalid Method');
    }
    $requiredArray = array(
        'email' => assignDefault($_REQUEST['email']),
        'password' => assignDefault($_REQUEST['password'])
    );

    array_walk($requiredArray, 'getSafeValue');

    $validResponse = checkRequired($requiredArray);

    if (count($validResponse) > 0) {

        throw new Exception(implode(' , ', $validResponse));
    }

    $notRequiredField = array
        (       
        'device_type' => assignDefault($_REQUEST['device_type']),
        'device_token' => assignDefault($_REQUEST['device_token'])
    );

    $paramArray = array_merge($requiredArray, $notRequiredField);

    array_walk($paramArray, 'getSafeValue');

    extract($paramArray, EXTR_OVERWRITE);
    $table_name = "users";
    $id_field = 'id';
    $url= IMG_URL;

    $password1 = MD5($password);

    $resultSet1 = " SELECT *
							FROM
								$table_name
							WHERE
								`email` = '$email'
								AND
                                `password` = '$password1' 
                                
                                ";

    $queryResult1 = executeQuery($resultSet1, false, FAILURE_CODE, 'SELECT  Query Execution Failed ', false);
    if (count($queryResult1) == 0) {
        throw new Exception('Email or Password is Incorrect');
    }
     $id = $queryResult1['id'];
    $auth1 = randomPassword();
    $auth_token = base64_encode($auth1);

    if (dbExist($id, $id_field, $table_name)) {

        $query12 = "UPDATE $table_name SET
                            login_status = '1',
		                    auth_token = '$auth_token',
                            device_token = '$device_token',
                            device_type = '$device_type'
		          WHERE
		                  id = '" . $id . "' ";
         $queryResult12 = executeQuery($query12, false, FAILURE_CODE, 'update Query Execution Failed ', true);
    } 

    
    $userAuth = "Select `id`,`username`,`email`,`gem`,`login_status`,auth_token from users where   id = '".$id."' ";
    $data1  = executeQuery( $userAuth, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
     
   
    http_response_code(200);
    $status['code'] = SUCCESS_CODE;
    $status['message'] = 'Login Succesfully';
    $body = $data1;

    
    sendResponse($status, $body);
} catch (Exception $e) {
    $status['code'] = FAILURE_CODE;
    $status['message'] = $e->getMessage();
    $body =  new stdClass();
    sendResponse($status, $body);
}
?>
