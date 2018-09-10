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
		if(document.getElementsByClassName("menu_active").length>0){
			admin.menu.close({target:document.getElementsByClassName("menu_list")[0].children[0]});
			document.getElementsByClassName("menu_active")[0].onclick=function(){
				admin.menu.open();
			}
		}
	},
	pages:{
		showPagesList:function(){
			admin.ajaxSend("/api/GetPages/",function(e){
				if(e.target.response.success==1){
					pagesList="<a onclick=\"admin.addPage()\" class=\"btn\" href=\"javascript://\">Добавить страницу</a>"
					pagesList+="<table class='pages_list'>\n<tr>\n\t<th>id</th>\n\t<th>title</th>\n\t<th>url</th>\n\t<th>Edit</th>\n\t<th>Order</th>\n\t<th>Remove</th>\n</tr>\n";
					for(var i=0;i<e.target.response.pages.length;i++){
						pagestatus="enabled_page";
						if(e.target.response.pages[i].status==0){
							pagestatus="disabled_page";
						}
						pagesList+="<tr class=\""+pagestatus+"\">\n\t<td>"+e.target.response.pages[i].id+"</td>\n\t<td>"+e.target.response.pages[i].title+"</td>\n\t<td>"+e.target.response.pages[i].url+"</td><td><a class=\"btn\" onclick=\"admin.editPage("+e.target.response.pages[i].id+")\" href=\"javascript://\">Edit</a></td><td style=\"drag\">v/^</td><td><a onclick=\"admin.editPage("+e.target.response.pages[i].id+")\" class=\"btn\" href=\"javascript://\">Remove</a></td>\n</tr>\n";
					}
					pagesList+="</table>"
					document.getElementsByClassName("admin_content")[0].innerHTML=pagesList;
					console.log(e.target.response);
					//alert("Список страниц получен");
				}else{
					alert("Ошибка. "+e.target.response.error);
				}
			});
		}
	},
	menu:{
		close:function(e){
			if(e.target.parentNode.className=="menu_list"){
				switch(e.target.innerHTML){
					case "Pages":
						admin.pages.showPagesList();
						break;
					case "Users":
						admin.pages.showUsersList();
					break;
					case "Products":
						admin.pages.showProductsList();
					break;
					case "Settings":
						admin.pages.showSettings();
					break;
				}
				document.getElementsByClassName("menu_active")[0].innerHTML=e.target.innerHTML;
			}
			document.getElementsByClassName("menu_list")[0].style.display="none";
			document.removeEventListener("click", admin.menu.close , false);
		},
		open:function(){
			document.getElementsByClassName("menu_list")[0].style.display="block";
			setTimeout(function(){
				document.addEventListener("click", admin.menu.close , false);
			});
			
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
				setTimeout(function(){
					location.reload();
				},500)
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
