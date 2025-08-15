<?
$rightMenu=true;
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Руководство");
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
echo"<h1>Руководство</h1>";
$oppid=intval($_GET["opp"]);
$levelid=intval($_GET["level"]);
$z=new asmuinfo();
$params=array(
"eduLevel"=>$levelid,
"managers"=>1
);
$z->setAdminGroups(array(1,8));
$z->setСlassList(array(array("classname"=>"teachingStaff","params"=>$params),));
$z->getHtml(false);
?>

<br><br><h2>Наличие на сайте информации о руководителях филиалов (при наличии филиалов):</h2>
	<table class="simpletable" itemprop="rucovodstvoFil">
		<tbody style="font-size: 11px;">
		<tr><th style="text-align:left">Наименование филиала</th><td itemprop="nameFil">отсутствует</td></tr>
		<tr><th  style="text-align:left">ФИО руководителя</th><td itemprop="fio">отсутствует</td></tr>
		<tr><th  style="text-align:left">Должность</th><td  itemprop="post">отсутствует</td></tr>
		<tr><th  style="text-align:left">Контактные телефоны</th><td  itemprop="telephone">отсутствует</td></tr>
		<tr><th  style="text-align:left">Адреса электронной почты</th><td  itemprop="email">отсутствует</td></tr>
		</tbody>
	</table>
<br><br>
<h2>Наличие на сайте информации о руководителях представительств образовательной организации (при наличии представительств):</h2>
	<table class="simpletable" itemprop="rucovodstvoRep">
		<tbody style="font-size: 11px;">
		<tr><th  style="text-align:left">Наименование представительства</th><td   itemprop="nameRep">отсутствует</td></tr>
		<tr><th  style="text-align:left">ФИО руководителя</th><td   itemprop="fio">отсутствует</td></tr>
		<tr><th  style="text-align:left">Должность</th><td   itemprop="post">отсутствует</td></tr>
		<tr><th  style="text-align:left">Контактные телефоны</th><td   itemprop="telephone">отсутствует</td></tr>
		<tr><th  style="text-align:left">Адреса электронной почты</th><td   itemprop="email">отсутствует</td></tr>
		</tbody>
	</table>
	<br>
<?
if(!isset($nofooter) || !$nofooter)
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>