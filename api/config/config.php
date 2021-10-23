<?php

global $mysqli;
if (!ini_get('display_errors')) {
    ini_set('display_errors', '0');
}
try {
	
	
	$host = 'localhost';
            $username = "root";
            $password = "";
            // $password = "&QT>Zt#/?9t_mfW2";
            $dbname = "randomizer";
            $mysqli=new mysqli($host,$username,$password,$dbname);
			if(mysqli_connect_errno())
			{
				printf("Connection failed %s\n",mysqli_connect_error());
				exit();
			}
			else
			{
				return 1;
			}

}
catch(Exception $e){

    echo $e->getMessage();

}
?>
