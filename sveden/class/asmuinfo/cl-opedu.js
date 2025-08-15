var Dialog = new BX.CDialog({
		title:"",
		head: "",
		content: "",
		icon: 'head-block',
		resizable: true,
		draggable: true,
		height: '400',
		width: '600',
		buttons: [BX.CDialog.btnClose]
	});

function showcell(idcontext,title,inyaz){
	var Tcontext=BX(idcontext);
	if (inyaz==0){ subtitle="РЎРїРёСЃРѕРє РґРѕРєСѓРјРµРЅС‚РѕРІ";} else {subtitle="List of documents";};
	Dialog.SetTitle(subtitle);
	Dialog.SetContent(Tcontext);
	Dialog.SetHead("<h3>"+title+"</h3>");
	Dialog.Show();
}


function showcell2(idcontext,title,inyaz){
	var Tcontext=BX(idcontext);
	BX.removeClass(Tcontext,"hide");
	if (inyaz==0){ subtitle="Р РЋР С—Р С‘РЎРѓР С•Р С” Р Т‘Р С•Р С”РЎС“Р С