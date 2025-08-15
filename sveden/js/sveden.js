/*hidedivlink*/

    $(document).on('click', '.hidedivlink', function(){
	//alert("Ok");
      if ($(this).next().is(":visible"))
	{$(this).next().slideUp("slow");} 
	else 
	{$(this).next().slideDown("slow"); }

    });