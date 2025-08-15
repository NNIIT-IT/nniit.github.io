<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?if (!function_exists(opp_subscript)){	
	function opp_subscript($p1,$p2)
	{
	$e_html='<form action="/auth/npo/auth_npo.php" method="POST">';
	$e_html.='<input type="submit" value="подписаться" onclick="return=false">';
	$e_html.='<input hidden name="PODPISKA_TYPE" value="'.$p1.'">';
	$e_html.='<input hidden name="PODPISKA_ID" value="'.$p2.'">';
	$e_html.='<input hidden name="B_URL" value="'.htmlspecialchars($_SERVER['PHP_SELF']).'">';
	//$e_html.='<input hidden name="PHP_URL" value="/auth/npo/reg_in_tbl.php">';
	//$e_html.='<input hidden name="ERROR" value="">';
	$e_html.='</form>';
	return 	$e_html;
	}	
}
?>
<?
echo htmlspecialchars($_SERVER['PHP_SELF']);
echo opp_subscript('1','6790');
echo opp_subscript('2','999');
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>