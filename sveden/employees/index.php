<?
$rightMenu=true;
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Педагогический состав");
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
?>
<!-- h1>Педагогический состав</h1 -->
<?
$oppid=intval($_GET["opp"]);
$levelid=intval($_GET["level"]);
$z=new asmuinfo();
$params=array(
"eduLevel"=>$levelid,
"managers"=>0
);
$z->setAdminGroups(array(1,8));
$z->setСlassList(array(array("classname"=>"teachingStaff","params"=>$params),));
$z->getHtml(false);
?>
<div style="max-width:80%">
<?$APPLICATION->IncludeFile("/sveden/employees/employeesOpp.html",Array(),Array("MODE"=>"html"));?>
</div>


<div class="hide">
<?
$params=array("eduLevel"=>$levelid,"managers"=>1);
$z->setAdminGroups(array(1,8));
$z->setСlassList(array(array("classname"=>"teachingStaff","params"=>$params),));
$z->getHtml(false);
?>


	<table class="simpletable" itemprop="rucovodstvoFil">
		<tbody style="font-size: 11px; line-height: 0em;">
		<tr><th style="text-align:left">Наименование филиала</th><td itemprop="nameFil">отсутствует</td></tr>
		<tr><th  style="text-align:left">ФИО</th><td itemprop="fio">отсутствует</td></tr>
		<tr><th  style="text-align:left">Должность</th><td  itemprop="post">отсутствует</td></tr>
		<tr><th  style="text-align:left">Контактные телефоны</th><td  itemprop="telephone">отсутствует</td></tr>
		<tr><th  style="text-align:left">Адреса электронной почты</th><td  itemprop="email">отсутствует</td></tr>
		</tbody>
	</table>
	<table class="simpletable" itemprop="rucovodstvoRep">
		<tbody style="font-size: 11px; line-height: 0em;">
		<tr><th  style="text-align:left">Наименование представительства</th><td   itemprop="nameRep">отсутствует</td></tr>
		<tr><th  style="text-align:left">ФИО</th><td   itemprop="fio">отсутствует</td></tr>
		<tr><th  style="text-align:left">Должность</th><td   itemprop="post">отсутствует</td></tr>
		<tr><th  style="text-align:left">Контактные телефоны</th><td   itemprop="telephone">отсутствует</td></tr>
		<tr><th  style="text-align:left">Адреса электронной почты</th><td   itemprop="email">отсутствует</td></tr>
		</tbody>
	</table>
</div>
<?
if(!isset($nofooter) || !$nofooter)
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

