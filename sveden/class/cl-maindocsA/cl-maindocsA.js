console.log("start cl-maindocument.js");
$(document).ready(function() {
	expandPropetyItem=$("#expandPropetyItem").val();
	$('div').filter(function(index,el) {
		x=$(el).attr('itemprop');
		if(x!=undefined) {
			if(x==expandPropetyItem) {
//				$(el).css({"color":"blue"});
				elp=$(el);
				elp.find('div:hidden').show();
				elpf=true;
				while(elpf){
					console.log(elp);
					elpv=elp.is(":visible");
					console.log(elpv);
					elp.show();
					elp=elp.parent();
					elpf=elp.length>0 && !elpv;
				}
			}
		}
	});
});