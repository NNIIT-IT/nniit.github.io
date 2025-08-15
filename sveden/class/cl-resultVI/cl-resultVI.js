//РІРІРѕРґ РґР°РЅРЅС‹С…
$(".resultVIfind div:nth-child(1)").on("click",
	function(e){
		ediv=$(".resultVIfind div:nth-child(2)");
		ediv.removeClass("hide");
	}
);	
function clearResultVIfind(){
		ediv=$(".resultVIfind div:nth-child(1)").children(1)[0];
		ediv.value="";
		$(".resultVIfind div:nth-child(2)").addClass("hide");
		$(".resultVIRow").show();
	
}
//РѕС‡РёСЃРєР° РІРІРѕРґР°
$(".resultVIfind div:nth-child(2)").on("click",
	function(e){
		clearResultVIfind();
	}
);
function resultVIFindOnPage(){
	flag = false;
	input_text=$(".resultVIfind div:nth-child(1)").children(1)[0].value;
	if(input_text.length > 2){
		$(".resultVIfind input").prop('disabled', true);
		$.post("/sveden/class/api/find-number-zayv.php",{"txt":input_text},function(data){
			

			$(".resultVIRow").each(function (index, el){
				
			var v  = $(el).children(1)[0].innerText;

				dataAr=data.split(",");
				dataArlength=dataAr.length;
				isElementExist=false;
				for (var i = 0; i < dataArlength; i++) {
					svalue=dataAr[i];
					
					isElementExist=isElementExist || v.includes(svalue);
				}


				isCaption=$(el).index()==0;
				if (isElementExist && !isCaption){
					$(el).show();
				
				}
				if (!isElementExist && !isCaption){
					$(el).hide();
				}
				
			});
		$(".resultVIfind input").prop('disabled', false);
		});
	} 
	//else if(input_text.length != 0) $(".error").html("РќРµРѕР±С…РѕРґРёРјРѕ РІРІРµСЃС‚Рё РЅРµ РјРµРЅРµРµ 3 СЃРёРјРІРѕР»РѕРІ");
	
}	
$(".resultVIfind div:nth-child(3)").on("click",function(e){
	
	resultVIFindOnPage();
});	
$(".resultVIfind input").keyup(function(e){
	if(e.keyCode == 13){
		resultVIFindOnPage();
		
	}
	if($(".resultVIfind input")[0].value.length>0){
		$(".resultVIfind div:nth-child(2)").removeClass("hide");
	}

	if((event.keyCode==8 || e.keyCode==46) && $(".resultVIfind input")[0].value.length==0){
		
		clearResultVIfind();
		
	}
});