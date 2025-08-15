<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$pageTitle=$MESS["pagetitle"];
$APPLICATION->SetTitle($MESS["pagetitle"]);
$indexfile=str_replace($_SERVER["DOCUMENT_ROOT"],"",__DIR__)."/index_".$_SESSION["SESS_LANG_UI"].".html";
echo "<h1>".$MESS["pagetitle"]."</h1>";
echo "<div class=\"\" style=\"padding:1em;\">";
$APPLICATION->IncludeFile($indexfile,Array(),Array("MODE"=>"html"));
echo "</div>";
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>