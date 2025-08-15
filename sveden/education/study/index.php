<?
if(!isset($mode)) {
$rightMenu=true;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Информация о численности обучающихся по реализуемым образовательным программам по источникам финансирования");
	echo "<div class=\"container\"><h1>Образование</h1>";
}
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");

$z=new asmuinfo();
/*
echo "<h2>Информация о численности обучающихся по
реализуемым образовательным программам за счет
бюджетных ассигнований федерального бюджета,
бюджетов субъектов Российской Федерации, местных
бюджетов и по договорам об образовании за счет средств
физических и (или) юридических лиц, в том числе
информация о численности обучающихся, являющихся
иностранными гражданами, по каждой образовательной
программе и каждой профессии, специальности, в том
числе научной, направлению подготовки или
укрупненной группе профессий, специальностей и
направлений подготовки (для профессиональных
образовательных программ)</h2><br>";*/
$params=array(
	"mainPropList"=>array(),
	"propList"=>array("eduChislenEl"),
	"sectionsList"=>array(),
	"mainsections"=>array(397),//1175
	"scoupeList"=>array(),
	"hideCaption"=>1,
	);

echo "<br>";
ryhrth
$z->setСlassList(array(array("classname"=>"educhislen","params"=>array("russ"=>0))));
$z->setAdminGroups(array(1,8));
echo $z->getHtml(true);

echo "<br>";echo "<br>";
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params),
));
$z->setAdminGroups(array(1,8));
echo $z->getHtml(true);

if(!isset($mode)){
	echo "</div>";
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>