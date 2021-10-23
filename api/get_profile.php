<?php

/**
 * email	*
 * password	*
 * device_type
 * device_token
 */
require_once("config/function.php");
ini_set("display_errors", "0");
//error_reporting(E_ALL);

try {

    if($_SERVER['REQUEST_METHOD']!='GET') {
        throw new Exception('Invalid Method');
    }
    $requiredArray = array(
        'auth_token' => assignDefault($_REQUEST['auth_token'])
    );

    array_walk($requiredArray, 'getSafeValue');

    $validResponse = checkRequired($requiredArray);

    if (count($validResponse) > 0) {

        throw new Exception(implode(' , ', $validResponse));
    }

    $notRequiredField = array
        (       
    );

    $paramArray = array_merge($requiredArray, $notRequiredField);

    array_walk($paramArray, 'getSafeValue');

    extract($paramArray, EXTR_OVERWRITE);
    $table_name="users";
    if(!dbExist($auth_token,'auth_token',$table_name)) {
                throw new Exception("Authentication Token does not match"); 
    }

    $url= IMG_URL;
    $userAuth = "Select `id`,`username`,`last_name`,`email`,`phone`,`address`, `dob`, `latitude`, `longitude`,CASE WHEN image !='' THEN  concat('$url' ,image) ELSE '' END AS image,`status`,`login_status`,`social_id`,`notification_status`,`login_type`,auth_token from users where  auth_token = '".$auth_token."' ";
    $data1  = executeQuery( $userAuth, false , FAILURE_CODE , 'Select Query Execution Failed ' , false);
    http_response_code(200);
    $status['code'] = SUCCESS_CODE;
    $status['message'] = 'Profile Detail';
    $body = $data1;

    
    sendResponse($status, $body);
} catch (Exception $e) {
    http_response_code(400);
    $status['code'] = FAILURE_CODE;
    $body =  new stdClass();
    $status['message'] = $e->getMessage();
    sendResponse($status, $body);
}
?>
