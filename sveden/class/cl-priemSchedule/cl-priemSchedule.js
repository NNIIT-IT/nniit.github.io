//РІРІРѕРґ РґР°РЅРЅС‹С…
console.log("priemSchedule.js load");
$(".priemSchedulefind div:nth-child(1)").on("click",
	function(e){
		ediv=$(".priemSchedulefind div:nth-child(2)");
		ediv.removeClass("hide");
	}
);	
function clearpriemSchedulefind(){
		ediv=$(".priemSchedulefind div:nth-child(1)").children().eq(0).val("");
		$(".priemSchedulefind div:nth-child(2)").addClass("hide");
		$(".priemScheduleRow").show();
		$(".priemScheduleRowCaption").show();
		$(".priemScheduleTable").parent().hide(); 
		$(".priemScheduleCell").show(); 
		$(".priemScheduleTableBody").hide();
		$(".priemScheduleTime").show();
		$(".priemScheduleTimeInd").html("");
		$(".priemScheduleAddr").show();
		$(".priemSchedulefindError").hide();

}
//РѕС‡РёСЃРєР° РІРІРѕРґР°
$(".priemSchedulefind div:nth-child(2)").on("click",
	function(e){
		clearpriemSchedulefind();
	}
);
function priemScheduleFindOnPage(){
	$(".priemSchedulefindError").hide();
	$(".priemScheduleRow").hide();
	$(".priemScheduleRowCaption").hide();
	$(".priemScheduleTable").parent().hide(); 
	$(".priemScheduleCell").show(); 
	$(".priemScheduleTableBody").hide();
	//$(".priemScheduleTimeInd").html("");
	//$(".priemScheduleTime").hide();
	$("#nofind").hide();
	flag = false;

	input_text=$(".priemSchedulefind div:nth-child(1)").children()[0].value;
	let exams=[];
		
	
	if(input_text.length > 2){
		wait = BX.showWait("priemSchedulefind");
		$(".priemSchedulefind input").prop('disabled', true);
		$.post("/sveden/class/cl-priemSchedule/findUserShedule.php",{"txt":input_text},function(data){
			
			let obj = JSON.parse(data);
			flag=false;
			for (k in obj){
				$(".priemScheduleRow").each(function (index, row){
					rowc1=$(row).children().eq(0);	
					
					let v= rowc1.text();
					rzv=rowc1.hasClass("rezerv");

					
					if(v==k || rzv==true){
						let prnt=$(row).parent(".priemScheduleTable").children().eq(0);
	

						$(prnt).children().each(function (index, colCaption){

							dd= $(colCaption)[0].innerText;

							if(dd.indexOf(obj[k].s)>-1 ) {
								if(rzv==false){
									let cell=$(row).children().eq(index);
									let cellTime=cell.children().eq(0);
									
									let cellTimeInd=cell.children().eq(2);
									
									cellTimeInd.html("РІ "+obj[k].t);

									$(row).parent().parent().show();//Р·Р°РіРѕР»РѕРІРѕРє С‚Р°Р±Р»РёС†С‹
									$(prnt).show();//СЃС‚СЂРѕРєР° С€Р°РїРєРё С‚Р°Р±Р»РёС†С‹
									$(prnt).parent().show();//С‚Р°Р±Р»РёС†Р°
									$(row).show();//СЃС‚СЂРѕРєР° С‚Р°Р±Р»РёС†С‹
									if(obj[k].t!=""){
										cellTimeInd.show();
										cellTime.hide();
									}else {
										cellTimeInd.hide();
										cellTime.show();
									}
									//$(row).children().each(function(iCell, cCell){$(cCell).children().eq(0).hide();});
									flag=true;
									$(colCaption).attr("data-visible",1);
									$(colCaption).parent().attr("data-visible",1);
									

								}else{
									prevTbl=$(row).parent(".priemScheduleTable").prev().prev().children().first();

									
									if(prevTbl.attr("data-visible")=="1"){
									
										$(colCaption).attr("data-visible",1);
										$(row).parent().parent().show();//Р·Р°РіРѕР»РѕРІРѕРє С‚Р°Р±Р»РёС†С‹
										$(prnt).show();//СЃС‚СЂРѕРєР° С€Р°РїРєРё С‚Р°Р±Р»РёС†С‹
										$(prnt).parent().show();//С‚Р°Р±Р»РёС†Р°
										$(row).show();//СЃС‚СЂРѕРєР° С‚Р°Р±Р»РёС†С‹
									
									
									}
								}
								
							}
						});

						
					}
				});
				
			};

		$(".priemScheduleTable").each(function (xindex, xtable){

			let caprow=$(xtable).children().eq(0);

			$(caprow).children().each(function (index, rowcol){
			
				if(index>0){
					vv=$(rowcol).attr("data-visible");
					if(vv==undefined) {
						//$(rowcol).css("color","gray");
						$(xtable).children().each(function (index2, rowcol2){
							$(rowcol2).children().eq(index).hide();
							
						});
					}else{

					}  
				}
			});
			
		});

		$(".priemSchedulefind input").prop('disabled', false);
		if(flag==false) {
			$(".priemSchedulefindError").show();
		}
		BX.closeWait("priemSchedulefind", wait);
	});//post
	} 
	//else if(input_text.length != 0) $(".error").html("РќРµРѕР±С…РѕРґРёРјРѕ РІРІРµСЃС‚Рё РЅРµ РјРµРЅРµРµ 3 СЃРёРјРІРѕР»РѕРІ");
}	
$(".priemSchedulefind div:nth-child(3)").on("click",function(e){
	
	priemScheduleFindOnPage();
});	
$(".priemSchedulefind input").keyup(function(e){
	if(e.keyCode == 13){
		priemScheduleFindOnPage();
		
	}
	if($(".priemSchedulefind input")[0].value.length>0){
		$(".priemSchedulefind div:nth-child(2)").removeClass("hide");
	}

	if((event.keyCode==8 || e.keyCode==46) && $(".priemSchedulefind input")[0].value.length==0){
		
		clearpriemSchedulefind();
		
	}
});
