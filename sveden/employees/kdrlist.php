<?$html="";
if (!isset($connection))$connection = Bitrix\Main\Application::getConnection();
	//ОПП block id 59
$s=<<<sql
SELECT 
ep.PROPERTY_376 as sform,  
if(ep.PROPERTY_352 is null,0,1) as biedu, 
ep.PROPERTY_377 as fgosId, 
BS.NAME as levelname, 
BE.ID as oppid, BE.NAME as 
nameopp, 
ep.PROPERTY_351 as oppcode, 
ep.PROPERTY_353 as prfname 
FROM `b_iblock_element` BE 
LEFT JOIN `b_iblock_element_prop_s59` ep ON BE.ID=ep.IBLOCK_ELEMENT_ID 
LEFT JOIN `b_iblock_section` BS ON BE.IBLOCK_SECTION_ID=BS.ID 
WHERE BE.IBLOCK_ID=59 and BE.IBLOCK_SECTION_ID in (181) and BE.ACTIVE="Y" and BS.ACTIVE="Y" and ep.PROPERTY_350 is NULL
and ep.PROPERTY_382 is NULL
ORDER BY BS.NAME desc,  ep.PROPERTY_352
sql;
	$aropps=array();
	$res = $connection->query($s);
	while ($row = $res->Fetch()){
		$prof=$row["prfname"];
		$profcode="";
		$profcode=trim(preg_replace("/[^0-9^\.]/i",'',$prof));
		$profname=trim(preg_replace("/^[0-9\.,\-,\–, ]+/i",'',$prof));
		$code=trim($row["oppcode"]);

		$arform=unserialize($row["sform"]);

		$fgosId=intval($row["fgosId"]);

		$sfgos="ФГОС ВО 3+";
		if($fgosId==216) $sfgos="ФГОС ВО 3++";
		if($row["levelname"]!="Специалитет") $sfgos="";
		
		$arAddName=array();
		$sAddName="";
		if($sfgos!="") $arAddName[]=$sfgos;

		if($row["biedu"]==1) $arAddName[]="Билингвальное обучение";

		if(count($arAddName)>0) $sAddName=" (".implode("; ",$arAddName).") ";

		if($arform["s1"]!="") //только очные формы!?
			$aropps[$row["levelname"]][$row["oppid"]]=array("oppid"=>$row["oppid"],"nameopp"=>$row["nameopp"].$sAddName,"speccode"=>$code,"profname"=>$profname,"profcode"=>$profcode);
	}

	foreach ($aropps as $level_name=>$aropp){
		$html.="<a href=\"javascript:void(0)\" class=\"hidedivlink linkicon\" >$level_name<br></a><ul>";$kx=1;
			foreach ($aropp as $opp){
				$html.="<li><div>";
				$html.="<a itemprop=\"optRef\" href=\"https://".$_SERVER['SERVER_NAME']."/sveden/employees/kdrlistAll.php?opp={$opp["oppid"]}\" >{$opp["speccode"]} {$opp["nameopp"]}";
				if ($opp["profname"]!="") $html.=" (".$opp["profname"].") ";//$opp["profcode"]
				$html.="</a></div></li>";
				$kx++;
			}
		$html.="</ul>";
	}
	echo $html;
?>