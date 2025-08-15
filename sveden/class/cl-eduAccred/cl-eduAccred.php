<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class eduAccred extends  iasmuinfo{
	const cashTime=6000;
	const arBlocks=array(6,12,17);
	private $cashGUID;
	private $edulevelId=0;
	private $specGroup=0;
	private $listitems=array();
	public function setparams($params){
		
		$this->edulevelId=0;
		if(isset($params["eduLevel"])){
			$this->edulevelId=intval($params["eduLevel"]);
		}
		if(isset($params["specGroup"])) $this->specGroup=intval($params["specGroup"]);
		$this->cashGUID="eduAccred_".$this->edulevelId;
	}
	private function generateItems(){


$sql=<<<sql
SELECT el.Id as oppId, 
el.name as eduName,
bs.NAME as eduLevel,
bs.id as eduLevelId,
bie.#EDUCODE# as eduCode, 
if(bie.#ADEDU# >0,1,0) as adEdu,
if(bie.#BEEDU# >0,1,0) as beEdu,
bie.#SROKOBUCH# as beForm,
bie.#SROKOPP# as dateEnd,
if(bie.#HIDE# is NULL,0,1) as hide,
if((bie.#PROFILE# is NULL or bie.#PROFILE#=""),"отсутствует",bie.#PROFILE#) as eduProfile,
if(bie.#LANGUAGE# is null,"Русский",bie.#LANGUAGE#) as language,
case 
when bie.#FGOS#=25863 then "ФГОС ВО 3+"
when bie.#FGOS#=25864 then "ФГОС ВО 3++"
else ""
end as beFgos,
if(bie.#NUMBERBFVACANT# is null,0, round(bie.#NUMBERBFVACANT#,0)) +
if(bie.#NUMBERBRVACANT# is null,0, round(bie.#NUMBERBRVACANT#,0)) +
if(bie.#NUMBERBMVACANT# is null,0, round(bie.#NUMBERBMVACANT#,0)) +
if(bie.#NUMBERPVACANT# is null,0, round(bie.#NUMBERPVACANT#,0)) +
if(bie.#NUMBERVACANTINV# is null,0, round(bie.#NUMBERVACANTINV#,0)) +
if(bie.#NUMBERINVACANT# is null,0, round(bie.#NUMBERINVACANT#,0)) +
if(bie.#NUMBERBFVACANTC# is null,0, round(bie.#NUMBERBFVACANTC#,0)) as och,
if(bie.#NUMBERBFVACANTZ# is null,0, round(bie.#NUMBERBFVACANTZ#,0)) +
if(bie.#NUMBERPVACANTZ# is null,0, round(bie.#NUMBERPVACANTZ#,0)) +
if(bie.#NUMBERVACANTINVZ# is null,0, round(bie.#NUMBERVACANTINVZ#,0)) +
if(bie.#NUMBERBFVACANTCZ# is null,0, round(bie.#NUMBERBFVACANTCZ#,0)) as zch,
bie.#EDUEL# as  eduElRec,
group_concat(if(doc.PROPERTY_135=47,  concat("/upload/",f.SUBDIR,"/",f.FILE_NAME,"::",if(f.DESCRIPTION is null or f.DESCRIPTION="",f.ORIGINAL_NAME,f.DESCRIPTION),"##")  ,'') SEPARATOR '') as tPred, 
group_concat(if(doc.PROPERTY_135=53,  concat("/upload/",f.SUBDIR,"/",f.FILE_NAME,"::",if(f.DESCRIPTION is null or f.DESCRIPTION="",f.ORIGINAL_NAME,f.DESCRIPTION),"##") ,'') SEPARATOR '') as tPrac, 

group_concat(e17e.PREVIEW_TEXT SEPARATOR ";") as lPred, 
group_concat(e17e.DETAIL_TEXT SEPARATOR ";") as lPrac 
FROM `b_iblock_element` el
left join `b_iblock_element_prop_s#iblock0#` bie on bie.IBLOCK_ELEMENT_ID=el.id
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=el.id
left join b_iblock_section bs on bs.id=se.IBLOCK_SECTION_ID 
left join b_iblock_element_prop_s12 doc on doc.PROPERTY_134=el.id and (doc.PROPERTY_135=47 or doc.PROPERTY_135=53)
left join b_iblock_element_prop_m12 docf on docf.IBLOCK_ELEMENT_ID=doc.IBLOCK_ELEMENT_ID and docf.IBLOCK_PROPERTY_ID=#FILES_ID#
left join `b_iblock_element_prop_s17` e17 on e17.PROPERTY_185=el.id
left join `b_iblock_element` e17e on e17.IBLOCK_ELEMENT_ID=e17e.id
left join b_file f on f.ID=docf.VALUE

WHERE 
el.IBLOCK_ID=#iblock0# and 
((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y") 
 #where#
group by el.id
order by bs.SORT,bie.#EDUCODE#
sql;
$uLang=strtoupper($_SESSION["SESS_LANG_UI"]);
$sql=str_replace("#FILES_ID#","#FILES_".$uLang."_ID#",$sql);
$sql=str_replace("#SCREENNAME_ID#","#SCREENNAME_".$uLang."_ID#",$sql);

//$this->BD->query("SET GLOBAL group_concat_max_len = 1000000";);


//and bie.PROPERTY_577 is NULL 
$where="";
if($this->edulevelId>0) $where=" and  bs.id=".$this->edulevelId;
if($this->specGroup!=1){
	$where.=" and bie.#HIDE# is NULL ";
}
$sql=str_replace("#where#",$where,$sql);
$sql=$this->sqltoiblock($sql,$this::arBlocks);

$this->BD->query("SET SESSION group_concat_max_len = 1000000;");

	$this->listitems=array();
				 //echo $sql;
	if($rez=$this->BD->query($sql)){
		while ($rec=$rez->fetch()){
			
			$tmprow=array(

				"oppId"=>$rec["oppId"],
				"eduCode"=>trim($rec["eduCode"]),
				"eduName"=>trim($rec["eduName"]),
				"eduLevel"=>$rec["eduLevel"],
				"eduLevelId"=>$rec["eduLevelId"],
				"language"=>$rec["language"],
				"dateEnd"=>(trim($rec["dateEnd"])!="")?$rec["dateEnd"]:"отсутствует",
				"beEdu"=>$rec["beEdu"],
				"vacantA"=>$rec["och"],
				"vacantB"=>$rec["zch"],
				"eduPrac"=>"отсутствует",
				"eduPred"=>"отсутствует",
				"eduProf"=>"отсутствует",
				"beFgos"=>$rec["beFgos"],
			);
			
			

			$sListPredFiles=trim($rec["tPred"]);
			if($sListPredFiles!=""){
				$arListPredFiles=explode("##",$sListPredFiles);
				//print_r($arListPredFiles);
				if (count($arListPredFiles)>0){
					$tmprow["eduPred"]="";$artPred2=array();
					foreach($arListPredFiles as $ListPredFiles){
						$arListPred=explode("::",$ListPredFiles);
						if($arListPred[1]!=""){
							$artPred2[$arListPred[0]]=$arListPred[1];
						}
					}
					//print_r($artPred2);
					if(count($artPred2)>0){
						$tmprow["eduPred"]="";
						foreach($artPred2 as $predFilePath=>$predName){
							$prName=str_replace(".pdf","",$predName);
						 	$tmprow["eduPred"].="<a class=\"linkicon link\" itemprop=\"eduPred\" href=\"{$predFilePath}\">{$prName}</a><br>";
						}
					}
				}
			}
			if($tmprow["eduPred"]=="отсутствует" || (strlen($sListPredNames)>10)){
				$tmprow["eduPred"]="";
				$sListPredNames=trim($rec["lPred"]);
				$sListPredNames=str_replace(array("\r\n","<br>"),array(";",";"),$sListPredNames);
				$artPred=explode(";",$sListPredNames);
				$artPred2=array();
				if(count($artPred)>0)$tmprow["eduPred"]="";
				foreach($artPred as $artPredi){
					$artPredi=trim($artPredi);
					if($artPredi!=""){if(!in_array($artPredi,$artPred2)){$artPred2[$artPredi]=$artPredi;}}
				}
				if(count($artPred2)>0){
					$tmprow["eduPred"]="";
					foreach($artPred2 as $predName){
						$tmprow["eduPred"].="<a  class=\"link\" itemprop=\"eduPred\" href=\"#\">{$predName}</a><br>";
					}
				}

			}

			$sListPracFiles=trim($rec["tPrac"]);
			if($sListPracFiles!=""){
				$arListPracFiles=explode("##",$sListPracFiles);

				if (count($arListPracFiles)>0){
					$tmprow["eduPrac"]="";
					$artPrac2=array();
					foreach($arListPracFiles as $ListPracFiles){
						$arListPrac=explode("::",$ListPracFiles);
						if($arListPrac[1]!=""){
							$artPrac2[$arListPrac[0]]=$arListPrac[1];
						}
					}
					if(count($artPrac2)>0){
						$tmprow["eduPrac"]="";
						foreach($artPrac2 as $pracFilePath=>$pracName){
						 	$tmprow["eduPrac"].="<a class=\"linkicon link\" itemprop=\"eduPrac\" href=\"{$pracFilePath}\">{$pracName}</a><br>";
						}
					}
				}
			}
			if($tmprow["eduPrac"]=="отсутствует"){
				$sListPracNames=trim($rec["lPred"]);
				$sListPracNames=str_replace(array("\r\n","<br>"),array(";",";"),$sListPracNames);
				$artPrac=explode(";",$sListPracNames);
				$artPrac2=array();
				if(count($artPrac)>0)$tmprow["eduPrac"]="";
				foreach($artPrac as $artPraci){
					$artPraci=trim($artPraci);
					if($artPraci!=""){if(!in_array($artPraci,$artPrac2)){$artPrac2[$artPraci]=$artPraci;}}
				}
				if(count($artPrac2)>0){
					$tmprow["eduPrac"]="";
					foreach($artPrac2 as $pracName){
						$tmprow["eduPrac"].="<a  class=\"link\" itemprop=\"eduPrac\" href=\"#\">{$pracName}</a><br>";
					}
				}

			}












			if(trim($rec["eduProfile"])!="") $tmprow["eduProf"]=trim($rec["eduProfile"]);
			

			$tmprow["eduEl"]="";
			$eduElAr=unserialize($rec["eduElRec"]);
			if(is_array($eduElAr["VALUE"]) && in_array(10879,$eduElAr["VALUE"])) $tmprow["eduEl"].=" электронное обучение"; 
			if($tmprow["eduEl"]!="") $tmprow["eduEl"].=",<br>"; 
			if(is_array($eduElAr["VALUE"]) && in_array(10880,$eduElAr["VALUE"])) $tmprow["eduEl"].=" дистанционные технологии"; 
			if($tmprow["beEdu"]) $tmprow["languageEl"]=" русский, английский"; else  $tmprow["languageEl"]=" русский";
			if($tmprow["eduEl"]=="" || in_array(10881,$eduElAr["VALUE"])) {
				$tmprow["eduEl"]=" не предусмотренно образовательной программой"; 
				$tmprow["languageEl"]=" не предусмотренно образовательной программой"; 
			}
			$arforms=$this->unserform($rec["beForm"]);

			if (strlen($arforms[1])>4){

				//	echo "<pre>";
				//	print_r($tmprow);	
				//echo "</pre>";

				$tmprow["learningTerm"]=$arforms["s1"];
				$tmprow["eduForm"]="Очная";
				$tmprow["vacantX"]=$rec["och"];
				$index=md5($tmprow["eduCode"]."_".$rec["eduProfile"]."_1");
				if(isset($this->listitems[$rec["eduLevel"]][$index])){
					$language=$this->listitems[$rec["eduLevel"]][$index]["language"];
					$arlanguage1=explode(",",str_replace(" ","",$language));
					$arlanguage2=explode(",",str_replace(" ","",$rec["language"]));
					$arlanguage=array_unique(array_merge($arlanguage1,$arlanguage2));
					$tmprow["language"]=implode(", ",$arlanguage);
					
				}
				
				$this->listitems[$rec["eduLevelId"]][$index]=$tmprow;
				//echo "<!-- ROW "; print_r($arforms);echo "-->";
			}
			if (strlen($arforms[2])>4){
				$tmprow["learningTerm"]=$arforms["s2"];
				$tmprow["eduForm"]="Заочная";
				$tmprow["vacantX"]=$rec["zch"];
				$index=md5($tmprow["eduCode"]."_".$rec["eduProfile"]."_2");
				if(isset($this->listitems[$rec["eduLevel"]][$index])){
					$language=$this->listitems[$rec["eduLevel"]][$index]["language"];
					$arlanguage1=explode(",",str_replace(" ","",$language));
					$arlanguage2=explode(",",str_replace(" ","",$rec["language"]));
					$arlanguage=array_unique(array_merge($arlanguage1,$arlanguage2));
					$tmprow["language"]=implode(", ",$arlanguage);
					
				}

				$this->listitems[$rec["eduLevelId"]][$index]=$tmprow;
			}
			if (strlen($arforms[3])>4){
				$index=md5($tmprow["eduCode"]."_".$rec["eduProfile"]."_3");
				$tmprow["eduForm"]="Очно-заочная";
				$tmprow["vacantX"]=0;
				if(isset($this->listitems[$rec["eduLevel"]][$index])){
					$language=$this->listitems[$rec["eduLevel"]][$index]["language"];
					$arlanguage1=explode(",",str_replace(" ","",$language));
					$arlanguage2=explode(",",str_replace(" ","",$rec["language"]));
					$arlanguage=array_unique(array_merge($arlanguage1,$arlanguage2));
					$tmprow["language"]=implode(", ",$arlanguage);
					
				}
				$this->listitems[$rec["eduLevelId"]][$index]=$tmprow;
			}

		}
		//asort($this->listitems);
	}

} //generateItems
	public function showHtml($buffer=false){

		//$memcache = new Memcache;
		//$memcache->addServer('unix:///tmp/memcached.sock', 0);
		//$casheData=$memcache->get($this->cashGUID);
		//if(strlen($casheData)>255) $html=$casheData;
$arLeves=array(
186=>"Среднее профессиональное образование",
179=>"Высшее образование - бакалавриат",
180=>"Высшее образование - специалитет",
184=>"Высшее образование - магистратура",
182=>"Высшее образование - аспирантура",
181=>"Высшее образование - ординатура",
424=>"Дополнительное профессиональное образование",
);
		$itemprops=array(
			"eduCode"=>"Код профессии, специальности, направления подготовки,
научной специальности, шифр области науки, группы
научных специальностей, научной специальности (для
образовательных программ высшего образования по
программам подготовки научных и научнопедагогических кадров в аспирантуре (адъюнктуре)",
			"eduName"=>"Наименование профессии, специальности, направления
подготовки, научной специальности, наименование
образовательной программы (для общеобразовательных
программ), наименование группы научных
специальностей",
			"eduProf"=>"Образовательная программа, направленность, профиль, шифр и наименование научной специальности",
			"eduLevel"=>"Уровень образо&shy;вания",
			"eduForm"=>"Форма обучения",
			"learningTerm"=>"Норма&shy;тивный срок обу&shy;чения",
			"dateEnd"=>"Срок действия государственной аккредитации образовательной программы (при наличии государственной аккредитации)",
			"language"=>"Языки, на которых осу&shy;ществля&shy;ется обра&shy;зо&shy;вание (обучение)",
			"eduPred"=>"Учебные предметы, курсы, дисци&shy;плины (модули), предусмот&shy;ренные соответ&shy;ствующей образо&shy;ватель&shy;ной програм&shy;мой",
			"eduPrac"=>"Практики, предусмот&shy;ренные соответ&shy;ству&shy;ющей обра&shy;зова&shy;тельной програм&shy;мой",
			"eduEl"=>"Информация об использовании при реализации образовательных программ электронного обучения и дистанционных образовательных технологий",
			//"languageEl"=>"Информация о языках, на которых осуществляется образование (обучение), размещенная в форме электронного документа, подписанного электронной подписью",
		);
$html="";
		if ($html=="" || ($casheData==false) || ($this->isEditmode()))
		{

			$this->generateItems();
		echo "<!--GUID";
			print_r($this->listitems);
		echo "-->";


			$html.="\r\n<table  class=\"eduAccredTable\" >\r\n<thead>";
			$htmlh="<tr>";
			//создаем шапку
			foreach ($itemprops as $itemprop=>$caption){
				$htmlh.="<th>{$caption}</th>";
			}
			$htmlh.="</tr>";
			$html.=$htmlh."\r\n</thead>\r\n<tbody>\r\n";
	
			$firstline=1;

			foreach($this->listitems as $levelId=>$items){
				$levelName=$arLeves[$levelId];
				if($this->edulevelId==0) $html.="<tr ><th colspan=\"11\" class=\"oppheader\">{$levelName}</th></tr>\r\n";
				//if($firstline==1){$firstline=0;}else{$html.=$htmlh;}
				//ksort($levels["items"]);

				foreach($items as $itemrow){
				if($itemrow["vacantX"]==0){

					$html.="<tr itemprop=\"eduAccred\" class=\"gray\" title=\"Прием на обучение (на 1 курс) не осуществляется\">\r\n";
				} else 	$html.="<tr itemprop=\"eduAccred\">\r\n";

					foreach ($itemprops as $itemprop=>$tmp){
						$valueProp=($itemrow[$itemprop]!="")?$itemrow[$itemprop]:$this::emptyCell;
						if($itemprop=="eduPred" || $itemprop=="eduPrac"){
							//делаем всплывающий элемент
							$idc=$itemprop.$itemrow["oppId"];
							$html.="<td>";
								$html.="<span onclick=\" showcell('$idc','Дисциплины список',0)\" title=\"Дисциплины...\" class=\"link\"> Посмотреть </span>\r\n";
								$html.="<div style=\"display:none\"><div id=\"$idc\" >{$valueProp}</div></div>\r\n";
							$html.="</td>\r\n";
						}elseif($itemprop=="eduLevel"){
							$html.="<td itemprop=\"{$itemprop}\">{$levelName}</td>\r\n";
						} else{	
							$beFgos=($itemrow["beFgos"]!="" && $itemprop=="eduProf")?" (".$itemrow["beFgos"].") ":"";
							$html.="<td itemprop=\"{$itemprop}\">{$valueProp}{$beFgos}</td>\r\n";
						}
					}
				$html.="</tr>\r\n";
				}

			}
			$html.="\r\n</tbody>\r\n</table>\r\n<!--".date("d.m.Y H:i:s")." -->";
			
			//$result = $memcache->replace( $this->cashGUID, $html);
			//if( $result == false )
			//{
			//  $memcache->set($this->cashGUID, $html, false, $this->cashTime);
			//} 
			
			//$memcache->close();
		}	
		if($buffer) return $html; else echo $html;
	}//showHtml

}//class
?>