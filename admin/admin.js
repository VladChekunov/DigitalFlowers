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
		dragAction:false,
		saveOrderPages:function(){
			var ids = [];
			for(var i=1;i<document.getElementsByClassName("pages_list")[0].children[0].children.length;i++){
				pageTrId = parseInt(document.getElementsByClassName("pages_list")[0].children[0].children[i].classList[1].replace("page-",""));
				ids.push(pageTrId);
			}
				admin.ajaxSend("/api/saveOrderPages/?ids="+ids.join(","),function(e){
					if(e.target.response.success==1){
						alert("Порядок сохранён.");
					}else{
						alert("Ошибка. "+e.target.response.error);
					}
				});
				
		},
		renderPageEditor:function(e){
					pageProps = '<a onclick=\"admin.pages.showPagesList()\" class=\"btn\" href=\"javascript://\">Back</a>';
					if(e.page_id!=-1){
						pageProps += '<a onclick=\"admin.pages.savePage('+e.page_id+')\" class=\"btn\" href=\"javascript://\">Save</a>'
					}else{
						pageProps += '<a onclick=\"admin.pages.addPage()\" class=\"btn\" href=\"javascript://\">Add</a>'
					}
					pageProps += '<div class="page_field"><div class="prop_title">URL</div><input class="url_field" value="'+e.page_url+'"></div>'
					pageProps += '<div class="page_field"><div class="prop_title">Title</div><input class="title_field" value="'+e.page_title+'"></div>'
					statusbar_status = "status_disabled"
					if(e.page_status==1){
						statusbar_status = "status_enabled"
					}
					pageProps += '<div class="page_field"><div class="prop_title">Status</div><div class="status '+statusbar_status+'"></div><input class="status_field" style="display:none;" value="'+e.page_status+'"></div>'
					pageProps += '<textarea class="source_field">'+e.page_source+'</textarea>'
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
		},
		newPage:function(){
					admin.pages.renderPageEditor({
						page_url:"",
						page_title:"",
						page_status:"",
						page_source:"",
						page_id:-1
					});
		},
		savePage:function(pageId){
			url=document.getElementsByClassName("url_field")[0].value;
			title=document.getElementsByClassName("title_field")[0].value;
			status=document.getElementsByClassName("status_field")[0].value;
			source=document.getElementsByClassName("source_field")[0].value;

			admin.ajaxSend("/api/savePage/?id="+pageId+"&url="+url+"&title="+title+"&source="+source+"&status="+status,function(e){
				if(e.target.response.success==1){
					alert("Сохранено");
				}else{
					alert("Ошибка. "+e.target.response.error);
				}
			});
		},
		addPage:function(){
			url=document.getElementsByClassName("url_field")[0].value;
			title=document.getElementsByClassName("title_field")[0].value;
			status=document.getElementsByClassName("status_field")[0].value;
			source=document.getElementsByClassName("source_field")[0].value;

			admin.ajaxSend("/api/addPage/?url="+url+"&title="+title+"&source="+source+"&status="+status,function(e){
				if(e.target.response.success==1){
					alert("Сохранено");
				}else{
					alert("Ошибка. "+e.target.response.error);
				}
			});
		},
		editPage:function(pageId){
			admin.ajaxSend("/api/GetPageById/?id="+pageId,function(e){
				if(e.target.response.success==1){
					console.log(e.target.response);
					
					admin.pages.renderPageEditor({
						page_url:e.target.response.page.url,
						page_title:e.target.response.page.title,
						page_status:e.target.response.page.status,
						page_source:e.target.response.page.pagesource,
						page_id:e.target.response.page.id
					});
				}else{
					alert("Ошибка. "+e.target.response.error);
				}
			});
		},
		removePage:function(pageId){
			//alert(pageId);
			var newBox = document.createElement("div");
			newBox.className="dialog_box";
			newBox.innerHTML="Вы точно хотите удалить страницу? <a class='btn' href='javascript://'>Удалить, ага, е, ага</a>";
			document.getElementsByClassName("admin_content")[0].appendChild(newBox);
			var closeWin = function(e){
				if(e.target.parentNode.className=="dialog_box"){
					admin.ajaxSend("/api/removePage/?id="+pageId,function(e){
						if(e.target.response.success==1){
							document.getElementsByClassName("page-"+pageId)[0].parentNode.removeChild(document.getElementsByClassName("page-"+pageId)[0])
						}else{
							alert("Ошибка. "+e.target.response.error);
						}
					});
				}
				document.removeEventListener("click", closeWin , false);
				newBox.parentNode.removeChild(newBox);
			}
			setTimeout(function(){
				document.addEventListener("click",  closeWin, false);
			},500)
		},
		showPagesList:function(){
			admin.ajaxSend("/api/GetPages/",function(e){
				if(e.target.response.success==1){
					pagesList="<a onclick=\"admin.pages.newPage()\" class=\"btn\" href=\"javascript://\">Add page</a><a onclick=\"admin.pages.saveOrderPages()\" class=\"btn\" href=\"javascript://\">Save Order</a>"
					pagesList+="<table class='pages_list'>\n<tr>\n\t<th>id</th>\n\t<th>title</th>\n\t<th>url</th>\n\t<th>Edit</th>\n\t<th>Order</th>\n\t<th>Remove</th>\n</tr>\n";
					for(var i=0;i<e.target.response.pages.length;i++){
						pagestatus="enabled_page";
						if(e.target.response.pages[i].status==0){
							pagestatus="disabled_page";
						}
						pagesList+="<tr class=\""+pagestatus+" page-"+e.target.response.pages[i].id+"\">\n\t<td>"+e.target.response.pages[i].id+"</td>\n\t<td>"+e.target.response.pages[i].title+"</td>\n\t<td>"+e.target.response.pages[i].url+"</td><td><a class=\"btn\" onclick=\"admin.pages.editPage("+e.target.response.pages[i].id+")\" href=\"javascript://\">Edit</a></td><td class=\"drag\">"+e.target.response.pages[i].order+"</td><td><a onclick=\"admin.pages.removePage("+e.target.response.pages[i].id+")\" class=\"btn\" href=\"javascript://\">Remove</a></td>\n</tr>\n";
					}
					pagesList+="</table>"
					document.getElementsByClassName("admin_content")[0].innerHTML=pagesList;

					var firElement;
					var secElement;
					var newElement = {innerHTML:null, className:null};


					var dragend = function(that){
						secElement = that.target.parentNode;
						if(secElement.classList!=undefined){
						if(secElement.classList[0]=="enabled_page" || secElement.classList[0]=="disabled_page"){
							newElement.innerHTML = firElement.innerHTML;
							newElement.className = firElement.className;

							firElement.innerHTML = secElement.innerHTML;
							firElement.className = secElement.className;

							secElement.innerHTML = newElement.innerHTML;
							secElement.className = newElement.className;
						}
						}
						document.removeEventListener("mouseup", dragend , false);
						document.addEventListener('mousedown', dragstart);

					}
					var dragstart = function(that){
						firElement = that.target.parentNode;
						if(firElement.classList!=undefined){
						if(firElement.classList[0]=="enabled_page" || firElement.classList[0]=="disabled_page"){
							document.removeEventListener("mousedown", dragstart , false);
							document.addEventListener('mouseup', dragend);
						}
						}
					}
					if(admin.pages.dragAction==false){
						admin.pages.dragAction=true;
						document.addEventListener('mousedown', dragstart);
					}
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
