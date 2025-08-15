<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?IncludeTemplateLangFile(__FILE__);

$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/jquery-1.9.1.min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/jquery-3.1.1.min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/script.js");
//$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/index.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/responsivevoice.min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/js.cookie.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/bvi-init.js?ver=1.0.8");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/bvi.min.js?ver=1.0.8");
$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_DEF."/css/bvi.min.css");
$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_DEF."/css/bvi-font.min.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_DEF."/js/slick.min.js");
$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_DEF."/css/slick.min.css");
CJSCore::Init(array("jquery","ajax","window","sidepanel","date"));
$realPageDir="";
$APPLICATION->AddChainItem($APPLICATION->GetTitle());
//if($_SERVER["REAL_FILE_PATH"]) $realPageDir=$_SERVER["REAL_FILE_PATH"];
if($realPageDir=="") 
	$realPageDir=$APPLICATION->GetCurDir();
else 
	$realPageDir=dirname($realPageDir);
if($APPLICATION->GetCurDir()!="/auth/" and !$err404 and $APPLICATION->GetCurDir() != '/'){?>
</div><!-- container -->
<!-- div class="right_block">
<?
																						  /*
	$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"vertical_multilevel", 
	array(
		"ROOT_MENU_TYPE" => "right",
		"MAX_LEVEL" => "4",
		"USE_EXT" => "Y",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"CHILD_MENU_TYPE" => "left",
		"DELAY" => "Y",
		"ALLOW_MULTI_SELECT" => "N",
		"COMPONENT_TEMPLATE" => "vertical_multilevel",
		"MENU_THEME" => "site",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);
*/ ?>
</div -->
</div><!-- pagecontainer -->
<?}
if ($USER->IsAuthorized()||$USER->isAdmin()){

	
	$APPLICATION->AddHeadScript("/local/templates/niit/adminscript.js");
		$userSotr=in_array(1,$USER->GetUserGroupArray());
		$currentscript=$APPLICATION->GetCurDir()."index.js";
		$currentstyle=$APPLICATION->GetCurDir()."index.css";
		$fullFileScript=$_SERVER["DOCUMENT_ROOT"].$currentscript;
		$fullFileStyle=$_SERVER["DOCUMENT_ROOT"].$currentstyle;
		if(!file_exists($fullFileScript)) file_put_contents($fullFileScript, "");
		if(!file_exists($fullFileStyle)) file_put_contents($fullFileStyle, "");
		$APPLICATION->AddHeadScript($currentscript);
		$APPLICATION->SetAdditionalCss($currentstyle);
		//кнопка правки стилей на админской панели
		$APPLICATION->AddPanelButton(
			array(
				"ID" => "CSS_page",
				"TEXT" => "CSS страницы",
				"TYPE" => "BIG", // большая кнопка
				// выполнение JavaScript при нажатии на кнопку
				"HREF" => "javascript:btneditfile('".$currentstyle."');",
				"ICON" => "bx-panel-edit-page-icon",
			  )
		);
	
		//кнопка правки скриптов на админской панели
		$APPLICATION->AddPanelButton(
			array(
				"ID" => "JS_page",
				"TEXT" => "JS страницы",
			 "TYPE" => "BIG", // большая кнопка
				// выполнение JavaScript при нажатии на кнопку
				"HREF" => "javascript:btneditfile('".$currentscript."');",
				"ICON" => "bx-panel-edit-page-icon",
			  )
		);
}?>
<footer class="footer">
<div class="containerFooter">
<?
		$link1='?login='.$USER->GetLogin().'&backurl='.$_SERVER['REQUEST_URI'];
		if ($USER->IsAuthorized()){
			echo '<a class="key" style="text-decoration: none;" href="'.$link1.'">'.$USER->GetLastName()." ".$USER->GetFirstName().'</a>';
			echo ' <a  href="'.$APPLICATION->GetCurPageParam("logout=yes", array("login","logout","register","forgot_password","change_password")).'"> Выйти</a>';
		}
			else{
			if (mb_substr($_SERVER['REQUEST_URI'],0,6)!="/auth/")
			echo '<a class="key" href="/auth/?backurl='.$_SERVER['REQUEST_URI'].'">Вход на сайт</a>';
			//echo '<span class="key">Вход на сайт временно недоступен</span>';
			}

	?>
</div>
</footer>
</body>
</html>