<?php

class API{
	function Auth(){
		global $api;
		if($api->userPermission==1){
			return array(
				'success'  => 0,
				'error'  => "Вы уже авторизованы.",
			);
		}
		if(!isset($_GET["login"]) || !isset($_GET["password"])){
			return array(
				'success'  => 0,
				'error'  => "Не указан логин или пароль.",
			);
		}
		if(!preg_match('/^[A-Za-z][A-Za-z0-9]{4,31}$/', $_GET["login"])){//TODO
			return array(
				'success'  => 0,
				'error'  => "Логин не валиден.",
			);
		}
		if(strlen($_GET['password'])<8 || strlen($_GET['password'])>32){
			return array(
				'success'  => 0,
				'error'  => "Пароль не валиден.",
			);
		}

		mysqli_select_db($api->mysqlConnect, "users");
		$query = mysqli_query($api->mysqlConnect, "SELECT id, pass FROM users WHERE login='".$_GET['login']."' LIMIT 1");
		$data = mysqli_fetch_assoc($query);

		if($data==NULL){
			return array(
				'success'  => 0,
				'error'  => "Неправильный логин или пароль.",
			);
		}

		if($data['pass'] != md5(md5($_GET["password"]))){
			return array(
				'success'  => 0,
				'error'  => "Неправильный логин или пароль. Пароль. ",
			);
		}

		$hash = md5($api->generateCode(10));
		setcookie("key", $hash, time()+60*60*24*30, "/");
		mysqli_query($api->mysqlConnect, "UPDATE `users` SET `key`='".$hash."' WHERE `id`='".$data['id']."';");

		return array(
			'success'  => 1,
		);
	}
	function Exit(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Вы не авторизованы.",
			);
		}
		$hash = md5($api->generateCode(10));
		setcookie("key", "", time()+60*60*24*30, "/");
		mysqli_query($api->mysqlConnect, "UPDATE `users` SET key='".$hash."' WHERE `id`='".$api->userId."'");
		return array(
			'success'  => 1,
		);
	}
}

class CMSCore{
	var $mysqlHost;
	var $mysqlUser;
	var $mysqlPassword;
	var $mysqlDB;
	var $mysqlConnect;

	var $userPermission;//0 - nobody 1 - admin
	var $userLogin;
	var $userId;

	var $API;

	function generateCode($length=6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;
		while(strlen($code) < $length){
			$code .= $chars[mt_rand(0,$clen)];
		}
		return $code;
	}
	function UIbeginAdminHeader(){
		echo "<!DOCTYPE html>
<html>
<head>
	<title>Админ-панель</title>
	<meta charset=\"utf-8\">
	<script src=\"admin.js\"></script>
	<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.3.1/css/all.css\" integrity=\"sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU\" crossorigin=\"anonymous\">
	<link rel=\"stylesheet\" href=\"admin.css\">
</head>
<body>
";
	}
	function UIendAdminHeader(){
		echo "
</body>
</html>";
	}
	function UIgetAdminPanel(){
		echo "
	<header>
		<div class=\"logout\">
			<i class=\"fa fa-sign-out-alt\"></i> Выйти
		</div>
		<div class=\"user\">
			<i class=\"fa fa-user\"></i> ".$this->userLogin."
		</div>
	</header>
	";
	}
	function UIgetAuthForm(){
		echo "
	<input class=\"loginInput\" placeholder=\"Логин\">
	<input class=\"passwordInput\" type=\"password\" placeholder=\"Пароль\">
	<a class=\"signInBtn\" href=\"javascript://\">Войти</a>";
	}
	function CMSCore($host,$user,$password,$db){
		$this->mysqlHost = $host;
		$this->mysqlUser = $user;
		$this->mysqlPassword = $password;
		$this->mysqlDB = $db;
		$this->connectMySQL();
		$this->checkUser();//get User Name & User Permission
		$this->API = new API();
	}
	function connectMySQL(){
		$this->mysqlConnect = mysqli_connect($this->mysqlHost, $this->mysqlUser, $this->mysqlPassword, $this->mysqlDB);
		if(!($this->mysqlConnect)){
			die('FUUUUUU: ' . mysql_error());

		}
	}
	function checkUser(){
		if (isset($_COOKIE["key"])){
			//mysql_select_db("users");
			$query = mysqli_query($this->mysqlConnect, "SELECT * FROM `users` WHERE `key` = '".$_COOKIE['key']."' LIMIT 1");
			$userdata = mysqli_fetch_assoc($query);
			if($userdata['key'] == $_COOKIE['key']){
				$this->userPermission=1;
				$this->userLogin=$userdata['login'];
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
