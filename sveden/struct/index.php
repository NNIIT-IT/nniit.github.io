<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Структура и органы управления образовательной организацией");
if (is_set($_GET["path"])) {
	$path=htmlspecialcharsBX($_GET["path"]);
} else $path="";
?><?
$APPLICATION->IncludeFile($APPLICATION->GetCurDir().'index_inc.html',[], ['MODE' => 'html']);
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"struct","params"=>array())));
$z->getHtml(false,true);
//филиалы
echo "<br><br>";
$params1=array(
	"mainPropList"=>array(
		"filInfo",

	),
	/*"propList"=>array(
"nameFil","addressFil","workTimeFil","telephoneFil","emailFil","websiteFil","fiofil","postfil","divisionClauseDocLink"),*/
	"sectionsList"=>array(443),);//"scoupeList"=>array("main")
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params1)));

$z->getHtml(false,true);
$params1=array(
	"mainPropList"=>array(
		"repInfo",
	),
	/*"propList"=>array(
"nameRep","addressRep","workTimeRep","telephoneRep","emailRep","websiteRep","fioRep","postRep","divisionClauseDocLink",),*/
	"sectionsList"=>array(442),);//"scoupeList"=>array("main")
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params1)));

$z->getHtml(false,true);

?>
<div class="hide" itemprop="rucovodstvoFil">
		<h2>Информация о руководителях филиалов:</h2>
		<span itemprop="nameFil">филиалы отсутствуют</span>
		<span class="hide" itemprop="fio">филиалы отсутствуют</span>
		<span class="hide" itemprop="post">филиалы отсутствуют</span>
		<span class="hide" itemprop="telephone">филиалы отсутствуют</span>
		<span class="hide" itemprop="email">филиалы отсутствуют</span>
	</div>
	<div class="hide" itemprop="rucovodstvoRep">
		<h2>Информация о руководителях представительств образовательной организации:</h2>
		<span itemprop="nameRep">представительства отсутствуют</span>
		<span class="hide" itemprop="fio">представительства отсутствуют</span>
		<span class="hide" itemprop="post">представительства отсутствуют</span>
		<span class="hide" itemprop="telephone">представительства отсутствуют</span>
		<span class="hide" itemprop="email">представительства отсутствуют</span>
	</div>
<br><?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>