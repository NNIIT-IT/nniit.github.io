<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
use \Bitrix\Main\Data\Cache;
class teachingStaff extends  iasmuinfo{
	const cashTime=6000;
	const emptyCell="отсутствует";
	private $cashGUID="teachingStaff";
	private $listitems=array();

	private $eduLevelId=0;
	private $oppId=0;
	private $arOpp=array();
	private $arUsers=array();
	private $managers=0;
	public function setparams($params){
		$this->oppId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		if(isset($params["opp"])) 
			$this->oppId=intval($params["opp"]);
		if(isset($params["managers"])) $this->managers=intval($params["managers"]);

		$this->cashGUID="teachingStaff_".$this->managers."_".$this->eduLevelId."_".$this->oppId;
	}
	function readUsersFromBD(){
		$sql="select ID, UF_NIR as nir, UF_PPS as pps, UF_PHONE as phone, UF_EMAIL as email,";
		$sql.="UF_CHILDEXPERIENCE as childExperience, UF_MEDEXPERIENCE as medExperience,";
		$sql.="UF_TEACHINGOP as teachingOp, UF_SPECEXPERIENCE as specExperience,";
		$sql.="UF_GENEXPERIENCE as genExperience, UF_PROFDEVELOPMENT as profDevelopment,";
		$sql.="UF_SPECEXPERIENCE_B as specExperienceB, UF_GENEXPERIENCE_B as genExperienceB, ";
		$sql.="UF_ACADEMSTAT as academStat, UF_DEGREE as degree,UF_QUALIFICATION as qualification,";
		$sql.="UF_TEACHINGQUAL as teachingQual , UF_TEACHINGLEVEL as teachingLevel, UF_TEACHINGDISCIPLIN as teachingDiscipline,";
		$sql.="UF_POST as post, UF_FIO as fio, UF_PHONE as telephone, ";
		$sql.="UF_HI_STAFF as hipost, UF_HI_ZAM as hizam, UF_EMAIL as email ";
		$sql.="from prepod ";
		$sql.="where UF_ACTIVE order by UF_HI_STAFF desc, UF_FIO asc";
	
		$res = $this->BD->query($sql);
		while($ob=$res->fetch()){

			$this->arUsers[$ob["ID"]]=$ob;
			//$this->arUsers["teachingOp"]=json_decode($this->arUsers["teachingOp"],true);//=array(id=>oppID,name=>oppName)
			$this->arUsers[$ob["ID"]]["teachingOp"]=array();
			$jsonValue=str_replace("&quot;",'"',$ob["teachingDiscipline"]);
			//echo $jsonValue;
			$this->arUsers[$ob["ID"]]["teachingDiscipline"]=array();
			//$arjsonDisc=\Bitrix\Main\Web\Json::decode(mb_convert_encoding($jsonValue, 'UTF-8', 'windows-1251'));//
			$arjsonDisc=\Bitrix\Main\Web\Json::decode($jsonValue);//
			foreach($arjsonDisc as $discRow){
				$this->arUsers[$ob["ID"]]["teachingOp"][$discRow["opp"]]=$discRow["opp"];
				$this->arUsers[$ob["ID"]]["teachingDiscipline"][]=$discRow["disc"];
			}
			
		}
	
	}



