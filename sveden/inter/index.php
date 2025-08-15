<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Международное сотрудничество");
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
$APPLICATION->IncludeFile("/sveden/inter/header.html",Array(),Array("MODE"=>"html"));
echo "<h1>Международное сотрудничество</h1>";
echo "<h2>Информация о заключенных и планируемых к заключению договорах с иностранными и (или) международными организациями по вопросам образования и науки</h2>";
$z=new asmuinfo();
$z->setAdminGroups(array(8));

$z->setСlassList(array(array("classname"=>"internationalDog","params"=>array("open"=>1,"hideCaption"=>1))));
$html=$z->getHtml(true,true);
//array("classname"=>"internationalAccr","params"=>array("open"=>1))
?>
<div>
<?=$html?>
<h2>Информация о международной аккредитации</h2>
<table class="simpletable">
<thead>
<tr itemprop="internationalDog">
<td itemprop="stateName">Название государства</td>
<td itemprop="orgName">Наименование организации</td>
<td itemprop="dogReg">Реквизиты договора (наименование, дата, номер,срок действия)</td>
</tr>
	</thead>
<tbody>
<tr><td colspan="3">Отсутствует</td></tr>
	</tbody>
</table>

<?
//$APPLICATION->IncludeFile("/sveden/inter/institut.html",Array(),Array("MODE"=>"html"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>