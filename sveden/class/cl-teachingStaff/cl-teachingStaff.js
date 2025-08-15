function setFld(){
	searchStr1=$("#filterInput1").val();
	searchStr2="";
if($("#filterInput2")[0].selectedIndex>0)
	searchStr2=$("#filterInput2")[0].selectedOptions[0].value;
//console.log($("#filterInput2")[0].selectedIndex);
console.log(searchStr2);

$(".prepodrow").each(function(index) {
		fld=this.childNodes[1];
		hid1=false;	hid2=false;
		if((fld!=undefined) && (searchStr1!="")){
			if(fld.textContent.search(searchStr1)>=0){
				hid1=false;
			}else{
				hid1=true;
			}
		}
		fld=this.childNodes[11];
		if((fld!=undefined)  && (searchStr2!="")){
			if(fld.textContent.search(searchStr2)>=0){
				hid2=false;
			}else{
				hid2=true;
			}
		}

	if(hid1 || hid2){ 
		$(this).addClass("hide");
	}else{ 
		$(this).removeClass("hide");

	}
});

}
//var $j = jQuery.noConflict();
    $(document).ready(function() {
        $("#filterInput1").keyup(function(){setFld();});

 		$("#filterInput2").change(function(){setFld();});

		$(".filterInputClear").click(function(){
			$(".filterInput").val("");
			$(".prepodrow").removeClass("hide");
		});
		
    });


