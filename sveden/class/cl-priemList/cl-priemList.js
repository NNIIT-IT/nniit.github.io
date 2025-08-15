//РІРІРѕРґ РґР°РЅРЅС‹С…
$(".priemListfind div:nth-child(1)").on("click",
	function(e){
		ediv=$(".priemListfind div:nth-child(2)");
		ediv.removeClass("hide");
	}
);	
function clearListfind(){
		ediv=$(".priemListfind div:nth-child(1)").children(1)[0];
		ediv.value="";
		$(".priemListfind div:nth-child(2)").addClass("hide");
		$(".priemListRow").show();
		$(".priemListRowCaption").show();
		$(".priemListTable").show(); 
		$(".priemListEdu").show(); 
		$(".priemListTableBody").hide();
}
//РѕС‡РёСЃРєР° РІРІРѕРґР°
$(".priemListfind div:nth-child(2)").on("click",
	function(e){
		clearListfind();
	}
);
function priemListFindOnPage(){
	flag = false;
	$(".priemListRowCaption").hide();
	$(".priemListTable").hide(); 
	$(".priemListEdu").hide(); 
	input_text=$(".priemListfind div:nth-child(1)").children(1)[0].value;
	if(input_text.length > 2){
		waitpriemListfind = BX.showWait("priemListfind");
		$(".priemListfind input").prop('disabled', true);
		$.post("/sveden/class/api/find-number-zayv.php",{"txt":input_text},function(data){
			
			//$(".priemListTableBody").css("height:auto");
			$(".priemListRow").each(function (index, el){
				
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
					$(el).parents(".priemListEdu").show(); 
					$(el).parents(".priemListTable").show();		 
					$(el).parents(".priemListTableBody").show();
					$(el).prevAll('.priemListRow').prev(".priemListRowCaption").show();
					$(el).show();
					
					
					
				}
				if (!isElementExist && !isCaption){
					$(el).hide();
				}
				
				
			});
		$(".priemListfind input").prop('disabled', false);
		});
		BX.closeWait("priemListfind", waitpriemListfind);
	} 
	//else if(input_text.length != 0) $(".error").html("РќРµРѕР±С…РѕРґРёРјРѕ РІРІРµСЃС‚Рё РЅРµ РјРµРЅРµРµ 3 СЃРёРјРІРѕР»РѕРІ");
	
}	
$(".priemListfind div:nth-child(3)").on("click",function(e){
	
	priemListFindOnPage();
});	
$(".priemListfind input").keyup(function(e){
	if(e.keyCode == 13){
		priemListFindOnPage();
		
	}
	if($(".priemListfind input")[0].value.length>0){
		$(".priemListfind div:nth-child(2)").removeClass("hide");
	}

	if((event.keyCode==8 || e.keyCode==46) && $(".priemListfind input")[0].value.length==0){
		
		clearListfind();
		
	}
});