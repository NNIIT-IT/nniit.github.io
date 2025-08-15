<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доступная среда");
if (is_set($_GET["path"])) {
	$path=htmlspecialcharsBX($_GET["path"]);
} else $path="";

?>
<h1>Доступная среда</h1>
<div itemprop="mtoOvz">
<?
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"purposeCab","params"=>array("objTypes"=>array("purposeCabOvz","purposePracOvz")))));
$z->getHtml(false,true);
$z->setСlassList(array(array("classname"=>"xobjects","params"=>array("ovz"=>1))));
$z->getHtml(false,true);
echo "</div>";
$expandPropetyItem="";
if(isset($_GET["itemprop"])){
	$expandPropetyItem=htmlspecialchars($_GET["itemprop"]);
}

$maindocsparams=array(
	"mainPropList"=>array(),
	"propList"=>array("purposeFacilOvz","ovz","comNetOvz","erListOvz","techOvz","hostelInterOvz","hostelNumOvz","interNumOvz"),
	"sectionsList"=>array(),
	"scoupeList"=>array(),
	"mainsections"=>array(394),
	"hideCaption"=>1,
	"expandPropetyItem"=>$expandPropetyItem
);

$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$maindocsparams),
	//array("classname"=>"addref","params"=>$paramsHref)
));
$z->getHtml(false,true);


?>
<p><!-- a class="goicon linkicon link  folder " href="/ob-universitete/inklyuzivnoe-obrazovanie/index.php">Инклюзивное образование</a></p -->
</div>
<?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
