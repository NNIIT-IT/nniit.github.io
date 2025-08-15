//РІРІРѕРґ РґР°РЅРЅС‹С…
$(".priemListfind div:nth-child(1)").on("click",
	function(e){
		ediv=$(".priemListfind div:nth-child(2)");
		ediv.removeClass("hide");
	}
);	

//РѕС‡РёСЃРєР° РІРІРѕРґР°
$(".priemListfind div:nth-child(2)").on("click",
	function(e){
		
		ediv=$(".priemListfind div:nth-child(1)").children(1)[0];
		ediv.value="";
		$(e.currentTarget).addClass("hide");
		$(".priemListRow").show();
		$(".priemListTableBody").hide();
	}
);
function priemListFindOnPage(){
	flag = false;
	input_text=$(".priemListfind div:nth-child(1)").children(1)[0].value;
	if(input_text.length > 2){
		$.post("/sveden/class/cl-priemList1/cl-priemList-find.php",{"txt":input_text},function(data){
			
			$(".priemListTableBody").css("height:auto");
			$(".priemListRow").each(function (index, el){
						
			    var v  = $(el).children(2)[0].innerText;
			
				isElementExist=v.includes(data);
				isCaption=$(el).index()==0;
				if (isElementExist && !isCaption){
					$(el).show();
					$(el).parents(".priemListTableBody").show();
	
				}
				if (!isElementExist && !isCaption){
					$(el).hide();
				}
	
			});
		});
	} 
	//else if(input_text.length != 0) $(".error").html("РќРµРѕР±С…РѕРґРёРјРѕ РІРІРµСЃС‚Рё РЅРµ РјРµРЅРµРµ 3 СЃРёРјРІРѕР»РѕРІ");
	
}	
$(".priemListfind div:nth-child(3)").on("click",function(e){
	
	priemListFindOnPage();
});	
$(".priemListfind input").keyup(function(event){
	if(event.keyCode == 13){
		priemListFindOnPage();
		
	}
});