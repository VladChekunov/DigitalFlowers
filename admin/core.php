<?php

//ini_set('display_errors', 'On');

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
		if(!preg_match('/^[A-Za-z][A-Za-z0-9]{4,31}$/', $_GET["login"])){
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
	function DFExit(){
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
	function GetPages(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		//mysqli_select_db($api->mysqlConnect, "pages");
		$query = mysqli_query($api->mysqlConnect, "SELECT `id`, `url`, `title`, `status`, `order` FROM `pages` ORDER BY `order`;");

		$result = array();
		while($row = mysqli_fetch_assoc($query)){
			$listContent = array(
				'id'=>$row["id"],
				'url'=>$row["url"],
				'title'=>$row["title"],
				'status'=>$row["status"],
				'order'=>$row["order"],
			);
			$result[] = $listContent;
		}
			return array(
				'success'  => 1,
				'pages' => $result,
			);

	}
	function GetPageById(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		//mysqli_select_db($api->mysqlConnect, "pages");
		$query = mysqli_query($api->mysqlConnect, "SELECT `id`, `url`, `title`, `source`, `status` FROM `pages` WHERE `id`='".$_GET["id"]."';");
		$data = mysqli_fetch_assoc($query);
		$result = array(
			'id'  => $data["id"],
			'url'  => $data["url"],
			'title'  => $data["title"],
			'pagesource'  => $data["source"],
			'status'  => $data["status"],
		);
		return array(
			'success'  => 1,
			'page' => $result,
		);
		
	}
	function savePage(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET["id"]) || !isset($_GET["status"]) || !isset($_GET["url"]) || !isset($_GET["title"]) || !isset($_GET["source"])){
			return array(
				'success'  => 0,
				'error'  => "Не введён один из обязательных параметров.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		if(!preg_match('/^(0|1)$/', $_GET["status"])){
			return array(
				'success'  => 0,
				'error'  => "Статус не валиден.",
			);
		}
		//Тут должны быть проверки на валидность
		//TODO
		/*url*/
		/*title*/
		/*source*/
		
		$content = "";//$_GET["source"];
		$elements = json_decode($_GET["source"], true);

		for($i=0;$i<count($elements["els"]);$i++){
			//$content .=$elements["els"][$i]["type"];
			$content .= $api->checkElement($elements["els"][$i]);
		}
		$sqlquery = "UPDATE `pages` SET `url`='".$_GET["url"]."', `title`='".$_GET["title"]."', `source`='".$_GET["source"]."', `content`='".urlencode($content)."', `status`='".$_GET["status"]."' WHERE `id`='".$_GET["id"]."';";
		mysqli_query($api->mysqlConnect, $sqlquery);
		//todo write to constants menu
		return array(
			'success'  => 1,
			'content' => $sqlquery,
		);
	}
	function removePage(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не введён.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		mysqli_query($api->mysqlConnect, "DELETE FROM `pages` WHERE `id`='".$_GET["id"]."';");

		return array(
			'success'  => 1,
		);
	}
	function addPage(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET["status"]) || !isset($_GET["url"]) || !isset($_GET["title"]) || !isset($_GET["source"])){
			return array(
				'success'  => 0,
				'error'  => "Не введён один из обязательных параметров.",
			);
		}
		if(!preg_match('/^(0|1)$/', $_GET["status"])){
			return array(
				'success'  => 0,
				'error'  => "Статус не валиден.",
			);
		}
		//Тут должны быть проверки на валидность
		//TODO
		/*url*/
		/*title*/
		/*source*/
		$content = $_GET["source"];

		$query = mysqli_query($api->mysqlConnect, "SELECT MAX(`order`)+1 FROM `pages`;");
		$nextOrder = mysqli_fetch_assoc($query);

		mysqli_query($api->mysqlConnect, "INSERT INTO `pages` (`url`, `title`, `source`, `content`, `status`, `order`) VALUES ('".$_GET["url"]."', '".$_GET["title"]."', '".$_GET["source"]."', '".$content."', '".$_GET["status"]."', '".$nextOrder["MAX(`order`)+1"]."');");

		return array(
			'success'  => 1,
		);

	}
	function saveOrderPages(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET["ids"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификаторы не введёны.",
			);
		}
		$ids = explode(',', $_GET["ids"]);
		$sqlque = "";
		for($i = 0; $i < count($ids); $i++){
			$sqlque.="UPDATE `pages` SET `order`='".$i."' WHERE `id`='".$ids[$i]."';";
		}

		mysqli_multi_query($api->mysqlConnect, $sqlque);

		return array(
			'success'  => 1,
		);
	}
	function showUsersList(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if($api->userGroup==0){//Editors
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if($api->userGroup==1){//Moderator
			$query = mysqli_query($api->mysqlConnect, "SELECT `id`, `login`, `group` FROM `users` WHERE `group` < '2';");
		}
		if($api->userGroup==2){//Admin
			$query = mysqli_query($api->mysqlConnect, "SELECT `id`, `login`, `key`, `group` FROM `users`;");
		}
		$result = array();
		while($row = mysqli_fetch_assoc($query)){
			$listContent = array(
				'id'=>$row["id"],
				'login'=>$row["login"],
				'group'=>$row["group"],
			);
			$result[] = $listContent;
		}
		return array(
			'success'  => 1,
			'users' => $result,
		);
	}
	function removeUser(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if($api->userGroup==0){//Editors
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		$query = mysqli_query($api->mysqlConnect, "SELECT `group` FROM `users` WHERE `id`='".$_GET['id']."' LIMIT 1");
		$data = mysqli_fetch_assoc($query);
		if($data["group"]>$api->userGroup){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}

		mysqli_query($api->mysqlConnect, "DELETE FROM `users` WHERE `id`='".$_GET["id"]."';");

		return array(
			'success'  => 1,
		);
	}
	function GetUserById(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if($api->userGroup==0){//Editors
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		$query = mysqli_query($api->mysqlConnect, "SELECT `id`, `login`, `group` FROM `users` WHERE `id`='".$_GET['id']."' LIMIT 1");
		$data = mysqli_fetch_assoc($query);
		if($data["group"]>$api->userGroup){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}

		$result = array(
			'id'  => $data["id"],
			'login'  => $data["login"],
			'group'  => $data["group"],
		);


		return array(
			'success'  => 1,
			'user' => $result,
		);
	}
	function outUser(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if($api->userGroup==0){//Editors
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		$query = mysqli_query($api->mysqlConnect, "SELECT `group` FROM `users` WHERE `id`='".$_GET['id']."' LIMIT 1");
		$data = mysqli_fetch_assoc($query);
		if($data["group"]>$api->userGroup){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}

		$hash = md5($api->generateCode(10));
		mysqli_query($api->mysqlConnect, "UPDATE `users` SET `key`='".$hash."' WHERE `id`='".$_GET["id"]."';");

		return array(
			'success'  => 1,
		);
		
	}
	function addUser(){
		global $api;
		if($api->userPermission==0){//Ты не авторизован
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if($api->userGroup==0){//Редакторы не могут менять пользователей
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET["login"]) || !isset($_GET["password"]) || !isset($_GET["group"])){
			return array(
				'success'  => 0,
				'error'  => "Отсутствует один или несколько обязательных параметров.",
			);
		}
		if($_GET["group"]!=0 && $_GET["group"]!=1 && $_GET["group"]!=2){
			return array(
				'success'  => 0,
				'error'  => "Группа задана не валидно.",
			);
		}

		if($api->userGroup<$_GET["group"]){
			return array(
				'success'  => 0,
				'error'  => "Нельзя создать пользователя рангом выше себя.",
			);
		}
		if(!preg_match('/^[A-Za-z][A-Za-z0-9]{4,31}$/', $_GET["login"])){
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
 
		//Check if user not already exits
		$query = mysqli_query($api->mysqlConnect, "SELECT `id` FROM `users` WHERE `login` = '".$_GET["login"]."';");
		$data = mysqli_fetch_assoc($query);
		if($data!=NULL){
			return array(
				'success'  => 0,
				'error'  => "Пользователь с таким логином уже существует.",
			);
		}

		$hash = md5($api->generateCode(10));
		mysqli_query($api->mysqlConnect, "INSERT INTO `users` (`login`, `pass`, `group`, `key`) VALUES ('".$_GET["login"]."', '".md5(md5($_GET['password']))."', '".$_GET["group"]."','".$hash."');");

		return array(
			'success'  => 1,
		);

		
		
	}
	function saveUser(){
		global $api;
		if($api->userPermission==0){//Ты не авторизован
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET['id'])){
			return array(
				'success'  => 0,
				'error'  => "Отсутствует один или несколько обязательных параметров.",
			);
		}

		$query = mysqli_query($api->mysqlConnect, "SELECT `group` FROM `users` WHERE `id`='".$_GET['id']."' LIMIT 1");
		$data = mysqli_fetch_assoc($query);
		if($data["group"]>$api->userGroup){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}

		if(!isset($_GET["password"]) && !isset($_GET["login"]) && !isset($_GET["group"])){
			return array(
				'success'  => 0,
				'error'  => "Отсутствует один или несколько обязательных параметров.",
			);
		}

		$sqlQueryes = array();
		if(isset($_GET["password"])){
			if(strlen($_GET['password'])<8 || strlen($_GET['password'])>32){
				return array(
					'success'  => 0,
					'error'  => "Пароль не валиден.",
				);
			}

			$sqlQueryes[]=" `pass`='".md5(md5($_GET['password']))."'";
		}
		if(isset($_GET["login"])){
			if(!preg_match('/^[A-Za-z][A-Za-z0-9]{4,31}$/', $_GET["login"])){
				return array(
					'success'  => 0,
					'error'  => "Логин не валиден.",
				);
			}

			$sqlQueryes[]=" `login`='".$_GET['login']."'";
		}
		if(isset($_GET["group"])){
			if($_GET['group']!=0 && $_GET['group']!=1 && $_GET['group']!=2){
				return array(
					'success'  => 0,
					'error'  => "Группа не валидна.",
				);
			}

			$sqlQueryes[]=" `group`='".$_GET['group']."'";
		}
		$queryString = join(', ', $sqlQueryes);
		mysqli_query($api->mysqlConnect, "UPDATE `users` SET ".$queryString." WHERE `id`='".$_GET['id']."';");

		return array(
			'success'  => 1,
		);

	}

	function showProductsList(){
		global $api;
		if($api->userPermission==0){//Ты не авторизован
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}

		$query = mysqli_query($api->mysqlConnect, "SELECT `product_id`, `name`, `price`, `image` FROM `products`;");

		$result = array();
		while($row = mysqli_fetch_assoc($query)){
			$listContent = array(
				'id'=>$row["product_id"],
				'product_name'=>$row["name"],
				'price'=>$row["price"],
				'image'=>$row["image"],
			);
			$result[] = $listContent;
		}
		return array(
			'success'  => 1,
			'products' => $result,
		);

	}
	function removeProduct(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		if(!isset($_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не введён.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		mysqli_query($api->mysqlConnect, "DELETE FROM `products` WHERE `product_id`='".$_GET["id"]."';");
		
		return array(
			'success' => 1,
		);
	}
	function GetProductById(){
		global $api;
		if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		
		$query = mysqli_query($api->mysqlConnect, "SELECT * FROM `products` WHERE `product_id`='".$_GET['id']."';");
		$row = mysqli_fetch_assoc($query);

		$product = array(
			'product_id' => $row['product_id'],
			'product_name' => $row['name'],
			'product_price' => $row['price'],
			'product_old_price' => $row['old_price'],
			'product_quantity' => $row['quantity'],
			'product_image' => $row['image'],
			'product_description' => $row['description'],
		);

		return array(
			'success' => 1,
			'product' => $product,
		);
	}
	function addProduct(){
	 	global $api;
	 	if($api->userPermission==0){
			return array(
				'success'  => 0,
				'error'  => "Ошибка доступа.",
			);
		}
		//check $_GET fields TODO
		//name
		//price
		//old_price
		//quantity
		//image
		//description

		mysqli_query($api->mysqlConnect, "INSERT INTO `products` (`name`, `price`, `old_price`, `quantity`, `image`, `description`) VALUES ('".$_GET["name"]."', '".$_GET["price"]."', '".$_GET["old_price"]."', '".$_GET["quantity"]."', '".$_GET["image"]."', '".$_GET["description"]."');");


		return array(
	 		'success' => 1
		);
	}
	function saveProduct(){
		global $api;
		if($api->userPermission==0){
				return array(
					'success'  => 0,
					'error'  => "Ошибка доступа.",
				);
		}
		if(!isset($_GET["id"]) || !isset($_GET["name"]) || !isset($_GET["price"]) || !isset($_GET["quantity"]) || !isset($_GET["image"])){
			return array(
				'success'  => 0,
				'error'  => "Не введён один из обязательных параметров.",
			);
		}
		if(!preg_match('/^[0-9]{1,32}$/', $_GET["id"])){
			return array(
				'success'  => 0,
				'error'  => "Идентификатор не валиден.",
			);
		}
		//Тут должны быть проверки на валидность

		mysqli_query($api->mysqlConnect, "UPDATE `products` SET `name`='".$_GET["name"]."', `price`='".$_GET["price"]."', `old_price`='".$_GET["old_price"]."', `quantity`='".$_GET["quantity"]."', `image`='".$_GET["image"]."', `description`='".$_GET["description"]."' WHERE `product_id`='".$_GET["id"]."';");
			
			
		return array(
			'success' => 1
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
	var $userGroup;// 0 - Editor / 1 - Moderator / 2 - Admin

	var $API;
	function getParam($param, $el){
		return $el["values"][array_search($param, $el["params"])];
	}
	function checkElement($el){
		global $api;
		$content="";
		$preel="";//before content
		$postel="";//after content
		switch($el["type"]){
			case "map":
				$mapsrc = ' src="'.$api->getParam("mapsrc", $el).'"';
				$preel .="<div class='map'>\n<script src='".$mapsrc."'></script>\n";
				$postel .="\n</div>\n";
			break;
			case "cols":
				$preel .='<div class="cols_container">';
				$postel .='</div>';
			break;
			case "col":
				$preel .='<div class="col-'.$api->getParam("size", $el).'">';
				$postel .='</div>';
			break;
			case "image_card":
				$preel .='<div class="image_card">
				<i class="'.$api->getParam("icon", $el).'"></i>
				<b>'.$api->getParam("header", $el).'</b>
				<p>'.$api->getParam("description", $el).'</p>';
				$postel .='</div>';
			break;
			case "title":
				$preel .='<h2>'.$api->getParam("content", $el);
				$postel .='</h2>';
			break;
			case "text":
				$preel .='<div class="text_container">';
				$postel .='</div>';
			break;
			case "jumbotron":
				$preel .='<div class="jumbotron">
		<div class="jt_bg">
			<img src="'.$api->getParam("content", $el).'">
		</div>
		<div class="jt_fg">
			<div class="content">';
				$postel .='			</div>
		</div>
	</div>';
			break;
			case "html":
				$preel .=''.$api->getParam("content", $el);

				$postel .='';
			break;
		}
		for($i=0;$i<count($el["childrens"]);$i++){
			$content .= $api->checkElement($el["childrens"][$i]);
		}
		return $preel.$content.$postel;
/*
	{
	  "els": [{
	    "type": "map",
	    "childrens": [],
	    "params": ["mapsrc"],
	    "values": ["world"]
	  }]
	}
*/
	}
	function generateCode($length=6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;
		while(strlen($code) < $length){
			$code .= $chars[mt_rand(0,$clen)];
		}
		return $code;
	}
	function getPage($pageId){
		if($pageId==-1){
			include "../template/404.php";
			return 0;
		}
		//get content from db
		$query = mysqli_query($this->mysqlConnect, "SELECT `content` FROM `pages` WHERE `id` = '".$pageId."';");
		$pagedata = mysqli_fetch_assoc($query);

		include "../template/header.php";
		echo urldecode($pagedata["content"]);
		include "../template/footer.php";
		return 1;
	}
	function UIbeginAdminHeader(){
		echo "<!DOCTYPE html>
<html>
<head>
	<title>Админ-панель</title>
	<meta charset=\"utf-8\">
	<script src=\"/admin/admin.js\"></script>
	<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.3.1/css/all.css\" integrity=\"sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU\" crossorigin=\"anonymous\">
	<link rel=\"stylesheet\" href=\"/admin/admin.css\">
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
		<div class=\"admin_menu\">
			<div class=\"menu_active\">Pages</div>
			<div style=\"display:none\" class=\"menu_list\">
				<a href=\"javascript://\">Pages</a>
				<a href=\"javascript://\">Users</a>
				<a href=\"javascript://\">Products</a>
				<a href=\"javascript://\">Settings</a>
			</div>
		</div>
		<div class=\"logout\">
			<i class=\"fa fa-sign-out-alt\"></i> Выйти
		</div>
		<div class=\"user\">
			<i class=\"fa fa-user\"></i> ".$this->userLogin."
			<div style=\"display:none;\" class=\"user_permission\">".$this->userGroup."</div>
		</div>
	</header>
	<div class=\"admin_content\"></div>
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
	function getMenu(){
		$query = mysqli_query($api->mysqlConnect, "SELECT ``url`, `title` FROM `pages` ORDER BY `order` WHERE `order`='1';");

		$result = "";
		while($row = mysqli_fetch_assoc($query)){
			$result.='<a href="'.$row["url"].'">'.$row["title"].'</a>'."\n";
		}
		return $result;
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
				$this->userGroup=$userdata['group'];
			}else{
				$this->userPermission=0;
				setcookie("key", "", time()+60*60*24*30, "/");
			}
		}else{
			$this->userPermission=0;
		}
	}
}
?>
