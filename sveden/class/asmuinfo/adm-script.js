function isFunctionAdm(functionToCheck)  {
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}

if (!isFunctionAdm("admregistration")){
	function admregistration(){}
	if($("#bx-panel-toggle").hasClass("bx-panel-toggle-on") && window.BX&&BX.admin){
		$(document).ready(function(){
			$(".maindocselement").each(
				function(i,elem){
					
					$(this).addClass("bx-context-toolbar-empty-area");
				}
			);
			$(".maindocselement").on( "mouseover", 
				function(){

					ereaId=$(this)[0].id;
					elId=$(this).attr("data-id");
					blockId=$(this).attr("data-iblock");
					sectionId=$(this).attr("data-section");
					if(sectionId==undefined){
						sectionId="";
					}
					xmlid=$(this).attr("data-xmlid");
					oppid=$(this).attr("data-opp");
					elname=$(this).attr("data-elname");
					if(elname==undefined){
						elname="";
					}
					props=$(this).attr("data-prop");
					if(props==undefined){
						props="";
					}
					
					url=$(this).attr("data-url");
					if(url=="" ) url=encodeURIComponent(window.location.pathname+window.location.search);
					title=$(this).attr("title");
					if(title=="")title=$(this).prop("title");
					if(title=="")title="Элемент";	
					showMenuiasmuinfo(ereaId,elId,blockId,title,xmlid,oppid,url,elname,sectionId,props);
				}
			);
			$(".maindocselementHB").each(
				function(i,elem){
							
					$(this).addClass("bx-context-toolbar-empty-area");
				}
			);

			$(".maindocselementHB").on( "mouseover", 
				function(evt){
					ereaId=$(this)[0].id;
					elId=$(this).attr("data-id");
					blockId=$(this).attr("data-iblock");
					
					if(blockId==undefined){
						
						blockId=$(this).parent().attr("data-iblock");
					}
					xmlid=$(this).attr("data-xmlid");
					oppid=$(this).attr("data-opp");
					nodel=$(this).attr("data-nodel")=="Y";
					title=$(this).attr("title");
					console.log(title);
					if(title=="" || title==undefined)title=$(this).prop("title");
					if(title=="" || title==undefined)title="Элемент";	
					url=$(this).attr("data-url");
					if(url==undefined){
						url=$(this).parent().attr("data-url");
					}
					if(url=="" || url==undefined) url=encodeURIComponent(window.location.pathname+window.location.search);
					uflevel=$(this).attr("data-level");
					showMenuiasmuinfoHB(ereaId,elId,blockId,title,xmlid,oppid,url,nodel,uflevel);
				}
				evt.preventDefault();
			);
			$(".maindocselementStaff").each(
				function(i,elem){
							
					$(this).addClass("bx-context-toolbar-empty-area");
				}
			);

			$(".maindocselementStaff").on( "mouseover", 
				function(){
					ereaId=$(this)[0].id;
					elId=$(this).attr("data-id");
					blockId=$(this).attr("data-iblock");
					
					if(blockId==undefined){
						
						blockId=$(this).parent().attr("data-iblock");
					}
					xmlid=$(this).attr("data-xmlid");
					oppid=$(this).attr("data-opp");
					nodel=$(this).attr("data-nodel")=="Y";
					title=$(this).attr("title");
					if(title=="")title=$(this).prop("title");
					if(title=="")title="Элемент";	
					url=$(this).attr("data-url");
					if(url==undefined){
						url=$(this).parent().attr("data-url");
					}
					if(url=="" || url==undefined) url=encodeURIComponent(window.location.pathname+window.location.search);
					uflevel=$(this).attr("data-level");
					showMenuiasmuinfoStaff(ereaId,elId,blockId,title,xmlid,oppid,url,nodel,uflevel);
				}
			);	
		});
		function showMenuiasmuinfo(ereaId,elId,blockId,title,xmlid,oppid,url,elname,sectionId,props){
			
			if (xmlid!=undefined && xmlid!=""){xmlid="&TYPEDOC="+xmlid;} else {xmlid="";}
			if (oppid!=undefined && oppid!=""){oppid="&OPP="+oppid;} else {oppid="";}			
			dialogEditOb=new BX.CAdminDialog(
			{'content_url':"/bitrix/admin/iblock_element_edit.php?IBLOCK_ID="+blockId+"&type=sveden&ID="+elId+"&lang=ru&filter_section="+sectionId+"&bxpublic=Y&from_module=iblock&props="+props+"&return_url="+url,'width':'952','height':'627'});
			dialogAddOb=new BX.CAdminDialog(
			{'content_url':"/bitrix/admin/iblock_element_edit.php?IBLOCK_ID="+blockId+xmlid+oppid+"&name="+elname+"&type=sveden&ID=0&lang=ru&force_catalog="+sectionId+"&filter_section="+sectionId+"&bxpublic=Y&from_module=iblock&props="+props+"&return_url="+url,'width':'952','height':'627'});
	
			menu=[{
				text: 'Добавить ',href: '#', className: 'menu-popup-item menu-popup-item-create',
				onclick: function(e,item){this.popupWindow.close();dialogAddOb.Show();}
			}];
			if(elId>0){
					menu.push({
						text: 'Изменить '+title,href:'#',className:'menu-popup-item menu-popup-item-edit',
						onclick:function(e,item){this.popupWindow.close();dialogEditOb.Show();}
					});
			}
		

			BX.PopupMenu.show('menu'+ereaId, BX(ereaId),menu,{ darkMode:true,autoHide:true,offsetTop:-8,zIndex:10000,offsetLeft:7,angle:{offset:1}});

			if(props!=""){
				lst=$("#tr_PROPERTY_507").find("select");
				//console.log(lst);
			}
		};
	function showMenuiasmuinfoHB(ereaId,elId,blockId,title,xmlid,oppid,url,noModify,uflevel){
		sess=BX.bitrix_sessid();
		Xlevel="";
		if(uflevel!="undefined"){Xlevel="&UF_LEVEL="+uflevel;} 
		UrlEditor="/sveden/class/asmuinfo/hbEdit.php?";
		//UrlEditor="/bitrix/admin/highloadblock_row_edit.php?bxpublic=Y&";
		//url=encodeURIComponent(window.location.href);
		//url=encodeURIComponent(window.location.pathname+window.location.search);
		dialogEditOb=new BX.CAdminDialog(
			{'content_url':UrlEditor+"ENTITY_ID="+blockId+"&ID="+elId+"&title="+title+"&type=sveden&lang=ru&mode=list&action=update&return_url="+url+Xlevel,'width':'952','height':'627'});
		dialogAddOb=new BX.CAdminDialog(
			{'content_url':UrlEditor+"ENTITY_ID="+blockId+"&type=sveden&lang=ru&mode=list&action=add&UF_OPP="+oppid+"&title=OOOOOO"+title+"&return_url="+url+Xlevel,'width':'952','height':'627'});
	
	
		dialogDelOb=new BX.CAdminDialog(
			{'content_url':UrlEditor+"ENTITY_ID="+blockId+"&ID="+elId+"&title="+title+"&mode=list&action=delete&type=sveden&lang=ru&sessid="+sess+"&return_url="+url,'width':'952','height':'627'});
	
		
		menu=[{
			text: 'Добавить ',href: '#', className: 'menu-popup-item menu-popup-item-create',
			onclick: function(e,item){this.popupWindow.close();dialogAddOb.Show();}
		}];
		if(nodel) {menu=[];}
		if(elId>0){
			menu.push({
			text: 'Изменить '+title,href:'#',className:'menu-popup-item menu-popup-item-edit',
			onclick:function(e,item){this.popupWindow.close();dialogEditOb.Show();}
			});
			if(!nodel){
				menu.push({
				text: 'Удалить '+title,href:'#',className:'menu-popup-item menu-popup-item-edit',
				onclick:function(e,item){
					this.popupWindow.close();
					if(confirm('Delete '+title+'?')){
						top.location.href=UrlEditor+"ENTITY_ID="+blockId+"&ID="+elId+"&title="+title+"&mode=list&action=delete&type=sveden&lang=ru&sessid="+sess+"&return_url="+url
					//dialogDelOb.Show();
					}
				}
				});
			}
		};
		BX.PopupMenu.show('menu'+ereaId, BX(ereaId),menu,{autoHide:true,offsetTop:-13,zIndex:10000,offsetLeft:20,angle:{offset:0}});
		
	}
	function showMenuiasmuinfoStaff(ereaId,elId,blockId,title,xmlid,oppid,url,noModify,uflevel){
		sess=BX.bitrix_sessid();
		Xlevel="";
		if(uflevel!="undefined"){Xlevel="&UF_LEVEL="+uflevel;} 
		UrlEditor="/sveden/class/asmuinfo/hbEdit-staff.php?";
		//UrlEditor="/bitrix/admin/highloadblock_row_edit.php?bxpublic=Y&";
		//url=encodeURIComponent(window.location.href);
		//url=encodeURIComponent(window.location.pathname+window.location.search);
		dialogEditOb=new BX.CAdminDialog(
			{'content_url':UrlEditor+"ENTITY_ID="+blockId+"&ID="+elId+"&type=sveden&lang=ru&mode=list&action=update&return_url="+url+Xlevel,'width':'952','height':'627'});
		dialogAddOb=new BX.CAdminDialog(
			{'content_url':UrlEditor+"ENTITY_ID="+blockId+"&type=sveden&lang=ru&mode=list&action=add&return_url="+url+Xlevel,'width':'952','height':'627'});
	
	
		dialogDelOb=new BX.CAdminDialog(
			{'content_url':UrlEditor+"ENTITY_ID="+blockId+"&ID="+elId+"&mode=list&action=delete&type=sveden&lang=ru&sessid="+sess+"&return_url="+url,'width':'952','height':'627'});
	
		
		menu=[{
			text: 'Добавить ',href: '#', className: 'menu-popup-item menu-popup-item-create',
			onclick: function(e,item){this.popupWindow.close();dialogAddOb.Show();}
		}];
		if(nodel) {menu=[];}
		if(elId>0){
			menu.push({
			text: 'Изменить '+title,href:'#',className:'menu-popup-item menu-popup-item-edit',
			onclick:function(e,item){this.popupWindow.close();dialogEditOb.Show();}
			});
			if(!nodel){
				menu.push({
				text: 'Удалить '+title,href:'#',className:'menu-popup-item menu-popup-item-edit',
				onclick:function(e,item){
					this.popupWindow.close();
					if(confirm('Delete '+title+'?')){
						top.location.href=UrlEditor+"ENTITY_ID="+blockId+"&ID="+elId+"&mode=list&action=delete&type=sveden&lang=ru&sessid="+sess+"&return_url="+url
					//dialogDelOb.Show();
					}
				}
				});
			}
		};
		BX.PopupMenu.show('menu'+ereaId, BX(ereaId),menu,{autoHide:true,offsetTop:-13,zIndex:10000,offsetLeft:20,angle:{offset:0}});
	
	}
	
	}
}
