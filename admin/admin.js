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
		editPage:function(pageId){
			//Back
			//
			//Title
			//Url
			//...
			admin.ajaxSend("/api/GetPageById/?id="+pageId,function(e){
				if(e.target.response.success==1){
					console.log(e.target.response);
					pageProps = '<a onclick=\"admin.pages.showPagesList()\" class=\"btn\" href=\"javascript://\">Back</a><a onclick=\"admin.pages.savePage('+e.target.response.page.id+')\" class=\"btn\" href=\"javascript://\">Save</a>'
					pageProps += '<div class="page_field"><div class="prop_title">URL</div><input value="'+e.target.response.page.url+'"></div>'
					pageProps += '<div class="page_field"><div class="prop_title">Title</div><input value="'+e.target.response.page.title+'"></div>'
					statusbar_status = "status_disabled"
					if(e.target.response.page.status==1){
						statusbar_status = "status_enabled"
					}
					pageProps += '<div class="page_field"><div class="prop_title">Status</div><div class="status '+statusbar_status+'"></div><input class="status_field" style="display:none;" value="'+e.target.response.page.status+'"></div>'
					pageProps += '<textarea>'+e.target.response.page.source+'</textarea>'
					document.getElementsByClassName("admin_content")[0].innerHTML=pageProps;
					document.getElementsByClassName("status")[0].onclick=function(){
						if(document.getElementsByClassName("status_field")[0].value==1){
							document.getElementsByClassName("status_field")[0].value=0;
							document.getElementsByClassName("status")[0].classList.remove("status_enabled");
							document.getElementsByClassName("status")[0].classList.add("status_disabled");
						}else{
							document.getElementsByClassName("status_field")[0].value=1;
							document.getElementsByClassName("status")[0].classList.remove("status_disabled");
							document.getElementsByClassName("status")[0].classList.add("status_enabled");
						}
					}
				}else{
					alert("Ошибка. "+e.target.response.error);
				}
			});
		},
		showPagesList:function(){
			admin.ajaxSend("/api/GetPages/",function(e){
				if(e.target.response.success==1){
					pagesList="<a onclick=\"admin.addPage()\" class=\"btn\" href=\"javascript://\">Add page</a><a onclick=\"admin.saveOrderPages()\" class=\"btn\" href=\"javascript://\">Save Order</a>"
					pagesList+="<table class='pages_list'>\n<tr>\n\t<th>id</th>\n\t<th>title</th>\n\t<th>url</th>\n\t<th>Edit</th>\n\t<th>Order</th>\n\t<th>Remove</th>\n</tr>\n";
					for(var i=0;i<e.target.response.pages.length;i++){
						pagestatus="enabled_page";
						if(e.target.response.pages[i].status==0){
							pagestatus="disabled_page";
						}
						pagesList+="<tr class=\""+pagestatus+"\">\n\t<td>"+e.target.response.pages[i].id+"</td>\n\t<td>"+e.target.response.pages[i].title+"</td>\n\t<td>"+e.target.response.pages[i].url+"</td><td><a class=\"btn\" onclick=\"admin.pages.editPage("+e.target.response.pages[i].id+")\" href=\"javascript://\">Edit</a></td><td style=\"drag\">v/^</td><td><a onclick=\"admin.editPage("+e.target.response.pages[i].id+")\" class=\"btn\" href=\"javascript://\">Remove</a></td>\n</tr>\n";
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
