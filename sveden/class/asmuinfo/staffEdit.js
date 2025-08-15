function add_dst(disc="",opp=""){
	cn=$('div.discname').length+1;
	id="discopp_"+cn;
	ver=Math.floor(Date.now() / 1000);
	htm="<div class=\"discname\" id=\""+id+"\">";
	htm+="<div class=\"discColl\"></div><div class=\"discColl\"></div><div class=\"discbtnColl\">";
	htm+="<button onclick=\"$('#"+id+"').remove();savechange(); return false;\" class=\"btndel\">";
	htm+="<img src=\"/sveden/icons/delete.png\" style=\"width:20px\"></button></div>";
	htm+="</div>";
	$('#disclist').append(htm);
	//РґРѕР±Р°РІР»СЏРµРј СЃС‚СЂРѕРєСѓ
	disc="";
	//РїРѕР»СѓС‡Р°РµРј СЃРїРёСЃРѕРє СЃРїРµС†РёР°Р»СЊРЅРѕСЃС‚РµР№
	$.post("/sveden/class/asmuinfo/get_spec_js.php", {'id':id,'DISC':0,'VER':ver}, function(result){
		oppCell=$("#"+id).children()[0];
		$(oppCell).html(result);
		opp=$(oppCell).find('option:selected').val();
		$.post("/sveden/class/asmuinfo/get_disc_js.php", {'id':id,'VER':ver,'OPP':opp,'DISC':0}, function(result){
			discCell=$("#"+id).children()[1];
			$(discCell).html(result);
			savechange();
		});
	});
	return false;

}
function newopplistkdr(id,disc,ver){

	$.post("/sveden/class/asmuinfo/get_spec_js.php", {'id':id,'DISC':disc,'VER':ver}, function(result){$("#D"+id).html(result);savechange();});
	
	return false;
}
function oppchange(el){
	ver=Math.floor(Date.now() / 1000);
	opp=$(el).find(':selected').val();
	rowOb=$($(el).parent().parent());
	discObCell=rowOb.children()[1];
	discOb=discObCell.childNodes[0];
	disc=$(discOb).find('option:selected').val();
	$.post("/sveden/class/asmuinfo/get_disc_js.php", {'OPP':opp,'DISC':disc}, function(result){
		$(discObCell).html(result);
		savechange();
	});	
}
function discchange(el){
  savechange();
}
function savechange(){

	xvalue=[];
	//$("#input_UF_TEACHINGDISCIPLIN").val("");
	$('.discname').each(function(i, elem){
		oppSelect=$(elem).children()[0].childNodes[0];
		discSelect=$(elem).children()[1].childNodes[0];
		opp=$(oppSelect).find("option:selected" ).val();
		discName=$(discSelect).find("option:selected" ).text();
		discid=$(discSelect).find("option:selected" ).val();
		if(discid!=0){
			console.log(opp,discName);		
			xvalue.push({"opp":opp,"disc":discName});
		}
        });
	json = JSON.stringify(xvalue).replace(/\"/g, '&quot;');
	if(json!="[]"){
		$("#input_UF_TEACHINGDISCIPLIN").val(json);
	}
	
}