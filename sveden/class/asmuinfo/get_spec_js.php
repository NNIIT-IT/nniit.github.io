<?
if(isset($opp) && isset($disc)){
	$txt =str_replace(array(' ','!',',',';',')','(','-','%','$','#','&','_'),"",$disc );
	$cc=mb_strtoupper($txt);
	$dmd5=md5($cc);
}else{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$dmd5=htmlspecialchars($_POST["DISC"]);
	$opp=intval($_POST["OPP"]);
}
$opplistId=array();
$sectionsName=array(
			179=>"Бакалавриат",
			180=>"Специалитет",
			181=>"Ординатура",
			182=>"Аспирантура",
			184=>"Магистратура",
			185=>"НПО",
			186=>"CПО"
		);

//slow method
if(CModule::IncludeModule("iblock")){ 

	if($dmd5!=""){//filter by discipline
		$IBLOCK_ID=61;//uchplan
		$arFilter=array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>"Y");
		$arSelect=array("PREVIEW_TEXT","IBLOCK_ID","ID","IBLOCK_SECTION_ID","PROPERTY_OPP");
		$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);

		while($ar_fields = $res->GetNext()){
		
			$listX=str_replace(array("\r","\n","<br>","<br />"),"|",$ar_fields["PREVIEW_TEXT"]);
			$listX=str_replace("||","|",$listX);
			$listX=str_replace("||","|",$listX);
			$TDISC=explode("|",$listX);

			foreach($TDISC as $xdis){
				$txt =str_replace(array(' ','!',',',';',')','(','-','%','$','#','&','_'),"",$xdis );
				$cc=mb_strtoupper($txt);
				$md5Disc=md5($cc);
				//echo $xdis." ".$md5Disc."==".$dmd5."<br>";
				if($md5Disc==$dmd5){
					$opplistId[]=$ar_fields["PROPERTY_OPP_VALUE"];
				}
			}
		}
	}
if(count($opplist2)==0){

	$IBLOCK_ID=59;//opp
	$opplist2=array();
	$arFilter=array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>"Y",);
	$arSelect=array("NAME","IBLOCK_ID","ID","IBLOCK_SECTION_ID","PROPERTY_EDUCODE","PROPERTY_PROFILE","PROPERTY_ADEDU");
	$res2=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
	while($ar_fields2 = $res2->GetNext()){
			$nme=$ar_fields2["PROPERTY_EDUCODE_VALUE"]." ".$ar_fields2["NAME"];
			if($ar_fields2["PROPERTY_PROFILE_VALUE"]!="") $nme.=" ".$ar_fields2["PROPERTY_PROFILE_VALUE"]." ";
			if($ar_fields2["PROPERTY_ADEDU_VALUE"]=="Y") $nme.=" (адаптированная ОП)";
			$opplist2[$ar_fields2["IBLOCK_SECTION_ID"]][$ar_fields2["ID"]]=str_replace("\"","'",$nme);
		
	}
	}
}
$opplisthtml="";
foreach ($opplist2 as $section=>$opps){
	$levelName=$sectionsName[$section];
	if (count($opps)>0) $opplisthtml.="<optgroup label=\"$levelName\">";	
	
	foreach ($opps as $oppID=>$oppName){
		$selopp="";
		if ($opp==$oppID){
			$selopp="selected";
		};
		$opplisthtml.="<option value=\"".$oppID."\" $selopp >$oppName</option>";
	}
	if (count($opps)>0) $opplisthtml.="</optgroup>";
}
$opplisthtmlH="<select class=\"oppselect\" name=\"opp_{$oppID}\"  value=\"{$oppID}\" onchange=\"oppchange(this);\">";
$opplisthtml=$opplisthtmlH.$opplisthtml."</select>";
echo $opplisthtml;

?>