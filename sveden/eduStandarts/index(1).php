<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Образовательные стандарты и требования");
$APPLICATION->SetAdditionalCSS("/sveden/class/asmuinfo/asmuinfo.css");
echo "<h2>Образовательные стандарты и требования</h2>";
?>
<div itemprop="eduDoc">
<br><h3>Информация о применяемых федеральных государственных образовательных стандартах с размещением их копий </h3><br>
<div  itemprop="eduFedDoc">
<a href="#" class="hide">Посмотреть</a>
<br><b>Среднего профессионального образования: программы подготовки специалистов среднего звена по специальностям</b><br>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduFedSPDoc.html",Array(),Array("MODE"=>"html"));?><br>




<br><b>Высшего образования: бакалавриат по направлению подготовки </b><br>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduStandartDocBach.html",Array(),Array("MODE"=>"html"));?><br>

<br><b>Высшего образования: подготовка кадров по направлениям подготовки (специальностям) </b><br>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduFedDoc.html",Array(),Array("MODE"=>"html"));?><br>


<br><b>Высшего образования: подготовка кадров высшей квалификации (магистратура)</b><br>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduStandartDocMag.html",Array(),Array("MODE"=>"html"));?><br>
<br><b>Высшего образования: подготовка кадров высшей квалификации (аспирантура)</b><br>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduStandartDocAsp.html",Array(),Array("MODE"=>"html"));?><br>
<br><b>Высшего образования: подготовка кадров высшей квалификации (ординатура)</b><br>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduStandartDocOrd.html",Array(),Array("MODE"=>"html"));?><br>
</div>
<br><h3>Информация об утвержденных образовательных стандартах, с размещением их в форме электронного документа, подписанного электронной подписью</h3>
<div itemprop="eduStandartDoc">
<a href="#" class="hide">Посмотреть</a>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduStandartDoc.html",Array(),Array("MODE"=>"html"));?><br>
</div><br>
<br><h3>Информация о применяемых федеральных государственных требованиях с размещением их копий</h3>
<div itemprop="eduFedTreb">
<a href="#" class="hide">Посмотреть</a>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduFedTreb.html",Array(),Array("MODE"=>"html"));?><br>
</div><br>
<br><h3>
Информация о самостоятельно устанавливаемых
требованиях, с размещением их в форме электронного
документа, подписанного электронной подписью
</h3>
<div itemprop="eduStandartTreb">
<a href="#" class="hide">Посмотреть</a>
<?$APPLICATION->IncludeFile("/sveden/eduStandarts/eduStandartTreb.html",Array(),Array("MODE"=>"html"));?><br>
</div>
</div>

<?/*

$mode=true;
if(!isset($mode)) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Образовательные стандарты и требования");
	echo "<div class=\"container\">";
}
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$params=array(
	"mainPropList"=>array(
		"filInfo",
		"repInfo",
		"uchredLaw",
	),
	"propList"=>array(
		"eduFedDoc",
		"eduStandartDoc",
		"eduFedTreb",
		"eduStandartTreb",
	),
	"sectionsList"=>array(),);//"scoupeList"=>array("main")
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params),
	array("classname"=>"addressPlace","params"=>array(""))
));
$z->getHtml(false,true);
if(!isset($mode)){
	echo "</div>";
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
*/
?>

<?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>