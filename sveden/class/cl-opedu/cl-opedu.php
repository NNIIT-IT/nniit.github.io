<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class opedu extends  iasmuinfo{
	const cashTime=6000;
	const adminGroup=153;
	const arBlocks=array(12,6,17);
	const emptyCell="Отсутствует";
	const enumID=418;//TYPEDOC
	private $cashGUID;
	private $ovz;
	private $listitems=array();
	private $hideTitle=0;
	private $arLeves=array();
	public function setparams($params){
		$this->ovz=0;
		if(isset($params["ovz"])) $this->ovz=intval($params["ovz"]);//0 нет 1 да 2 все
		$this->cashGUID="opedu_".$this->ovz;
		if(isset($params["hideTitle"])) $this->hideTitle=intval($params["hideTitle"]);//0 нет 1 да 2 все


	}
	private function getOppFileList($oppID,$xmlId,$inyaz=false,$oppsection=0,$eduCode=""){
		$filesOpp=array();
		global $connection, $editMode;
		$inyaz0=($inyaz)?1:0;
/*$itempropsEach=array(
			"eduCode"=>0,
			"eduName"=>0,
			"eduLevel"=>0,
			"eduForm"=>0,
			"opMain"=>1,
			"educationPlan"=>1,
			"educationAnnotation"=>1,
			"educationRpd"=>1,
			"educationShedule"=>1,
			"methodology"=>1,
			"eduPr"=>0,
			"eduEl"=>0
		);*/
$itemprop="";
$sql=<<<sql
SELECT distinct el.name as name, s.IBLOCK_ELEMENT_ID as elId, f.ID as fileid, em.VALUE,concat(f.SUBDIR,"/",f.FILE_NAME) as file,f.ORIGINAL_NAME,f.DESCRIPTION,em.XML_ID as xmlid,
"1" as sign, 
f.ID as fileid,
"1" as siginfo,
els.#SCREENNAME# as screenName,
if(els.#FORALLOPP#=1,1,0) as forallopp,
els.#OPPDOC# as oppid

FROM b_iblock_element_prop_s60 s
left join b_iblock_element_prop_m60 m on m.IBLOCK_ELEMENT_ID=s.IBLOCK_ELEMENT_ID and m.IBLOCK_PROPERTY_ID=419
left join b_iblock_element el on el.ID=s.IBLOCK_ELEMENT_ID
left join b_iblock_element_prop_s60 els on els.IBLOCK_ELEMENT_ID=s.IBLOCK_ELEMENT_ID
left join b_file f on f.ID=m.VALUE_NUM
left join b_iblock_property_enum em on em.ID=s.PROPERTY_418
#JOIN#
where (( ((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y")
sql;
		if($oppsection!=0){

			if($oppID!="")	{
				$JOIN=" left join b_iblock_element opp on opp.ID=els.PROPERTY_417 ";
				//$sql.=" and (els.PROPERTY_417='".$oppID."' or els.PROPERTY_418>0) and opp.IBLOCK_SECTION_ID=".$oppsection." ";
				$sql.=" and (els.PROPERTY_417='".$oppID."' or els.PROPERTY_576>0) and opp.IBLOCK_SECTION_ID=".$oppsection." ";
			}

		}else{
			$JOIN="";
			if($oppID!="")	$sql.=" and (els.PROPERTY_417='".$oppID."')  ";
		}
		
		if($xmlId!="")	$sql.=" and em.XML_ID='".$xmlId."'";
		$sql.=" order by el.name desc,f.DESCRIPTION, f.FILE_NAME";
		$sql=str_replace("#JOIN#",$JOIN,$sql);
		$sql=$this->sqltoiblock($sql,$this::arBlocks);
		echo "<!--".$sql."-->";	
		$shtml=	"";

		if($rez=$this->BD->query($sql)){
		$gmin=3000;
		$gmax=0;
		$filesOpp=array();
		$shtml="\r\n\t";
		$fileCount=array();
		$values=array();
		while ($rec=$rez->fetch()){
			if(($rec["file"]!="") && ($rec["oppid"]==$oppID)){
				if(!isset($fileCount[$rec["xmlId"]])) $fileCount[$rec["xmlId"]]=0;
				$fileCount[$rec["xmlId"]]++;
			}
			$values[]=$rec;
		}
		foreach ($values as $rec){
			if($rec["file"]!=""){	
					$zfile=array();
					if(($fileCount[$rec["xmlId"]]>0 && $rec["oppid"]==$oppID)||($fileCount[$rec["xmlId"]]==0)){
					$zfile["src"]="/upload/".$rec["file"];
					$zfile["id"]=$rec["fileid"];
					$zfile["name"]=($rec["DESCRIPTION"]!="")?$rec["DESCRIPTION"]:$rec["ORIGINAL_NAME"];
					$zfile["sign"]=intval($rec["sign"])==1;
					if($rec["siginfo"]==1 || $zfile["sign"]) $zfile["siginfo"]="<span class=\"epinfo\"></span>"; else $zfile["siginfo"]="";
					$zfile["fileid"]=$rec["fileid"];

					$filesOpp[$rec["elId"]]["name"]=($rec["screenName"]!="")?$rec["screenName"]:$rec["name"];
					$filesOpp[$rec["elId"]]["dtype"]=intval($rec["dtype"]);
					$filesOpp[$rec["elId"]]["files"][]=$zfile;
				}
			} else{
				$zfile["name"]=($rec["DESCRIPTION"]!="")?$rec["DESCRIPTION"]:$rec["ORIGINAL_NAME"];
				$filesOpp[$rec["elId"]]["name"]=($rec["screenName"]!="")?$rec["screenName"]:$rec["name"];;
				$filesOpp[$rec["elId"]]["dtype"]=intval($rec["dtype"]);
				$filesOpp[$rec["elId"]]["files"]=array();

			}
		}

			if ($xmlId=="annot"){$title0="Аннотации ";$itemprop="educationAnnotation";}
		elseif($xmlId=="rpd") {$title0="РПД ";$itemprop="educationRpd";}
		elseif($xmlId=="pract") {$title0="Практики ";$itemprop="eduPr";}
		elseif($xmlId=="ucheb_plan") {$title0="Учебный план ";$itemprop="educationPlan";}
		elseif($xmlId=="graf") {$title0="Календарный график ";$itemprop="educationShedule";}
		elseif($xmlId=="doc" || $xmlId=="" ){$title0="Документы ";$itemprop="methodology";}
		if($itemprop!="") $sitemprop=" itemprop=\"$itemprop\" ";
		if(count($filesOpp)>0){
			foreach ($filesOpp as $elId=>$element){
				$name=$element["name"];
				$idcontext=$oppID."_".$xmlId."_".$inyaz."_".$elId;
				$title=$title0." ".$name;
				$ereaId="e_".$idcontext;
				$addAction="";
				$addClass="";
				$dtype=$element["dtype"];
				$shtml.="\r\n\t\t<span title=\"{$title0}\" id=\"{$idcontext}\" class=\"maindocselement oppfiles\" data-xmlid=\"{$dtype}\"";
				$shtml.=" data-opp=\"{$oppID}\" data-id=\"{$elId}\" data-iblock=\"60\" onclick=\" showcell('{$ereaId}','{$title}',{$inyaz0})\" >{$name}</span>\r\n";
				$shtml.="\t\t<div class=\"hide\" id=\"$ereaId\">";
				//$sitemprop="";

				
				if(count($element["files"])>0){
					foreach ($element["files"] as $xfile){
						$furl=$this->fullescape($xfile["src"]);
						//if(file_exists($_SERVER["DOCUMENT_ROOT"].$furl)){
						$furl=$this->fullescape($xfile["src"]);
						$furlSrc=$furl;
						//if($furl!="") 
						if($itemprop=="educationRpd"){
							//$shtml.="\r\n\t\t\t<object data=\"{$furl}\" type=\"application/pdf\" width=\"90%\" height=\"450px\">";
							//$shtml.="<a target=\"blank\" $sitemprop  href=\"{$furl}\" data-fileid=\"{$xfile["id"]}\" class=\"linkicon linkiconpdf\">{$xfile["name"]}</a>{$xfile["siginfo"]}{$sign}";
							//$shtml.="<br></object>";
							$furl="/sveden/files/?id=".$xfile["fileid"];
						} 
							$shtml.="\r\n\t\t\t<a $sitemprop  href=\"{$furlSrc}\"  class=\"hide\"></a><a $sitemprop  href=\"{$furl}\" data-fileid=\"{$xfile["id"]}\" class=\"linkicon fileviewpdf linkiconpdf\">{$xfile["name"]}</a>{$xfile["siginfo"]}{$sign}<br>";
						}
					//}
				}else{

					$shtml.="\r\n\t\t\t<a class=\"linkicon emptycell\" $sitemprop href=\"#\">".$this::emptyCell."</a><br>\r\n";
				}
				$shtml.="\r\n\t\t</div>";
				$shtml.="<br>\r\n";

			}
		}  elseif($xmlId!=""){
			if($xrez=$this->BD->query("SELECT ID FROM `b_iblock_property_enum` WHERE `PROPERTY_ID` = ".$this::enumID." and XML_ID=\"".$xmlId."\" LIMIT 1")){
				if ($xrec=$xrez->fetch()){ $dtype=$xrec["ID"];}
			}
			$idcontext=$oppID."_".$xmlId."_".$inyaz."0";
			$shtml.="<a $sitemprop href=\"#\" id=\"{$idcontext}\" class=\"link oppfiles maindocselement #addClassHide#\" data-xmlid=\"{$dtype}\" data-opp=\"{$oppID}\" data-elname=\"{$title0} {$eduCode}\"  data-iblock=\"".$this::arBlocks[0]."\" data-id=\"0\">".$this::emptyCell."</a>";
		} else{
			$idcontext=$oppID."_".$xmlId."_".$inyaz."0";
			$shtml.="<a $sitemprop href=\"#\" id=\"{$idcontext}\" class=\"link oppfiles maindocselement #addClassHide#\" data-opp=\"{$oppID}\" data-elname=\"{$title0} {$eduCode}\"  data-iblock=\"".$this::arBlocks[0]."\" data-id=\"0\">".$this::emptyCell."</a>";
		}
		
		}//$rez=$this->BD->query($sql)
		return $shtml;
	} //getOppFileList
	private function getOpMain($filesID){
		$list=array();
		$html="";
		if(count($filesID)>0){
			$sql="SELECT concat(f.SUBDIR,\"/\",f.FILE_NAME) as file,f.ORIGINAL_NAME,f.DESCRIPTION, f.ID as fileId, ";
		//	$sql.=" if(ufc.id is not null or f.signature is not null,1,0) as siginfo  ";
			$sql.=" 1 as siginfo  ";
			$sql.=" FROM b_file f ";
		//	$sql.=" LEFT JOIN `u_file_cript` ufc on f.ID=ufc.UF_FILE_ID ";
			if(count($filesID)==1) 	
				$sql.="where f.ID = $filesID[0] ";
			else 	
				$sql.="where f.ID in(".implode(",",$filesID).")";
			$sql.=" order by f.DESCRIPTION desc, f.ORIGINAL_NAME desc";
			
			$rez=$this->BD->query($sql);
			while ($rec=$rez->fetch()){
				if ($rec["DESCRIPTION"]!="")  {$desc=$rec["DESCRIPTION"]; $title=$desc;}else {$desc="Ссылка";$title=mb_substr($rec["ORIGINAL_NAME"],0,-4);}
				if($desc=="") $desc=$this::emptyCell;
				if($rec["file"]!=""){
					$src=$this->fullescape("/upload/".$rec["file"]); 
					if(intval($rec["siginfo"])==1) $siginfo="<span class=\"epinfo\"></span>"; else $siginfo="";
					$html.="\r\n\t<a itemprop=\"opMain\" class=\"linkicon \" href=\"{$src}\" title=\"$title\" download target=\"blank\" data-fileid=\"{$rec["fileId"]}\">{$desc}</a>{$siginfo}<br>";
				}else{
					$html.="\r\n\t<a itemprop=\"opMain\" class=\"linkicon \" href=\"/sveden/empty-file.pdf\" title=\"$title\" download target=\"blank\" data-fileid=\"{$rec["fileId"]}\">{$desc}</a><br>";
					//$html.="<span  itemprop=\"opMain\" class=\"linkicon\" title=\"$title\">{$desc}</span><br>";
				}
			}
			
		};
		return $html;
	}

	private function getUchPlanList($oppID){
			$filesOpp=array();
$sql=<<<sql
SELECT DISTINCT s.#GOD# as god, el.name as name, s.IBLOCK_ELEMENT_ID as elId, concat(f.SUBDIR,"/",f.FILE_NAME) as file,f.ORIGINAL_NAME,f.DESCRIPTION,
1 as siginfo,f.ID as fileid,s.PROPERTY_594 as displayName  
FROM b_iblock_element_prop_s#iblock2# s
left join b_iblock_element el on el.ID=s.IBLOCK_ELEMENT_ID
left join b_file f on f.ID=s.#UCHPLAN# 
where ((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y") and s.#OPP#=
sql;

			$sql.=$oppID;
			$sql.=" order by s.#GOD# desc";
			$sql=$this->sqltoiblock($sql,$this::arBlocks);
			$rez=$this->BD->query($sql);
			$gmin=3000;
			$gmax=0;
			$filesOpp=array();
			$shtml="\r\n";
					while ($rec=$rez->fetch()){
						if($rec["file"]!=""){
							$filesOpp[$rec["elId"]]["name"]=$rec["name"];
							$filesOpp[$rec["elId"]]["god"]=intval($rec["god"]);
							$filesOpp[$rec["elId"]]["src"]="/upload/".$rec["file"];
							$filesOpp[$rec["elId"]]["fname"]=$rec["DESCRIPTION"];
							$filesOpp[$rec["elId"]]["displayName"]=$rec["displayName"];
							$filesOpp[$rec["elId"]]["fileid"]=$rec["fileid"];
							if($rec["siginfo"]==1 || $rec["sign"]) $filesOpp[$rec["elId"]]["siginfo"]="<span class=\"epinfo\"></span>"; else $filesOpp[$rec["elId"]]["siginfo"]="";
						}

					}
			if(count($filesOpp)>0){
				foreach ($filesOpp as $elId=>$element){
					$name=htmlspecialcharsEx($element["name"]);
					$displayname=$name;
					if($element["displayName"]!="") $displayname=$element["displayName"];
					if($element["fname"]!="") $displayname=$element["fname"];
					$ereaId=$oppID."_mainopp_".$elId;
					$god=$element["god"];
					$title="учебный план $god ($name)";
					$shtml.="\r\n\t\t\t<div title=\"{$title}\" id=\"{$ereaId}\" class=\"maindocselement link uchplanlist\" data-title=\"{$god} {$name}\" data-opp=\"{$oppID}\"   data-iblock=\"".$this::arBlocks[2]."\" data-id=\"{$elId}\">";
					$fsrc="";
					if($element["src"]!="") 
						$fsrc=$this->fullescape($element["src"]);
					
					if($fsrc!="") 
						$shtml.="\r\n\t\t<a itemprop=\"educationPlan\" class=\"linkicon\" href=\"{$fsrc}\" data-fileid=\"{$element["fileid"]}\">{$displayname}</a>{$element["siginfo"]}";
					elseif($displayname!="")
						$shtml.="\r\n\t\t<a itemprop=\"educationPlan\" class=\"linkicon\" href=\"/sveden/empty-file.pdf\" data-fileid=\"".$element["fileid"]."\">".$displayname."</a>";
					//.$this::emptyCell.
					
					$shtml.="</div><br>";
				}
			} else{
				$idcontext=$oppID."_oppmain_0";
				$shtml.="\r\n\t\t\t<a href=\"/sveden/empty-file.pdf\" itemprop=\"educationPlan\" id=\"{$idcontext}\" class=\"maindocselement  link uchplanlist #addClassHide#\"  data-iblock=\"".$this::arBlocks[2]."\" data-opp=\"{$oppID}\" data-elid=\"0\">".$this::emptyCell."</a>";

			}
			$shtml.="";
			return $shtml;
		} //getUchPlanList

	private function generateItems(){
$sql=<<<sql
select 
bs.IBLOCK_ELEMENT_ID as id,
s.SORT as ssort,
bs.#EDUCODE# as eduCode,
el.NAME as eduName,
bs.#SROKOBUCH#	as eduFormRec,
GROUP_CONCAT(ms.VALUE SEPARATOR ',')  as opMainRec,
bs.#OOPMAIN# as opMain,
bs.#EDUEL# as eduElRec,
GROUP_CONCAT(eduel2.XML_ID SEPARATOR ',')  as eduElRec2,
if(bs.#PROFILE# is null,"",bs.#PROFILE#) as profile,
if(prel.XML_ID="fgos3pp",1,0) as fgos3pp,
if(bs.#FGOS# is null or prel.XML_ID="fgos3p",0,bs.#FGOS#) as fgoslevel,
if(prel.value is NULL or prel.XML_ID="fgos3p","",prel.value) as fgoslevelname,
if(bs.#BEEDU#>0,1,0) as beEdu,
if(bs.#ADEDU#>0,1,0) as ovz,
if(bs.#HIDE#>0,1,0) as hide,
s.id as sectionID,
s.NAME as eduLevel
from `b_iblock_element_prop_s#iblock1#` bs
left join `b_iblock_element` el on el.id=bs.IBLOCK_ELEMENT_ID
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=el.id
left join b_iblock_section s on s.id=se.IBLOCK_SECTION_ID 
left join `b_iblock_element_prop_m#iblock1#` ms on ms.IBLOCK_ELEMENT_ID=bs.IBLOCK_ELEMENT_ID and ms.IBLOCK_PROPERTY_ID=#OOPMAIN_ID# 
left join `b_iblock_element_prop_m#iblock1#` ms1 on ms1.IBLOCK_ELEMENT_ID=bs.IBLOCK_ELEMENT_ID and ms1.IBLOCK_PROPERTY_ID=bs.#EDUEL# 
left join `b_iblock_property_enum` prel on prel.id=bs.#FGOS#
left join b_iblock_property_enum eduel2 on eduel2.ID=ms1.VALUE_ENUM
WHERE ((( ((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y")  or el.id is NULL) #where#
GROUP BY bs.IBLOCK_ELEMENT_ID
sql;
		$where="";
		$ovz=intval($this->ovz)>0;
		if ($ovz) {
			
			$where1=" and (bs.#ADEDU#>0 and bs.#ADEDU# is not null)";
		}else{
			$where1=" and (bs.#ADEDU#=0 or bs.#ADEDU# is null)";
		}

		if(!$this->isEditmode){
			$where1.=" and (bs.#HIDE#=0 or bs.#HIDE# is NULL)";
		}
		$sql=str_replace("#where#",$where1,$sql);
		$sql=$this->sqltoiblock($sql,$this::arBlocks);
   //echo "<!-- ".$sql."-->";
		$rez=$this->BD->query($sql);
		$this->listitems=array();
		while ($rec=$rez->fetch()){
			$item=array();
			$item["id"]=$rec["id"];
			$item["ssort"]=$rec["ssort"];
			$idcontext="";

			$item["eduCode"]=$rec["eduCode"];
			$item["eduName"]=$rec["eduName"];

/*
$string = 'April 15, 2003';
$pattern = '/(\w+) (\d+), (\d+)/i';
$replacement = '${1}1,$3';
echo preg_replace($pattern, $replacement, $string);
*/
			$item["eduProf"]=$this::emptyCell;
			if($rec["profile"]!=""){
			/*
			$item["eduProf"]=trim(preg_replace('/^(\d+).(\d+).(\d+)/','', $rec["profile"]));
			$item["eduProf"]=trim(preg_replace('/^(\-)/',"", $item["eduProf"])); 
			$item["eduProf"]=trim(preg_replace('/^(\–)/',"", $item["eduProf"]));
			*/
			$item["eduProf"]=trim($rec["profile"]);
			}
			//$item["profile"]=$rec["profile"];
			$item["hide"]=$rec["hide"];
			$item["sectionID"]=$rec["sectionID"];
			$item["eduLevel"]=$rec["eduLevel"];
			$item["eduCode"]=$rec["eduCode"];
			
			//$item["fgos"]=($rec["fgos3pp"])?"ФГОС 3++":"ФГОС 3+";
			$item["fgos"]=$rec["fgoslevel"];

			$item["fgosName"]=$rec["fgoslevelname"];

			$item["beEdu"]=$rec["beEdu"];
			
			$item["educationAnnotation"]="\r\n".$this->getOppFileList($rec["id"],"annot",$rec["beEdu"],0,$rec["eduCode"])."";
			$item["educationRpd"]="\r\n".$this->getOppFileList($rec["id"],"rpd",$rec["beEdu"],0,$rec["eduCode"])."";
			$item["educationShedule"]=$this->getOppFileList($rec["id"],"graf",$rec["beEdu"],0,$rec["eduCode"]);
			$item["methodology"]=$this->getOppFileList($rec["id"],"doc",$rec["beEdu"],$rec["sectionID"],$rec["eduCode"]);
			$item["eduPr"]=$this->getOppFileList($rec["id"],"pract",$rec["beEdu"],0,$rec["eduCode"]);
			$item["educationPlan"]=$this->getUchPlanList($rec["id"]);
			$eduForms=array();
			//$eduFormAr=unserialize($rec["eduFormRec"]);
			$arforms=$this->unserform($rec["eduFormRec"]);
			/*$arforms=[1=>"",2=>"",3=>""];
			$s=mb_substr($rec["eduFormRec"],5,-1);

			//	echo $s."<br>";
			$arforms0= explode(";",$s);
			$arforms1=array();
			foreach($arforms0 as $c){
				$arforms1[]=explode(":",$c);
			}
			$arforms[1]=$arforms1[1][2];
			$arforms[2]=$arforms1[3][2];
			$arforms[3]=$arforms1[5][2];
			*/
			if(strlen($arforms[1])>4) {
				$eduForms[1]=($item["beEdu"])?"Full time / Очная форма":"Очная форма";
			}
			if(strlen($arforms[2])>4) {
				$eduForms[2]=($item["beEdu"])?"Part-time / Заочная форма":"Заочная форма";
			}
			if(strlen($arforms[3])>4) {
				$eduForms[3]=($item["beEdu"])?"Part-time / Очно-заочная форма":"Очно-заочная форма";
			}
			$opMainAr=array();
			if($rec["opMainRec"]!="") {
				$opMainAr=explode(",",$rec["opMainRec"]);
			} else{
				if(intval($rec["opMain"])>0 ) $opMainAr=array(intval($rec["opMain"]));
			}
			

			$item["opMain"]=$this->getOpMain($opMainAr);
			
			
			//$eduElAr=unserialize($rec["eduElRec"]);
			$eduElAr=explode(",",$rec["eduElRec2"]);
			
			$item["eduEl"]="";

			if(in_array("1e05d677066c88b453d2f9069f87fb0f",$eduElAr)) $item["eduEl"].=" электронное обучение"; 
			if($item["eduEl"]!="") $item["eduEl"].=",<br>"; 
			if(in_array("afa19012f35f883d9b02b8c0ed8d696f",$eduElAr)) $item["eduEl"].=" дистанционные технологии"; 
			if($item["eduEl"]=="" || in_array("8562eb7f616fdca8283af1644f479d9e",$eduElAr)) $item["eduEl"]=" не предусмотрено образовательной программой"; 


			$sortindex=(1000+intval($item["ssort"])).$item["fgos"].($item["beEdu"]?"1":"0").(9999-intval($item["sectionID"]));

			foreach($eduForms as $formID=>$form){
				
				$idcontext="opp_".$item["id"]."_".$formID;
				$sortitem=$item["eduCode"]."_".md5($item["eduProf"])."_".$formID."_".$item["id"];


				$title0=$item["eduName"]." ".$item["eduProf"]." (".$form.")";
				$shtml="<p title=\"{$title0}\" id=\"{$idcontext}\" class=\"maindocselement \" data-id=\"{$item["id"]}\" data-iblock=\"".$this::arBlocks[1]."\">{$item["eduCode"]}</p>";
				$item["eduCode"]=$shtml;
				$item["idcontext"]=$idcontext;
				$this->listitems[$sortindex]["items"][$sortitem]=$item;
				$this->listitems[$sortindex]["eduLevel"]=$item["eduLevel"];
				$this->listitems[$sortindex]["sectionID"]=$item["sectionID"];
				$this->listitems[$sortindex]["beEdu"]=$item["beEdu"];
				
				$this->listitems[$sortindex]["fgos"]=$item["fgos"];
				$this->listitems[$sortindex]["fgosName"]=$item["fgosName"];
				$this->listitems[$sortindex]["items"][$sortitem]["eduForm"]=$form;


				


	
			}


			//"eduForm"=>0,
			
			//"eduEl"=>$eduEl,
			
			
		}


	}//generateItems
	public function showHtml($buffer=false){

		//печать itemprop для каждого элемента
		$itempropsEach=array(
			"eduCode"=>0,
			"eduName"=>0,
			"eduProf"=>0,
			"eduLevel"=>0,
			"eduForm"=>0,
			"opMain"=>1,
			"educationPlan"=>1,
			"educationAnnotation"=>1,
			"educationRpd"=>1,
			"educationShedule"=>1,
			"methodology"=>1,
			"eduPr"=>1,
			//"eduEl"=>0
		);
		$itemprops=array(
			"eduCode"=>"Код специальности, направления подготовки, шифр
группы научных специальностей",
			"eduName"=>"Наименование профессии, специальности, направления
подготовки, наименование группы научных
специальностей",
			"eduProf"=>"Образовательная программа, направленность, профиль,
шифр и наименование научной специальности",
			"eduLevel"=>"Реализуемый уровень образования",
			"eduForm"=>"Реализуемые формы обучения",
			"opMain"=>"Описание образовательной программы с приложением ее
копии в виде электронного документа, подписанного
электронной подписью",
			"educationPlan"=>"Учебный план в виде электронного документа,
подписанного электронной подписью",
			"educationAnnotation"=>"Ссылки на аннотации к рабочим программам дисциплин (по каждой дисциплине в составе образовательной программы)",
			"educationRpd"=>"Рабочие программы (по каждой дисциплине в составе
образовательной программы) в виде электронного
документа, подписанного электронной подписью",
			"educationShedule"=>"Календарный учебный график в виде электронного
документа, подписанного электронной подписью",
			"methodology"=>"Методические и иные документы, разработанные
образовательной организацией для обеспечения
образовательного процесса, а также рабочие программы
воспитания и календарные планы воспитательной работы,
включаемых в ООП в виде электронного документа,
подписанного электронной подписью",
			"eduPr"=>"Рабочие программы практик, предусмотренных
соответствующей образовательной программой в виде
электронного документа, подписанного электронной
подписью",
			//"eduEl"=>"Ссылки на информацию об использовании при реализации образовательных программ электронного обучения и дистанционных образовательных технологий",
			
		);

		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		
		$casheData=$memcache->get($this->cashGUID);
		if($this->isEditmode())$casheData.="_edit";
		if (is_array($casheData)){ 
			if (isset($casheData["html"])&& isset($casheData["cnt"])) {
				$html=$casheData["html"];
				$cnt=$casheData["cnt"];
			}	
		} else 	$html=$casheData;
						
		
		
		
		$tableheader="Информация об образовательной программе";
		$mainItemProp="eduOp";
		if ($this->ovz) {
			$mainItemProp="eduAdOp";
			$tableheader="Информация об адаптированной образовательной программе";
		}
		if($this->hideTitle==1)$tableheader="";
	if ($html=="" || $html==false || $this->isEditmode())
	{
		$html="";
		
		
		
		
		$kitemprops=array_keys($itemprops);
		$this->generateItems();
		if($tableheader!="") $html="\r\n<h2>".$tableheader."</h2>\r\n";
		$html.="\r\n<table class=\"opptable \" style=\"font-size:8pt;\">\r\n<thead>\r\n<tr>";
		//создаем шапку
		foreach ($itemprops as $itemprop=>$caption){
			$html.="<th";
			if($itemprop=="eduLevel") $html.=" class=\"hide\"";
			$html.=">".$caption."</th>";
		}
		$html.="</tr>\r\n</thead>\r\n<tbody>\r\n";
		ksort($this->listitems);

		if(count($this->listitems)==0){
				$shtml="<p title=\"{$title0}\" id=\"{$idcontext}\" class=\"maindocselement \" data-id=\"0\" data-iblock=\"".$this::arBlocks[1]."\">*</p>";

				$html.="\r\n<tr itemprop=\"{$mainItemProp}\" class=\"hide\">";
				foreach($kitemprops as $kitemprop){
					$html.="\r\n\t<td ";
					$itmprop=" ";
					if($kitemprop=="eduLevel") {
						$htmlr1.=" class=\"hide\"";
					}
					$html.=$itmprop." ><a href=\"#\" itemprop=\"$kitemprop\" >".$this::emptyCell."</a></td>";
				}
				$html.="</tr>\r\n";

			$html.="<tr title=\"Программы\" id=\"{$this::arBlocks[1]}_new\" class=\"maindocselement \" data-id=\"0\" data-iblock=\"".$this::arBlocks[1]."\" ><td colspan=".count($itemprops).">Программы не реализуются</td></tr>";
				$html.=$htmlr."\r\n";
		}

$this->arLeves=array(
186=>"Среднее профессиональное образование - программы подготовки специалистов среднего звена",
179=>"Высшее образование - программы бакалавриата",
180=>"Высшее образование - программы специалитета",
184=>"Высшее образование - программы магистратуры",
182=>"Высшее образование - программы подготовки научных и научно-педагогических кадров в аспирантуре (адъюнктуре)",
181=>"Высшее образование - программы ординатуры",
424=>"Дополнительное профессиональное образование",
);

		foreach($this->listitems as $sortx=>$levels){
			$levelName=$this->arLeves[$levels["sectionID"]];
			$htmlr="<tr><th colspan=\"12\" class=\"oppheader #hide0#\"><!-- {$sortx} -->{$levelName}";
			if($levels["eduLevel"]=="Специалитет"){
				 $htmlr.=" ".$levels["fgosName"];
				if($levels["beEdu"]) $htmlr.=" (Билингвальное обучение) ";
			}else{
				 $htmlr.=" ".$levels["fgosName"];
			}
			$htmlr.="</th></tr>\r\n";
			ksort($levels["items"]);
			$cnt=count($levels["items"]);
			foreach($levels["items"] as $item){
				$sortId=$item["idcontext"];
				$htmlr1="\r\n<tr itemprop=\"{$mainItemProp}\" class=\"";
				if($item["hide"]){$cnt--;$htmlr1.="#hide#";}
				$htmlr1.="\">";
				foreach($kitemprops as $kitemprop){
					if($item[$kitemprop]=="") {
						$itemkitemprop=$this::emptyCell;
					}else{
						$itemkitemprop=$item[$kitemprop];
					}

					if($itempropsEach[$kitemprop]==0){
						$htmlr1.="\r\n\t<td itemprop=\"$kitemprop\" ";
						if($kitemprop=="eduLevel") {
							$htmlr1.=" class=\"hide\"";
						}
						$htmlr1.=$itmprop ." > {$item[$kitemprop]}\r\n\t</td>";
					}else{
						if($item[$kitemprop]==""){
							$htmlr1.="\r\n\t<td><a itemprop=\"$kitemprop\" href=\"#\"> {$itemkitemprop}</a></td>\r\n";
						}else{
							$htmlr1.="\r\n\t<td>{$itemkitemprop}\r\n\t</td>\r\n";
						}

					}
								
					

				}
				$htmlr1.="</tr>\r\n";
				if (!$this->isEditmode() && $item["hide"]){$htmlr1="";}	
				$htmlr.=$htmlr1."\r\n";
			}
			
				

			if($cnt<=0) 
			{

				$htmlr=str_replace("#hide#","#addClassHide#",$htmlr); 
				$htmlr=str_replace("#hide0#","#addClassHide#",$htmlr); 
				$htmlr.="<tr itemprop=\"{$mainItemProp}\" class=\"#noitem#\" >";
				
				foreach($kitemprops as $kitemprop){
					if($itempropsEach[$kitemprop]==0){
						$htmlr.="\t<td ";
							if($kitemprop=="eduLevel") {
								$htmlr.=" class=\"hide\"";
							}

						$htmlr.="><a href=\"#\" itemprop=\"{$kitemprop}\" ".$this::emptyCell."</a></td>\r\n";
					}else{
						$htmlr.="\t<td ";
							if($kitemprop=="eduLevel") {
								$htmlr.=" class=\"hide\"";
							}
						$htmlr.="><a itemprop=\"$kitemprop\" href=\"#\"".$this::emptyCell."</a></td>\r\n";
					}
					
				}
				$htmlr.="</tr>\r\n";
				
			}else {
				if($htmlr!=""){
					$htmlr=str_replace("#hide0#","",$htmlr); 
					$htmlr=str_replace("#hide#","#addClassHide#",$htmlr); 
				}
			}
			$html.=$htmlr."\r\n";
		}
		$html.="\r\n</tbody>\r\n</table>\r\n<!--".date("d.m.Y H:i:s")." -->";
		$html=$html;
		$casheData=array("cnt"=>$cnt,"html"=>$html);
		$result = $memcache->replace( $this->cashGUID, $casheData);
		if( $result == false )
		{
		  $memcache->set($this->cashGUID, $casheData, false, $this->cashTime);
		} 
		
	} 
			
		$memcache->close();	
		
		/* не кешируемая часть */
					
				$addClassHide="";$addClassHide3="";
				if($this->isEditmode()) {
					$addClassHide=" gray ";$addClassHide2="hide";
					
				}else{
					$addClassHide=" hide ";$addClassHide2="gray";
					if($cnt==0) {
						$html="\r\n<h2>".$tableheader."</h2>\r\n";
						if ($this->ovz) {
							$html.="<br><b>Адаптированные образовательные программы в настоящее время не реализуются</b><br>";
						}else{
							$html.="<br><b>Образовательные программы в настоящее время не реализуются</b><br>";
						}
						$html.="\r\n<table class=\"hide \"><tr itemprop=\"{$mainItemProp}\">";
						//создаем табличку
						foreach ($itemprops as $itemprop=>$caption){
							if($itempropsEach[$itemprop]==1)
								$html.="<td><a itemprop=\"$itemprop\" href=\"/sveden/emptyFile.pdf\">".$this::emptyCell."</td>";
							else
								$html.="<td itemprop=\"$itemprop\" >".$this::emptyCell."</td>";
						}
						$html.="</tr>\r\n</table>\r\n<!--".date("d.m.Y H:i:s")." -->";
					}
				}

				if($html!=""){
					$html=str_replace("#addClassHide#",$addClassHide,$html);
					$html=str_replace("#noitem#",$addClassHide2,$html);
					$html=str_replace("#addClassHide3#",$addClassHide3,$html);
				}

				
		/*--------------------*/	
		
		if($buffer) return $html; else echo $html;
	}

}//class
?>