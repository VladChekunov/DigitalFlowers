<?php

class CMSCore{
	var $mysqlHost;
	var $mysqlUser;
	var $mysqlPassword;
	var $mysqlDB;
	var $mysqlConnect;

	var $userPermission;//0 - nobody 1 - admin
	var $userLogin;
	var $userId;
	function UIbeginAdminHeader(){
		echo "<!DOCTYPE html>
<html>
<head>
	<title>Админ-панель</title>
</head>
<body>
";
	}
	function UIendAdminHeader(){
		echo "
</body>
</html>";
	}
	function CMSCore($host,$user,$password,$db){
		$this->mysqlHost = $host;
		$this->mysqlUser = $user;
		$this->mysqlPassword = $password;
		$this->mysqlDB = $db;
		$this->connectMySQL();
		$this->checkUser();//get User Name & User Permission
	}
	function connectMySQL(){
		$this->mysqlConnect = mysqli_connect($this->mysqlHost, $this->mysqlUser, $this->mysqlPassword, $this->mysqlDB);
		if(!($this->mysqlConnect)){
			die('FUUUUUU: ' . mysql_error());

		}
	}
	function checkUser(){
		if (isset($_COOKIE["hash"])){
			//mysql_select_db("users");
			$query = mysqli_query($this->mysqlConnect, "SELECT * FROM users WHERE user_hash = '".$_COOKIE['hash']."' LIMIT 1");
			$userdata = mysqli_fetch_assoc($query);
			if($userdata['user_hash'] == $_COOKIE['hash']){
				$this->userPermission=1;
				$this->userLogin=$userdata['user_login'];
				$this->userId=$userdata['id'];
			}else{
				$this->userPermission=0;
				setcookie("hash", "", time() - 3600*24*30*12, "/");
			}
		}else{
			$this->userPermission=0;
		}
	}
}

?>
