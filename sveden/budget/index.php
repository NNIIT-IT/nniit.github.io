<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("Финансово-хозяйственная деятельность");
//echo "<h1>Финансово-хозяйственная деятельность</h1>";

require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
?>

<?
$params1=array("mainPropList"=>array("volume"),
	"propList"=>array(
		"finBFVolume",
		"finBRVolume",
		"finBMVolume",
		"finPVolume",
		),
	"hideCaption"=>1,
	"mainsection"=>array(415),
	);
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params1),
));
$z->setAdminGroups(array(8));
$z->getHtml();
?>

<h3>Информация о поступлении и расходовании финансовых и материальных средств</h3>
<div itemprop="volume">
<?$params2=array("mainPropList"=>array("volume"),
	"propList"=>array(
		"finYear",
		"finPost",
		"finRas",
		),
	"hideCaption"=>1,
	"sectionsList"=>array(415),
	);
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params2),
));
$z->setAdminGroups(array(8));
$z->getHtml();
?>
</div>
<h3>Утвержденный план финансово-хозяйственной
деятельности образовательной организации или
бюджетные сметы образовательной организации</h3>
<?
$params3=array(
	"propList"=>array(
		"finPlanDocLink"
		),
	"hideCaption"=>1,
	"sectionsList"=>array(415),);
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params3),
));
$z->setAdminGroups(array(8));
$z->getHtml();
?>

<!-- div class="divlevels">
<h3 class="">Информация размещаемая на сайте http://bus.gov.ru</h3>
<p>
<a href="https://bus.gov.ru/agency/#">https://bus.gov.ru/agency/#</a>
</p>
</div -->
<?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>