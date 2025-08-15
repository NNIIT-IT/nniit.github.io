function showcell(idcontext,title,inyaz){
	var Tcontext=$("#"+idcontext)[0].innerHTML;;
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
}


$( document ).ready(function() {
	var n = $( "#wininfo" ).length;
	if(n==1) {
		$(".container").first().append('<div class="popupInfo" id="wininfo" ><span></span></div>');
	}
});
/*
$('.epinfo')  
	.mouseenter(function(event) {
	    fileid=$( this ).prev().attr("data-fileid");
	    var ob=$(this);
	    if(	fileid!=undefined){
			pos=$(this).offset();
			msg=$(this).attr("title");
			if(msg==undefined){
				$.getJSON("/api/fileSignInfo.php?id="+fileid+"&x="+pos.left+"&y="+pos.top,function(data){
					if($("#wininfo").length){	
						if(data.msg.length>2){
							//$("#wininfo").html("<span>"+data.msg+"</span>");
							//$("#wininfo").offset({left:data.x,top:data.y});
							//$("#wininfo").show();
							ob.attr("title",data.msg);
						}
					}
				});
			}else{
				//$("#wininfo").html("<span>"+msg+"</span>");
				//$("#wininfo").offset(pos);
				//$("#wininfo").show();

			}
	
		}
  	})
	.mouseleave(function() {
		if($("#wininfo").length){
			$("#wininfo").hide();
		}
	});
*/