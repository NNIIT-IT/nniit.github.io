<?
if (!isset($opp) && !isset($disc)){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$discmd5=htmlspecialchars($_POST["DISC"]);
	$opp=intval($_POST["OPP"]);
}elseif($disc!=""){
	$txt =str_replace(array(' ','!',',',';',')','(','-','%','$','#','&','_'),"",$disc );
	$cc=mb_strtoupper($txt);
	$discmd5=md5($cc);
};
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
$disclist3="";
if(CModule::IncludeModule("iblock")){ 
	
	$IBLOCK_ID=61;//uchplan
	$arDisc=array();
	$arFilter=array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>"Y");
	if($opp>0) $arFilter["PROPERTY_OPP_VALUE"]=$opp;
	$arSelect=array("NAME","IBLOCK_ID","ID","PREVIEW_TEXT","DETAIL_TEXT","PROPERTY_OPP");
	
	$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
	while($ar_fields = $res->GetNext()){
		if($ar_fields["PROPERTY_OPP_VALUE"]==$opp || $opp==0){
		//print_r($ar_fields);
		$listX=str_replace(array("\r","\n","<br>","<br />"),"|",$ar_fields["PREVIEW_TEXT"]).
			"|".str_replace(array("\r","\n","<br>","<br />"),"|",$ar_fields["DETAIL_TEXT"]);
		$listX=str_replace("||","|",$listX);
		$listX=str_replace("||","|",$listX);
		$TDISC=explode("|",$listX);
		
		foreach($TDISC as $xdis){
			
			$txt =str_replace(array(' ','!',',',';',')','(','-','%','$','#','&','_'),"",$xdis );
			$cc=mb_strtoupper($txt);
			$md5Disc=md5($cc);
			$arDisc[$md5Disc]=htmlspecialcharsBX($xdis);

		}
		}
	}

	$c=false;
	$kkk=0;if($disc="" || $discmd5==""){$c=true;}
	foreach ($arDisc as $id=>$dName){
		$check="";
		$selectdisc=($id==$discmd5);
		if ($selectdisc!="" || $c){
			$check=" selected ";
			$c=false;
		}
		$disclist3.="<option  $check value=\"".$id."\">$dName</option>";
	}
	if(count($arDisc)==0){
		$disclist3.="<option  selected value=\"0\" disable >Укажите другую Специальность</option>";
	}
}
$nhtm="<select onchange=\"discchange(this);\">";
$nhtm.=$disclist3."</select>";
echo $nhtm;
 
