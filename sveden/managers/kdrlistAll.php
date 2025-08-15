<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Документы");
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$oppid=intval($_GET["opp"]);
$levelid=intval($_GET["level"]);
?>
<div class="container"><br>
<?
$z=new asmuinfo();
$params=array(
"opp"=>$oppid,
"eduLevel"=>$levelid
);
$z->setAdminGroups(array(8));
$z->setСlassList(
	array(
		array("classname"=>"teachingStaff","params"=>$params),
	)
);
$z->getHtml(false);
echo "</div>";
?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>