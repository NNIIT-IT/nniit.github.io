	//if (!window.BX || BX.CatalogMenu) 		return;
	$(document).scroll(function(){
		//высота текста
		var aHBiglogo = $('#biglogo').children('span').height();
		//растояние между шапкой и текстом
		x=$('#biglogo').children('span');
		if(x.length>0)
			var aHBiglogoPos = $('#biglogo').children('span').offset().top;
		else 
			var aHBiglogoPos = 0;

		var hBottomY = $('header').offset().top+$('header').height();
		var deltaY=aHBiglogoPos-hBottomY;
		var aHBiglogo = $('#biglogo').children('span').height();
		var aTxt = $('#biglogo').is(":hidden");
		var sclolPos=$(this).scrollTop();

		if((deltaY<(aHBiglogo-50)) && (deltaY>=0)){
			scspan=(aHBiglogo-deltaY-50)/aHBiglogo;
			$('#biglogo').children('span').css("opacity",1-scspan);
			$('#logotext').css('opacity',scspan);
			$('#headerRow2Top').css("height",(100 - 25*scspan)+"px");//высота headerRow2Top = 100px
			/*$('#header').css("height",(100-10*scspan)+"px");//высота headerRow2Top = 100px*/
			$('#logoimg').css("width",(80-20*scspan)+"px");
			$('#logoimg0').css("width",(80-20*scspan)+"px");
			$('#logoimg1').css("width",(70-20*scspan)+"px");
			$('#logo').css("height",(85-15*scspan)+"px");
			$('#headerRow2Top').css("font-size",(1-scspan/3)+"em");
		}
		if(deltaY<0){
			$('#logotext').css('opacity',1);
			$('#biglogo').children('span').css("opacity",0);

			$('#headerRow2Top').css("font-size","0.8em");
			$('#headerRow2Top').css("height","90px");
			$('#logoimg').css("width","60px");
			$('#logoimg0').css("width","60px");
			$('#logoimg1').css("width","50px");
			$('#logo').css("height","70px");
		}
		if(deltaY>(aHBiglogo-50)){
			$('#logotext').css('opacity',0);
			$('#biglogo').children('span').css("opacity",1);
			$('#headerRow2Top').css("font-size","1em");
			$('#headerRow2Top').css("height","100px");
			$('#logoimg').css("width","85px");
			$('#logo').css("height","85px");

		}
	});
$(document).ready(function(){
	$("#langRu").on("click",function(){
		$.post("/local/templates/.default/selectlang.php",{lang:"ru"},function(){window.location.reload();});
	});
	$("#langEn").on("click",function(){
		$.post("/local/templates/.default/selectlang.php",{lang:"en"},function(){window.location.reload();});
	});
});
function action_lang(){
	if(document.getElementsByName('Lang')[0].value=="ru"){
		$.post("/local/templates/.default/selectlang.php",{lang:"ru"},function(){window.location.reload();});
	}
	if(document.getElementsByName('Lang')[0].value=="en"){
		$.post("/local/templates/.default/selectlang.php",{lang:"en"},function(){window.location.reload();});
	}
	//window.location = '?lang_ui=' + document.getElementsByName('Lang')[0].value;
}
