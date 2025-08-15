<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$connection = Bitrix\Main\Application::getConnection();
	$sqlHelper = $connection->getSqlHelper();
	use SimpleExcel\SimpleExcel;
	require_once($_SERVER["DOCUMENT_ROOT"].'/sveden/class/SimpleExcel/SimpleExcel.php'); // load the main class file (if you're not using autoloader)

if($_REQUEST["xmlFile"] && $_REQUEST["opp"]){
	$file_name=$_POST["xmlFile"];
	$file_content=$_POST["content"];
	$file_type=$_POST["xmlFileType"];
	$dataseek=stripos($file_content,"base64,")+7;
	$arFileDescription=explode(";",substr($file_content,0,$dataseek-1));
	$fileDescriptionType=substr($arFileDescription[0],5);

	$fileContent=base64_decode(substr($file_content,$dataseek));
	echo $file_type." ".$fileDescriptionType;
	$fname=$_SERVER["DOCUMENT_ROOT"]."/sveden/class/SimpleExcel/tmpl.xml";
	file_put_contents($fname,$fileContent);
	$excel = new SimpleExcel('xml');                    // instantiate new object (will automatically construct the parser & writer type as XML)
	$excel->parser->loadFile($fname);            // load an XML file from server to be parsed
	$foo = $excel->parser->getField();                  // get complete array of the table
	/*convert */
	$foo1=array();
	foreach($foo as $rowKey=>$row){
			if(strlen($row[0])>6)
			foreach($row as $cellKey=>$cell){
				$foo1[$rowKey][$cellKey]=mb_convert_encoding(htmlspecialcharsBX($cell),"Windows-1251","auto")	;
			}
	}
	$opp=intval($_REQUEST["opp"]);
	/* запись в БД */

	$dwRecords=array();
	$cntRec=count($foo1);

	for($i=1;$i<=$cntRec;$i++){

		$rec=$foo1[$i];
		$dbrec=array();
		$dbrec["UF_NIR"]="";
		$dbrec["UF_PPS"]="1";
		$dbrec["UF_PHONE"]="Отсутствует";
		$dbrec["UF_EMAIL"]="Отсутствует";
		$dbrec["UF_CHILDEXPERIENCE"]="0";
		$dbrec["UF_MEDEXPERIENCE"]="0";
//		$dbrec["UF_TEACHINGOP"]="";

		$dbrec["UF_SPECEXPERIENCE"]=intval(mb_substr($rec[10],0,strpos($rec[10]," ")))*12;
		$dbrec["UF_GENEXPERIENCE"]=intval(mb_substr($rec[9],0,strpos($rec[9]," ")))*12;
		$dbrec["UF_PROFDEVELOPMENT"]=htmlspecialcharsBX($rec[8]);
		$dbrec["UF_ACADEMSTAT"]=htmlspecialcharsBX($rec[6]);
		$dbrec["UF_DEGREE"]=htmlspecialcharsBX($rec[5]);
		$dbrec["UF_EMPLOYEEQUALIFICA"]=htmlspecialcharsBX($rec[7]);
		$dbrec["UF_TEACHINGQUAL"]=htmlspecialcharsBX($rec[4]);
		$dbrec["UF_TEACHINGLEVEL"]=htmlspecialcharsBX($rec[3]);
		$sdisc=str_replace(array(";","\r\n"),"|",$rec[2]);
		$ardisc=explode("|",$sdisc);
		$arDisc=array();
		print_r($ardisc);
		$dname="";
		foreach($ardisc as $dname){
			$d=htmlspecialcharsBX(trim($dname));
			if($d!="")	$arDisc[]=array("opp"=>$opp,"disc"=>$d);
		}
		$dbrec["UF_TEACHINGDISCIPLIN_1"]=$arDisc;
		$dbrec["UF_TEACHINGDISCIPLIN_2"]=array();
		$dbrec["UF_POST"]=trim($rec[1]);
		$dbrec["UF_FIO"]=trim($rec[0]);

		$dbrec["NEW"]=1;
		$dwRecords[$dbrec["UF_FIO"]]=$dbrec;
	//	print_r($dbrec);

	}



	$fromDB=array();
	$rec=$connection->query("select ID, UF_FIO,UF_TEACHINGDISCIPLIN from prepod ");
	while($ob=$rec->fetch()){
		$fio=$ob["UF_FIO"];
		;
		if(isset($dwRecords[$fio])){

			if($ob["UF_TEACHINGDISCIPLIN"]!=""){


					$jsonValue=str_replace("&quot;",'"',$ob["UF_TEACHINGDISCIPLIN"]);
					$arjsonDisc=\Bitrix\Main\Web\Json::decode(mb_convert_encoding($jsonValue, 'UTF-8', 'windows-1251'));

					foreach($arjsonDisc as $discBD){
							if($discBD["opp"]!="" && $discBD["disc"]!=""){
								$dwRecords[$fio]["UF_TEACHINGDISCIPLIN_2"][]=$discBD;
							}
					}
					
					
					
					
					$disWList=$dwRecords[$fio]["UF_TEACHINGDISCIPLIN_1"];
					foreach($disWList as $disW){
						$existInBD=false;
						foreach($dwRecords[$fio]["UF_TEACHINGDISCIPLIN_2"] as $jsonDisc){
							$existInBD=$existInBD || ($jsonDisc["opp"]==$disW["opp"] && $jsonDisc["dics"]==$disW["dics"] && $jsonDisc["dics"]!="" && $jsonDisc["opp"]!="");
						}
						if(!$existInBD){
							$dwRecords[$fio]["UF_TEACHINGDISCIPLIN_2"][]=$disW;
						}else{
							
						}
					}
					
			}else{
				$dwRecords[$fio]["UF_TEACHINGDISCIPLIN_2"]=$dwRecords[$fio]["UF_TEACHINGDISCIPLIN_1"];
			}
			$dwRecords[$fio]["NEW"]=0;

		} 

	}



	foreach($dwRecords as $fio=>$dataRec){
		$newREC=array();
		//новая запись
		if($dataRec["NEW"]==1){

				$newREC=$dataRec;
				$newREC["UF_TEACHINGDISCIPLIN"]="";
				$arTmp=array();
			
				foreach($newREC["UF_TEACHINGDISCIPLIN_1"] as $sdrec){
					$arTmp[]="{&quot;opp&quot;:&quot;".$sdrec["opp"]."&quot;,&quot;disc&quot;:&quot;".$sdrec["disc"]."&quot;}";
				}

				$newREC["UF_TEACHINGDISCIPLIN"]="[".implode(",",$arTmp)."]";

				unset($newREC["UF_TEACHINGDISCIPLIN_1"]);
				unset($newREC["UF_TEACHINGDISCIPLIN_2"]);
				unset($newREC["NEW"]);
				if($newREC["UF_FIO"]!="") {
					//print_r($newREC);
					$newREC["UF_ACTIVE"]=1;
					echo $connection->add("prepod",$newREC).", ";
				}
		
		}else{
			echo "UPDATE";
			
			$newREC=$dataRec;			
			$arTmpn=array();
			foreach($dataRec["UF_TEACHINGDISCIPLIN_2"] as $ndrec){
				$arTmpn[]="{&quot;opp&quot;:&quot;".$ndrec["opp"]."&quot;,&quot;disc&quot;:&quot;".htmlspecialcharsBX($ndrec["disc"])."&quot;}";
			}
			$newREC["UF_TEACHINGDISCIPLIN"]="[".implode(",",$arTmpn)."]";
			unset($newREC["UF_TEACHINGDISCIPLIN_1"]);
			unset($newREC["UF_TEACHINGDISCIPLIN_2"]);
			unset($newREC["NEW"]);
			echo "<pre>";print_r($newREC);echo "</pre>";
			$sql="update `prepod` set ";
			foreach($newREC as $bdKey=>$bdVal){
					if($bdVal!="") $sql.=$bdKey."=\"{$bdVal}\",";
			}
			$sql.=" UF_ACTIVE=1 where UF_FIO=\"".$fio."\"";
			if($fio!="") {
				echo $sql;
				$connection->queryExecute($sql);
			}
			
		}
		
	}
	
	
}else{
	$opp=array();
	if(CModule::IncludeModule("iblock")){ 
	 $arFilter=array("IBLOCK_ID"=>59,"ACTIVE"=>"Y");
	 $arSelect=array("NAME","PROPERTY_PROFILE","PROPERTY_EDUCODE","ID","IBLOCK_SECTION_ID");
	 $res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
	 while($ar_fields = $res->GetNext())
	  {
		$opp[$ar_fields["IBLOCK_SECTION_ID"]][$ar_fields["ID"]]=$ar_fields["PROPERTY_EDUCODE_VALUE"]." ".$ar_fields["NAME"]." ".$ar_fields["PROPERTY_PROFILE_VALUE"];
	  }
	}

$sectionsName=array(
			179=>"Бакалавриат",
			180=>"Специалитет",
			181=>"Ординатура",
			182=>"Аспирантура",
			184=>"Магистратура",
			185=>"НПО",
			186=>"CПО"
		);
$html="<form>";
$html.="Образовательная программа:<select id=\"opp\" name=\"opp\">";
foreach($opp as $levelID=>$oppList){
	if(count($oppList)>0){
		$html.="<optgroup label=\"".$sectionsName[$levelID]."\">";
		foreach($oppList as $oppID=>$oppName){
			$html.="<option value=\"".$oppID."\">".$oppName."</option>";
		}
	}
}
$html.="</select><br><br>";
$html.="Файл: <input type=\"file\" name=\"xmlFile\" id=\"xmlFile\">";
$html.="<br><br><p id=\"status\"></p></form>";
echo $html;
?>

<?}?>				