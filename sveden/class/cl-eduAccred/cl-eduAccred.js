/*
$f= showcell && getType.toString.call(showcell) === '[object Function]';

if(!$f){
function showcell(idcontext,title,inyaz){
	var Tcontext=BX(idcontext);
	if (inyaz==0){ subtitle="Список документов";} else {subtitle="List of documents";};

	var Dialog = new BX.CDialog({
			title:subtitle,
			head: "<h3>"+title+"</h3>",
			content: Tcontext,
			icon: 'head-block',
			resizable: true,
			draggable: true,
			height: '400',
			width: '600',
			buttons: [BX.CDialog.btnClose]
		});
	Dialog.Show();
}}
*/