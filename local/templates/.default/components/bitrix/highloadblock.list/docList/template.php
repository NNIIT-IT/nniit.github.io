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

foreach($arResult["rows"] as $row){
	$sign="";
	$x=$row['UF_FILE'];
	$matches=array();
	preg_match_all('/<a href="(.*?)"/s',$x, $matches);
	$url=$matches[1][0];

	$x=$row['UF_SIGN'];
	$matches=array();
	preg_match_all('/<a href="(.*?)"/s',$x, $matches);
	$sign=$matches[1][0];

	$x=$row['UF_NAME'];
	if(($_SESSION["SESS_LANG_UI"]=="en") && trim($row['UF_NAME_EN'])!=""){
		$x=trim($row['UF_NAME_EN']);
	}
	$matches=array();
	preg_match_all('/>(.*?)</s',$x, $matches);
	$name=trim($matches[1][0]);
	if($name=="") $name=$row['UF_NAME'];
	//	echo "url=".$url."<br>";
	$docs[$row["ID"]]=array("name"=>htmlspecialcharsbx($name),'url'=>$url,"sign"=>$sign);
}
echo "<div><ul class=\"linkicons\" id=\"docs2\" data-block=\"2\">";
foreach($docs as $ID=>$row){?>
	<li title="<?=$row['name']?>" class="doc_row maindocselementHB" data-block="2" data-id="<?=$ID?>" id="doc2<?=$ID?>">
	<a  class= "linkicon " href="<?=$row['url']?>" >
		<?=$row['name']?>
	</a>
		<?if($row['sign']!=""){?>
			<sup><a href="<?=$row['sign']?>">sig</a></sup>
		<?}?>
	</li>
	<br>
<?}?>
</ul></div>