	function readOppFromBD(){
		if(count($this->arUsers)>0){
			$needOppIdsAll=array();
			foreach($this->arUsers as $ku=>$xuser){
					$needOppIdsUser=array();
					foreach($xuser["teachingOp"] as $oppID){
						$needOppIdsAll[$oppID]=$oppID;
					}
			}
			if(CModule::IncludeModule("iblock")){ 
				$this->arOpp=array();
				$arFilter=array("IBLOCK_ID"=>59,"ACTIVE"=>"Y");
				//if(count($needOppIdsAll)>0) 
				//	$arFilter["ID"]=implode("|",$needOppIdsAll);
					
				$arSelect=array("NAME","PROPERTY_PROFILE","PROPERTY_EDUCODE","ID","IBLOCK_SECTION_ID");
				$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
				while($ar_fields = $res->GetNext())
					{
						$this->arOpp[$ar_fields["ID"]]["name"]=$ar_fields["PROPERTY_EDUCODE_VALUE"]." ".$ar_fields["NAME"]." ".$ar_fields["PROPERTY_PROFILE_VALUE"];
						$this->arOpp[$ar_fields["ID"]]["eduCode"]=$ar_fields["PROPERTY_EDUCODE_VALUE"];
						$this->arOpp[$ar_fields["ID"]]["level"]=$this::sectionsName[$ar_fields["IBLOCK_SECTION_ID"]];
						$this->arOpp[$ar_fields["ID"]]["sectionID"]=$ar_fields["IBLOCK_SECTION_ID"];
					}
				
			}		
		}
	}
	public function generateItems(){
		$this->readUsersFromBD();
		$this->readOppFromBD();
		$this->listitems=array();
		foreach($this->arUsers as $rsUser){
			$this->listitems[$rsUser["ID"]]["fio"]=$rsUser["fio"];
			$this->listitems[$rsUser["ID"]]["hipost"]=intval($rsUser["hipost"])==1;
			$this->listitems[$rsUser["ID"]]["hizam"]=intval($rsUser["hizam"])==1;
			$this->listitems[$rsUser["ID"]]["telephone"]=$rsUser["telephone"];
			$this->listitems[$rsUser["ID"]]["email"]=$rsUser["email"];
			$this->listitems[$rsUser["ID"]]["post"]=str_replace("&amp;","",$rsUser["post"]);

			$this->listitems[$rsUser["ID"]]["post"]=str_replace(array(";","\r\n"),array("<br>","<br>"),$rsUser["post"]);
			$this->listitems[$rsUser["ID"]]["teachingOp"]="";
			foreach($rsUser["teachingOp"] as $oppID){
				if($this->arOpp[$oppID]["name"]!="") $this->listitems[$rsUser["ID"]]["teachingOp"].=$this->arOpp[$oppID]["name"]."<br>";
			}
			$this->listitems[$rsUser["ID"]]["teachingDiscipline"]="<br><br>";
			$rsUser["teachingDiscipline"]=array_unique($rsUser["teachingDiscipline"]);
			foreach($rsUser["teachingDiscipline"] as $discName){
				if($discName!="") $this->listitems[$rsUser["ID"]]["teachingDiscipline"].=$discName."<br><br>";
			}

			$this->listitems[$rsUser["ID"]]["teachingLevel"]=str_replace(array(";","\r\n"),"<br>",$rsUser["teachingLevel"]);
			$this->listitems[$rsUser["ID"]]["teachingQual"]=str_replace(array(";","\r\n"),"<br>",$rsUser["teachingQual"]);
			$artmp1=array_filter(explode("<br>",$this->listitems[$rsUser["ID"]]["teachingLevel"]));
			$artmp2=array_filter(explode("<br>",$this->listitems[$rsUser["ID"]]["teachingQual"]));
			$artmp3=array();
			foreach ($artmp1 as $k=>$artmp1v){
				$artmp3[$k]=trim($artmp1v);
				if(isset($artmp2[$k])) $artmp3[$k].=", «".trim($artmp2[$k])."»";
			}
			$this->listitems[$rsUser["ID"]]["teachingLevel"]=implode(";<br><br>",$artmp3);
			$this->listitems[$rsUser["ID"]]["degree"]=str_replace(array(";","\r\n"),"<br>",$rsUser["degree"]);
			$this->listitems[$rsUser["ID"]]["qualification"]=str_replace(array(";","\r\n"),";<br><br>",$rsUser["qualification"]);
			$this->listitems[$rsUser["ID"]]["academStat"]=str_replace(array(";","\r\n"),"<br>",$rsUser["academStat"]);
			$this->listitems[$rsUser["ID"]]["profDevelopment"]=str_replace(array(";","\r\n"),"<br>",$rsUser["profDevelopment"]);

			$this->listitems[$rsUser["ID"]]["specExperience"]=$this->allmounstostr($rsUser["specExperience"]);
			if($rsUser["specExperienceB"]!=""){
				$specExperienceM=strtotime($rsUser["specExperienceB"]);
				$dYn=intval(date("Y"))*12+intval(date("m"))-intval(date("Y",$specExperienceM))*12-intval(date("m",$specExperienceM));
				if($dYn>0) $this->listitems[$rsUser["ID"]]["specExperience"]=$this->allmounstostr($dYn);
			}

			$this->listitems[$rsUser["ID"]]["genExperience"]=$this->allmounstostr($rsUser["genExperience"]);
			if($rsUser["genExperienceB"]!=""){
				$specExperienceM=strtotime($rsUser["genExperienceB"]);
				$dYn=intval(date("Y"))*12+intval(date("m"))-intval(date("Y",$specExperienceM))*12-intval(date("m",$specExperienceM));
				if($dYn>0) $this->listitems[$rsUser["ID"]]["genExperience"]=$this->allmounstostr($dYn);
			}

			if($this->listitems[$rsUser["ID"]]["post"]=="") $this->listitems[$rsUser["ID"]]["post"]=$this::emptyCell;

			if($this->listitems[$rsUser["ID"]]["teachingDiscipline"]=="") $this->listitems[$rsUser["ID"]]["teachingDiscipline"]=$this::emptyCell;

			if($this->listitems[$rsUser["ID"]]["teachingOp"]=="") $this->listitems[$rsUser["ID"]]["teachingOp"]=$this::emptyCell;
			if($this->listitems[$rsUser["ID"]]["teachingLevel"]=="") $this->listitems[$rsUser["ID"]]["teachingLevel"]=$this::emptyCell;
			if($this->listitems[$rsUser["ID"]]["teachingQual"]=="") $this->listitems[$rsUser["ID"]]["teachingQual"]=$this::emptyCell;
			if($this->listitems[$rsUser["ID"]]["profDevelopment"]=="") $this->listitems[$rsUser["ID"]]["profDevelopment"]=$this::emptyCell;
			if($this->listitems[$rsUser["ID"]]["genExperience"]=="") $this->listitems[$rsUser["ID"]]["genExperience"]=$this::emptyCell;
			if($this->listitems[$rsUser["ID"]]["specExperience"]=="") $this->listitems[$rsUser["ID"]]["specExperience"]=$this::emptyCell;
			
		};
//echo"<pre>";	print_r($this->listitems);	echo"</pre>";
//echo"<pre>";	print_r($this->arUsers);	echo"</pre>";
	}
	private function prepareHtmlRucovodstvo(){
		$fieldsListHi=array(
			"num"=>array("№",2),
			"fio"=>array("ФИО руководителя",6),
			"post"=>array("Должность",6),
			"telephone"=>array("Контактные телефоны",6),
			"email"=>array("адрес электронной почты",6)
		);
		$html.="<!-- hi staff list table -->";
		$html.="<h2>Информация о руководителе образовательной организации </h2>";
		$html.='<div style="display: table;    table-layout: fixed;width: 100%;" id="L_begin0" >';
		$html.='<div  class="prepodHeadCell" style="display:table-row;">';
			foreach($fieldsListHi as $c){$html.='<div style="width:'.$c[1].'em;display:table-cell;">'.$c[0].'</div>';}
		$html.='</div>';
		$hipost=false;
		foreach($this->listitems as $userId=>$userRow){
			if($userRow["hipost"]){
				$hipost=true;
				$html.='<div class="maindocselementStaff" style="display:table-row;" id="rowHI'.$userId.'" data-iblock="11" data-id="'.$userId.'" title="'.$userRow["post"].'" itemprop="rucovodstvo">';
				foreach($fieldsListHi as $itemprop=>$c){
					$sval=str_replace("&amp<br>amp","<br>",$userRow[$itemprop]);
					$html.='<div style="width:'.$c[1].'em; display:table-cell;" class="prepodRowCell" itemprop="'.$itemprop.'">'.$sval.'</div>';
				}
				$html.='</div>';
			}
		}

		if(!$hipost){
				$html.='<div style="display:table-row;" class="maindocselementStaff" id="rowHI0" data-iblock="11" data-id="0" title="Руководитель организации" itemprop="rucovodstvo" >';
				foreach($fieldsListHi as $itemprop=>$c){
					$html.='<div style="width:'.$c[1].'em; display:table-cell;"  class="prepodRowCell" itemprop="'.$itemprop.'"  >'.$this::emptyCell.'</div>';
				}
				$html.='</div>';
		}
		$html.='</div>';//table end
		return $html;
	}
	private function prepareHtmlRrucovodstvoZam(){
		$fieldsListHi=array(
			"num"=>array("№",2),
			"fio"=>array("ФИО руководителя",6),
			"post"=>array("Должность",6),
			"telephone"=>array("Контактные телефоны",6),
			"email"=>array("адрес электронной почты",6)
		);
		$html.="<h2>Информация заместителях руководителя образовательной организации </h2>";
		$html.='<div style="display: table;    table-layout: fixed;width: 100%;" id="L_begin1" >';
		$html.='<div  style="display:table-row;">';
		foreach($fieldsListHi as $c){
			$html.='<div class="prepodHeadCell" style="width:'.$c[1].'em;">'.$c[0].'</div>';
		}
		$html.='</div>';

		$hizam=false;
		foreach($this->listitems as $userId=>$userRow){
			if($userRow["hizam"]){
				$hizam=true;
				$html.='<div style="display:table-row;" class="maindocselementStaff" id="rowHI'.$userId.'" data-iblock="11" data-id="'.$userId.'" title="'.$userRow["post"].'" itemprop="rucovodstvoZam" >';
				foreach($fieldsListHi as $itemprop=>$c){
					$sval=str_replace("&amp<br>amp","<br>",$userRow[$itemprop]);
					$html.='<div style="width:'.$c[1].'em; display:table-cell;"  class="prepodRowCell" itemprop="'.$itemprop.'">'.$sval.'</div>';
				}
				$html.='</div>';
			}
		}

		if(!$hizam){
				$html.='<div style="display:table-row;" class="maindocselementStaff" id="rowHIZ0" data-iblock="11" data-id="0" title="Руководитель организации" itemprop="rucovodstvoZam" >';
				foreach($fieldsListHi as $itemprop=>$c){
					$html.='<div style="width:'.$c[1].'em; display:table-cell;" class="prepodRowCell" itemprop="'.$itemprop.'"  >'.$this::emptyCell.'</div>';
				}
				$html.='</div>';
		}
		$html.='</div>';//table end
		return $html;
	}
	private function prepareHtml(){
		
		$fieldsList=array(
			"num"=>array("№",2),
			"fio"=>array("Ф.И.О.",6),
			"post"=>array("Должность",6),
			"teachingDiscipline"=>array("Перечень препода&shy;ваемых дисциплин",6),
			"teachingLevel"=>array("Уровень образования, квалификация",6),
			"degree"=>array("Учёная степень",6),
			"academStat"=>array("Учёное звание",6),
			"qualification"=>array("Сведения о повышении квалификации (за последние 3 года)",8),
			"profDevelopment"=>array("Сведения о профессиональной переподготовке (при наличии)",8),
			"genExperience"=>array("Общий стаж работы",5),
			"specExperience"=>array("Сведения о продолжительности опыта (лет) работы в профессиональной сфере",6),
			"teachingOp"=>array("Наименование образовательных программ, в реализации которых участвует педагогический работник",6),
		);

		if($this->managers==1){
			$fieldsList["fio"]="ФИО руководителя";
			$html.=$this->prepareHtmlRucovodstvo();
			$html.=$this->prepareHtmlRrucovodstvoZam();
		}else{
		$html.="<p></p>";
		$html.="<h2> Информация о персональном составе
педагогических работников образовательной
программы</h2>";
		$html.="<!--  -->";
		$html.='<div style="background-color: #d3d3d35e;padding: 10px;">';
		$html.='<b>Фильтр: </b>';
		$html.='<input class="filterInput"  id="filterInput1" data-id="1" value="" placeholder="ФИО" >';
		if(count($this->arOpp)>1){
		$html.="<select class=\"filterInput\"  id=\"filterInput2\" data-id=\"2\" >";
			$html.="<option value=\"\">Все образовательные программы</option>";
			foreach($this->arOpp as $opp){
				$html.="<option value=\"{$opp['eduCode']}\">{$opp['name']}</option>";
			}
			//$html.='<input class="filterInput"  id="filterInput2" data-id="2" value="" placeholder="Наименование или код ОП" >';
			//print_r($this->arOpp);
		$html.='</select>';
		}
		$html.='<button class="filterInputClear buttongoogle">Очистить фильтр</button>';
		$html.='</div>';
		$html.='<div style="display: table;    table-layout: fixed;width: 100%;" id="L_begin2" >';
		$html.='<div  style="display:table-row;">';
		foreach($fieldsList as $c){
			$html.='<div class="prepodHeadCell" style="width:'.$c[1].'em;">'.$c[0].'</div>';
		}
		$html.='</div>';
		$rowNum=0;
		
		if(count($this->listitems)==0){
			$html.='<div  class="prepodrow maindocselementStaff" id="row0" data-id="0" data-iblock="11" title="'.$userRow["fio"].'" itemprop="teachingStaff">';
				foreach($fieldsList as $itemprop=>$RowCellData){
					$userRowCell=$this::emptyCell;
					if(in_array(
						$itemprop,
						array(
							'teachingDiscipline','teachingOp','profDevelopment'
						)
					)){
						$html.='<div class="prepodRowCell" title="'.$this::emptyCell.'" itemprop="'.$itemprop.'">';
						$html.='</div>';
					}else{
						$html.='<div class="prepodRowCell '.$addClassCell.'" itemprop="'.$itemprop.'" title="'.$RowCellData[0].'">'.$userRowCell.'</div>';
					}
				}	
			$html.='</div>';
		}

		foreach($this->listitems as $userId=>$userRow){
			$html.='<div  itemprop="teachingStaff" class="prepodrow maindocselementStaff" id="row'.$userId.'" data-iblock="11" data-id="'.$userId.'" title="'.$userRow["fio"].'">';
				$rowNum++;
				foreach($fieldsList as $itemprop=>$RowCellData){
					$userRowCell=($userRow[$itemprop]!="")?$userRow[$itemprop]:$this::emptyCell;
					if($itemprop=='num') $userRowCell=$rowNum;


					$addClassCell=($userRowCell==$this::emptyCell)?" emptyCell":"";
					if(in_array(
						$itemprop,
						array(
							'teachingDiscipline','teachingOp','profDevelopment','qualification',
						)
						
					)){
						$userRowCell=str_replace('\"','"',$userRowCell);
						$userRowCell=str_replace('<br>;<br>','<br>',$userRowCell);
						$html.='<div class="prepodRowCell'.$addClassCell.'" title="'.$RowCellData[0].'">';
						$html.='<span class="texticon hidedivlink linkicon link">Смотреть</span>';
						$html.='<div class="hoverhint" style="display:none" itemprop="'.$itemprop.'" >'.$userRowCell.'</div>';
						$html.='</div>';
					}else{
						$html.='<div class="prepodRowCell '.$addClassCell.'" itemprop="'.$itemprop.'" title="'.$RowCellData[0].'">'.$userRowCell.'</div>';
					}
				}	
			$html.='</div>';
		}
		$html.='</div>';	
		}
		return array("html"=>$html,"chainName"=>$chane["name"],"chainPath"=>$chane["path"]);
	}
public function showHtml($buffer=false){

		global $APPLICATION;
		
		if($this->cashGUID=="teachingStaff" || $this->cashGUID=="" ){
			$this->cashGUID="teachingStaff_".$this->eduLevelId."_".$this->oppId;
		}
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$OB=$memcache->get($this->cashGUID);
		if($OB!="" && !$this::isEditmode() ){
		   // получаем закешированные переменные
		    $vars=json_decode($OB,true);
	 	    $html = $vars["html"];
		    $chainName = $vars["chainName"];
		    $chainPath = $vars["chainPath"];

		} else{
			$this->generateItems();
			$vars=$this->prepareHtml();
			$OB=json_encode($vars);
			$result = $memcache->replace( $this->cashGUID, $OB);
			if( $result == false ){
				$memcache->set($this->cashGUID, $OB, false, $this->cashTime);
			} 
			$html = $vars["html"];
			$chainName = $vars["chainName"];
			$chainPath = $vars["chainPath"];

		}
		//кавычки восстановление
		$html=str_replace('&amp<br>amp<br>#34','<br>&#34;',$html);
		$memcache->close();
		if($buffer) {
			return $html; 
		}
		else{
			if($chainName!=""){
				$APPLICATION->AddChainItem($chainName, $chainPath);
			}	
			echo $html;
		}	
	if($this::isEditmode()){
		echo "<button id=\"prepodImport\" onclick=\"prepodImport();\" style=\"border: solid thin gray;background-color: lightgray;border-radius: 3px;font-size: 10px;margin: 5px;\">Импортировать данные </button>";
		CJSCore::Init(array("jquery"));
		echo "<script src=\"/sveden/class/cl-teachingStaff/cl-teachingStaff_adm.js\"></script>";
	}	
	}//showHtml
}