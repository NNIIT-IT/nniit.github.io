//РІРІРѕРґ РґР°РЅРЅС‹С…
$(".priemKonkursfind div:nth-child(1)").on("click",
	function(e){
		ediv=$(".priemKonkursfind div:nth-child(2)");
		ediv.removeClass("hide");
	}
);	
function clearpriemKonkursfind(){
		ediv=$(".priemKonkursfind div:nth-child(1)").children(1)[0];
		ediv.value="";
		$(".priemKonkursfind div:nth-child(2)").addClass("hide");
		$(".priemKonkursRow").show();
		$(".priemKonkursRowCaption").show();
		$(".priemKonkursTable").show(); 
		$(".priemKonkursEdu").show(); 
		$(".priemKonkursTableBody").hide();
}
//РѕС‡РёСЃРєР° РІРІРѕРґР°
$(".priemKonkursfind div:nth-child(2)").on("click",
	function(e){
		clearpriemKonkursfind();
	}
);
function priemKonkursFindOnPage(){
	flag = false;
	$(".priemKonkursRowCaption").hide();
	$(".priemKonkursTable").hide(); 
	$(".priemKonkursEdu").hide(); 
	input_text=$(".priemKonkursfind div:nth-child(1)").children(1)[0].value;
	if(input_text.length > 2){
		waitpriemKonkursfind = BX.showWait("priemKonkursfind");
		$(".priemKonkursfind input").prop('disabled', true);
		$.post("/sveden/class/api/find-number-zayv.php",{"txt":input_text},function(data){
			
			//$(".priemListTableBody").css("height:auto");
			$(".priemKonkursRow").each(function (index, el){
				
			var v  = $(el).children(1)[1].innerText;
				dataAr=data.split(",");
				dataArlength=dataAr.length;
				isElementExist=false;
				for (var i = 0; i < dataArlength; i++) {
					svalue=dataAr[i];
					
					isElementExist=isElementExist || v.includes(svalue);
				}
				isCaption=$(el).index()==0;
				if (isElementExist && !isCaption){
					$(el).parents(".priemKonkursEdu").show(); 
					$(el).parents(".priemKonkursTable").show();		 
					$(el).parents(".priemKonkursTableBody").show();
					$(el).prevAll('.priemKonkursRow').prev(".priemKonkursRowCaption").show();
					$(el).show();
					
					
					
				}
				if (!isElementExist && !isCaption){
					$(el).hide();
				}
				
			});
		$(".priemKonkursfind input").prop('disabled', false);
		});
		BX.closeWait("priemKonkursfind", waitpriemKonkursfind);
	} 
	//else if(input_text.length != 0) $(".error").html("РќРµРѕР±С…РѕРґРёРјРѕ РІРІРµСЃС‚Рё РЅРµ РјРµРЅРµРµ 3 СЃРёРјРІРѕР»РѕРІ");
	
}	
$(".priemKonkursfind div:nth-child(3)").on("click",function(e){
	
	priemKonkursFindOnPage();
});	
$(".priemKonkursfind input").keyup(function(e){
	if(e.keyCode == 13){
		priemKonkursFindOnPage();
		
	}
	if($(".priemKonkursfind input")[0].value.length>0){
		$(".priemKonkursfind div:nth-child(2)").removeClass("hide");
	}

	if((event.keyCode==8 || e.keyCode==46) && $(".priemKonkursfind input")[0].value.length==0){
		
		clearpriemKonkursfind();
		
	}
});