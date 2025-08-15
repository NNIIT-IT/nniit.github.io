function fileviewpdf(id){
	dlgid='viewpdf'+id;
	var popup = new BX.CDialog({id: 'viewpdf'+id,'width':document.documentElement.clientWidth-60,'height':800,'title': 'Просмотр документа','content_url': '/sveden/files/?id='+id+'&json=1','draggable': true,'resizable': true,'buttons': [BX.CDialog.btnClose]});
	popup.Show();
};

$(".fileviewpdf").on("click",function(){
	id=$(this).attr('data-fileid');
	dlgid='viewpdf'+id;
	var popup = new BX.CDialog({id: 'viewpdf'+id,'width':document.documentElement.clientWidth-60,'height':800,'title': 'Просмотр документа','content_url': '/sveden/files/?id='+id+'&json=1','draggable': true,'resizable': true,'buttons': [BX.CDialog.btnClose]});
	popup.Show();
});