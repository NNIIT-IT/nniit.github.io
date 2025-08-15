function isFunction(functionToCheck)  {
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}
if (!isFunction("admregistration")){
	function admregistration(){}
	if($("#bx-panel-toggle").hasClass("bx-panel-toggle-on") && window.BX&&BX.admin){
		$(document).ready(function(){
			//элементы
			$(".iblockElement").each(
				function(i,elem){
					$(this).addClass("bx-context-toolbar-empty-area");
				}
			);
			$(".iblockElement").on( "mouseover", 
				function(){
					ereaId=$(this)[0].id;
					elId=$(this).attr("data-el");
					blockId=$(this).attr("data-iblock");
					sectionId=$(this).attr("data-cat");
					url=encodeURIComponent(window.location.pathname+window.location.search);
					title=$(this).attr("title");
					if(title==undefined)title=$(this).attr("data-title");
					if(title==undefined)title="Элемент";	
					showMenuIblockElement(ereaId,elId,blockId,title,url,sectionId);
				}
			);
			$(".journalName").each(
				function(i,elem){
					$(this).addClass("bx-context-toolbar-empty-area");
				}
			);
			$(".journalName").on( "mouseover", 
				function(){
					ereaId=$(this)[0].id;
					elId=$(this).attr("data-id");
					showMenuJournal(ereaId,elId);
				}
			);

			function showMenuJournal(ereaId,elId){
				title="Журнал";
				dialogEditOb=new BX.CAdminDialog(
				{'content_url':"/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=24&type=journal&ID="+elId+"&lang=ru&force_catalog=0&bxpublic=Y&from_module=iblock&return_url=/",'width':'952','height':'627'});
				menu=[];
				if(elId>0){
						menu.push({
							text: 'Изменить '+title,href:'#',className:'menu-popup-item menu-popup-item-edit',
							onclick:function(e,item){this.popupWindow.close();dialogEditOb.Show();}
						});
				}
	
				BX.PopupMenu.show('menu'+ereaId, BX(ereaId),menu,{autoHide:true,offsetTop:-13,zIndex:10000,offsetLeft:20,angle:{offset:0}});
			};

			function showMenuIblockElement(ereaId,elId,blockId,title,url,sectionId){
				if(sectionId!=""){sect="&filter_section="+sectionId;}else{sect="&filter_section=";}
				dialogEditOb=new BX.CAdminDialog(
				{'content_url':"/bitrix/admin/iblock_element_edit.php?IBLOCK_ID="+blockId+"&type=journal&ID="+elId+"&lang=ru&force_catalog="+sectionId+"&bxpublic=Y&from_module=iblock&return_url="+url,'width':'952','height':'627'});
				dialogAddOb=new BX.CAdminDialog(
				{'content_url':"/bitrix/admin/iblock_element_edit.php?IBLOCK_ID="+blockId+"&type=journal&ID=0&lang=ru&force_catalog="+sectionId+"&bxpublic=Y&from_module=iblock&return_url="+url,'width':'952','height':'627'});
	
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
	
				BX.PopupMenu.show('menu'+ereaId, BX(ereaId),menu,{autoHide:true,offsetTop:-13,zIndex:10000,offsetLeft:20,angle:{offset:0}});
			};
			//issue
			$(".issueItem").each(
				function(i,elem){
					$(this).addClass("bx-context-toolbar-empty-area");
				}
			);
			$(".issueItem").on( "mouseover", 
				function(){
					ereaId=$(this)[0].id;
					elId=$(this).attr("data-id");
					url=encodeURIComponent(window.location.pathname+window.location.search);
					title=$(this).attr("title");
					if(title==undefined)title=$(this).attr("data-title");
					if(title==undefined)title="Элемент";	
					showMenuIssueElement(ereaId,elId,title,url);
				}
			);
			function showMenuIssueElement(ereaId,elId,title,url){
				dialogEditOb=new BX.CAdminDialog(
				{'content_url':"/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=26&type=journal&ID="+elId+"&lang=ru&force_catalog=0&bxpublic=Y&from_module=iblock&return_url="+url,'width':'952','height':'627'});
				dialogAddOb=new BX.CAdminDialog(
				{'content_url':"/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=26&type=journal&ID=0&lang=ru&force_catalog=0&bxpublic=Y&from_module=iblock&return_url="+url,'width':'952','height':'627'});
	
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
	
				BX.PopupMenu.show('menu'+ereaId, BX(ereaId),menu,{autoHide:true,offsetTop:-13,zIndex:10000,offsetLeft:20,angle:{offset:0}});
			};
	});
}}