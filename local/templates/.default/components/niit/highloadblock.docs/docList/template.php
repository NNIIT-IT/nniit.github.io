<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){	die();}
//$APPLICATION->AddHeadScript("/local/templates/.default/class/hbEdit.js");
//$this->addExternalJS("/local/templates/.default/class/hbEdit.js");
/** @global CMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */


if (!empty($arResult['ERROR']))
{
	echo $arResult['ERROR'];
	return false;
}
//echo "<pre>"; print_r($arResult);echo "</pre>";

$docs=array();
$prop=base64_encode(json_encode(['UF_URL'=>$APPLICATION->GetCurDir()]));

foreach($arResult["rows"] as $row){
	$sig="";
	$url="";
	if($row['UF_FILE']['VALUE']) $url=$row['UF_FILE']['VALUE']['SRC'];
	if($row['UF_SIGN']['VALUE']) $sig=$row['UF_SIGN']['VALUE']['SRC'];
	$name=$row['UF_NAME']['VALUE'];
	if(($_SESSION["SESS_LANG_UI"]=="en") && trim($row['UF_NAME_EN']['VALUE'])!=""){
		$name=trim($row['UF_NAME_EN']['VALUE']);
	}
	$docs[$row["ID"]]=array("name"=>htmlspecialcharsbx($name),'url'=>$url,"sig"=>$sig);
}
echo "<div><ul class=\"linkicons\" id=\"docs2\" data-block=\"2\">";
foreach($docs as $ID=>$row){

?>
<li title="<?=$row['name']?>" class="doc_row maindocselementHB" data-block="2" data-id="<?=$ID?>" id="doc2<?=$ID?>" data-prop="<?=$prop?>">
	<a  class= "linkicon " href="<?=$row['url']?>" >
		<?=$row['name']?>
	</a>
		<?if($row['sign']!=""){?>
			<sup><a href="<?=$row['sig']?>">sig</a></sup>
		<?}?>
	</li>
	<br>
<?}
if(count($docs)==0){?>
<li title="-" class="doc_row maindocselementHB" data-block="2" data-id="0" id="doc2_0" data-prop="<?=$prop?>">
	<a  class= "linkicon " href="<?=$row['url']?>" >
		&nbsp;&nbsp;
	</a>

	</li>
	<br>
<?}
?>
</ul></div>
