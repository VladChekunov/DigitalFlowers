<?php
include "core.php";
/*
Render specific page with footer header etc
*/
	$api = new CMSCore("localhost","root","toor","dflowers");

	$uri=preg_replace('#[a-z0-9]+\.[a-z0-9]+$#i', '', $_SERVER['REQUEST_URI']);
	$get_reqs=explode('/', $uri, 20);

	if($get_reqs[1]=="admin"){
		$api->UIbeginAdminHeader();
			if($api->userPermission==1){
				//echo "TODO: Открываем админ-панель";
				$api->UIgetAdminPanel();
			}else{
				$api->UIgetAuthForm();
			}
		$api->UIendAdminHeader();

	}else if($get_reqs[1]=="api"){
		switch($get_reqs[2]){
			case "Auth":
				echo json_encode($api->API->Auth());
			break;
			case "Exit":
				echo json_encode($api->API->Exit());
			break;
			case "GetPages":
				echo json_encode($api->API->GetPages());
			break;


		}
	}else{
		if(sizeof($get_reqs)>2 && $get_reqs[2]!=NULL){
			$api->getPage(-1);
		}else{
			$query = mysqli_query($api->mysqlConnect, "SELECT `url`,`id` FROM dflowers.pages WHERE `status`=1 ORDER by `order`;");
			$isPageFind=false;
			while($row = mysqli_fetch_assoc($query)){
				if(sizeof($get_reqs)<1 || $get_reqs[1]==NULL){
					if($row["url"]=="@"){
						//Запрашиваем глагне
						$api->getPage($row["id"]);
						$isPageFind=true;
						break;
					}
				}else{
					if($row["url"]==$get_reqs[1]){
						$api->getPage($row["id"]);
						$isPageFind=true;
						break;
					}
				}
			}
			if($isPageFind==false){
				$api->getPage(-1);
			}
		}
		//Если не одна не подходит -> 404
		//Если подходит, грузим по шаблону
	}
?>
