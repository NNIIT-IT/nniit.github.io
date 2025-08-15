<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
echo "<div class=\"container\">";
$z=new asmuinfo();
/*
$z->setparams(
	array(
		"mainPropList"=>array("fillnfo","volume","finRec"),
		"propList"=>array("finPlanDocLink"),
		"sectionsList"=>array(896)
	)
);
*/
//documents
//$params=array("mainPropList"=>array("paidEdu"),"propList"=>array("finPlanDocLink","paidEduDocLink","paidParents"),"sectionsList"=>array(),"scoupeList"=>array("document"),"mainsections"=>array(896,1337));
//main
//$params=array("mainPropList"=>array(""),"propList"=>array("ustavDocLink"),"sectionsList"=>array(),"scoupeList"=>array("main"));
$params=array("podrazd"=>2473);
$z->setРЎlassList(array(	array("classname"=>"maindocs","params"=>$params),));

$z->getHtml(true);
echo "</div>";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>