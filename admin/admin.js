var admin = {
	init:function(){
		if(document.getElementsByClassName("signInBtn").length>0){
			document.getElementsByClassName("signInBtn")[0].onclick=function(){
				admin.signin();
			}
		}
		if(document.getElementsByClassName("logout").length>0){
			document.getElementsByClassName("logout")[0].onclick=function(){
				admin.signout();
			}
		}
	},
	signout:function(){
		admin.ajaxSend("/api/Exit/",function(e){
			if(e.target.response.success==1){
				alert("Вы успешно вышли");
				location.reload();
			}else{
				alert("Ошибка. "+e.target.response.error);
			}
		});
	},
	signin:function(){
		authlogin = document.getElementsByClassName("loginInput")[0].value;
		authPassword = document.getElementsByClassName("passwordInput")[0].value;
		admin.ajaxSend("/api/Auth/?login="+authlogin+"&password="+authPassword,function(e){
			if(e.target.response.success==1){
				alert("Авторизованы");
				location.reload();
			}else{
				alert("Ошибка. "+e.target.response.error);
			}
		});
	},
	ajaxSend:function(url,callback){
		var oReq = new XMLHttpRequest();
		oReq.onload = function (e) {
			callback(e);
		    //results.innerHTML = e.target.response.message;
		};
		oReq.open('GET', url, true);
		oReq.responseType = 'json';
		oReq.send();
	}
}
window.onload=function(){
	admin.init();
}
