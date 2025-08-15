<?
if(!isset($mode)) {
$rightMenu=true;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

}
$APPLICATION->SetAdditionalCSS("/sveden/class/asmuinfo/asmuinfo.css");
$APPLICATION->SetTitle("Информация о профессионально-общественной аккредитации образовательной программы");

?>
<?if(!isset($mode)) {?>
<h1>Образование</h1>
<?}?>
<h2>Информация об общественной аккредитации образовательной организации</h2>
<table class="simpletable" style="max-width:800px;">
	<tr itemprop="eduOAccred">
<th itemprop="eduCode">Код, шифр </th>
<th itemprop="eduName"> Наименование профессии, специальности, направления подготовки, научной специальности </th>
<th itemprop="eduLevel"> Уровень образования </th>
<th itemprop="eduProf"> Образовательная программа, направленность, профиль, шифр и наименование научной специальности
</th>
<th itemprop="orgName"> Наименование аккредитующей организации</th>
<th itemprop="dateEnd">Срок действия профессиональнообщественной аккредитации (дата
окончания действия свидетельства о
профессионально-общественной
аккредитации)</th>
</tr>
	<tr >
		<td colspan="6" >отсутствует</td>
	</tr>
	</table>
<?
if(!isset($mode)){
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>