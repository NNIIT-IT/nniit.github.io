<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Аутетентификация");
$APPLICATION->SetTitle("Аутентификация");
?>
<section class="content">
<div class="container">
<?
global $USER;
 if($USER->IsAuthorized()): ?> 
<h1>Вы уже авторизированы</h1>
<?
	$bURL=htmlspecialchars($_GET("backurl"));
	if($buckURL!="") header("Location:$bURL");
	else header("Location:https://asmu.ru/");
?>
 <br>
<h1>Изменение данных учетной записи пользователя<br> электронных ресурсов АГМУ</h1>
<?
	$iduser=$USER->GetID();
/*
	$APPLICATION->IncludeComponent(
	"bitrix:forum.user.profile.edit", 
	"profile_edit", 
	array(
		"UID" => $iduser,
		"URL_TEMPLATES_PROFILE_VIEW" => "/personal/",
		"USER_PROPERTY" => array(
			0 => "NAME",
			1 => "UF_REGALII",
			2 => "UF_KLASSIF",
			3 => "UF_KF_VAL2",
			4 => "UF_DOL",
			5 => "UF_YCHS",
			6 => "UF_YCHZ",
			7 => "UF_STAG1",
			8 => "UF_STAG2",
			9 => "UF_WTEL",
			10 => "UF_AUD",
			11 => "UF_WEMAIL",
			12 => "UF_KORPUS",
			13 => "UF_BSPRAVKA",
			14 => "UF_UPKV",
			15 => "UF_GRKONS",
			16 => "UF_YEARIN",
			17 => "UF_SPEC",
			18 => "UF_PODR",
		),
		"SET_NAVIGATION" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "0",
		"CACHE_NOTES" => "",
		"SET_TITLE" => "N",
		"COMPONENT_TEMPLATE" => "profile_edit"
	),
	false
);
else
 echo '<a href="/auth/?login=&backurl=/index.php"> Авторизация </a> <br>';
 */
endif;?>
&nbsp;<br>
 <br>
 <br>
</div>
 </section> 
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>