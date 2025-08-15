<?
$rightMenu=true;
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Документы");
echo "<h1 class=\"title\">Документы</h1>";
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
?><div class="container">
<?
$z=new asmuinfo();
$propList=array(
"ustavDocLink", 
"localActStud",
"localActOrder",
"localActCollec",

	//"accreditationDocLink",
	//"localAct",
);
$propList3=array(

"reportEduDocLink",
"prescriptionDocLink",
	//"accreditationDocLink",
	//"localAct",
);
$propList2=array(
"priemDocLink",
"modeDocLink",
"tekKontrolDocLink",
"perevodDocLink",
"vozDocLink",
	//"accreditationDocLink",
	//"localAct",
);

$params=array(
	"mainPropList"=>array(),
	"propList"=>$propList,//array("priemDocLink","grant","priemLocalAct"),
	"sectionsList"=>array(393,402,403,404,406,407,408,409,410,411),
	"mainsections"=>array(393),//1175
	"hideCaption"=>1
	);
$params2=array(
	"mainPropList"=>array(),
	"propList"=>$propList2,//array("priemDocLink","grant","priemLocalAct"),
	"sectionsList"=>array(393,402,403,404,406,407,408,409,410,411),
	"mainsections"=>array(393),//1175
	"hideCaption"=>1
	);
$params3=array(
	"mainPropList"=>array(),
	"propList"=>$propList3,//array("priemDocLink","grant","priemLocalAct"),
	"sectionsList"=>array(393,402,403,404,406,407,408,409,410,411),
	"mainsections"=>array(393),//1175
	"hideCaption"=>1
	);
$z->setAdminGroups(array(8));
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params),
));
$z->getHtml(false);
?>
<br>
<span class="texticon hidedivlink linkicon link"><?=$MESS["localActS1"]?></span>
<div style="padding: 1em; " class="">
<?$params=array(
	"mainPropList"=>array(),
	"propList"=>$propList,//array("priemDocLink","grant","priemLocalAct"),
	"sectionsList"=>array(393,402,403,404,406,407,408,409,410,411),
	"mainsections"=>array(393),//1175
	"hideCaption"=>1
	);
$z->setAdminGroups(array(8));
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params2),
));
$z->getHtml(false);

?>
</div><br>
<?
$z->setAdminGroups(array(8));
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params3),
));
$z->getHtml(false);
?>
</div>
<?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>