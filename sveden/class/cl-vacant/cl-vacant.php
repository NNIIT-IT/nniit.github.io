<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
	/*	require_once($_SERVER['DOCUMENT_ROOT'].'/dompdf8/autoload.inc.php');
		require_once($_SERVER["DOCUMENT_ROOT"]."/local/common/class/pdfsign/pdfsign.php");
		use Dompdf\Dompdf;
		use Dompdf\Options;
*/
use asmuinfoclasses;
/*необходимо скорректировать на численность вакантных мест*/
class vacant extends  iasmuinfo{
	const cashTime=342000;
	private $cashGUID;
	private $arVacant;
	private $testmode;
	private $manual;
	public function setparams($params){
		$this->edulevelID=0;
		$this->russ=0;
		$this->testmode=false;
		if(isset($params["testmode"])) {
			$this->testmode=true;
		} 
		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);
		}
		if(isset($params["russ"])) {
			$this->russ=intval($params["russ"]);
		}
		if(isset($params["manual"])) {
			$this->manual=$params["manual"];
		}
		$this->cashGUID=md5("educhislen_".$this->edulevelID."_".$this->russ);
	}
	function sortArray(&$a) { 
		foreach( $a as &$b){
			foreach( $b as &$c){
				ksort($c["data"]);
			}
		}
		  return $a;
	}
	private function generateItems($listLevelId=0){
	$sectionsName=array(
			3=>"Высшее образование - программы бакалавриата",
			2=>"Высшее образование - программы специалитета",
			6=>"Высшее образование - программы ординатуры",
			5=>"Высшее образование - программы подготовки научных и научно-педагогических кадров в аспирантуре (адъюнктуре)",
			4=>"Высшее образование - программы магистратуры",
			7=>"НПО",
			8=>"Среднее профессиональное образование - программы подготовки специалистов среднего звена"
		);
			if($listLevelId==0) $listLevelId=181;
			if(CModule::IncludeModule("iblock")){ 
				
				$arVacantOpp=array();
				$arFilter=array("IBLOCKID"=>6,"SECTION_ID"=>$listLevelId,"ACTIVE"=>"Y");
					$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,array("ID","NAME","PROPERTY_EDUCODE","PROPERTY_PROFILE","IBLOCK_SECTION_ID"));
					while($ar_res = $res->Fetch())
					{
						$arVacantOpp[intval($ar_res["ID"])]=array(
							"name"=>$ar_res["NAME"],
							"eduCode"=>$ar_res["PROPERTY_EDUCODE_VALUE"],
							"eduLevel"=>$sectionsName[$ar_res["IBLOCK_SECTION_ID"]],
							"eduProf"=>$ar_res["PROPERTY_PROFILE_VALUE"],
						);
					}
				
			}
		
			$this->arVacant=array();
			$sql="select * from `stud_vacant` where trim(UF_LEVEL) =\"".$sectionsName[$listLevelId]."\"";
			$rez=$this->BD->query($sql);
			while ($rec=$rez->fetch()){
					if(isset($arVacantOpp[$rec["UF_OPP"]]))
					$this->arVacant[$rec["ID"]]=array(
						"rowId"=>$rec["ID"],
						"oppID"=>intval($rec["UF_OPP"]),
						"eduCode"=>$arVacantOpp[$rec["UF_OPP"]]["eduCode"],
						"eduName"=>$arVacantOpp[$rec["UF_OPP"]]["name"],
						"eduLevel"=>$arVacantOpp[$rec["UF_OPP"]]["eduLevel"],
						"eduProf"=>$arVacantOpp[$rec["UF_OPP"]]["eduProf"],
						"eduCourse"=>$rec["UF_COURSE"],
						"eduForm"=>($rec["UF_FORM"]!="")?$rec["UF_FORM"]:"Очная",
						"numberBFVacant"=>$rec["UF_FB"],
						"numberBRVacant"=>$rec["UF_BR"],
						"numberBMVacant"=>$rec["UF_BM"],
						"numberPVacant"=>$rec["UF_P"],
					);
			}
		//echo "<pre>";print_r($arVacantOpp);echo "</pre>";
			foreach ($arVacantOpp as $oppID=>$opp){
				$xflag=false;
				foreach($this->arVacant as $key=>$item){ 
					if ($oppID && intval($item["oppID"])==intval($oppID)) $xflag=true;
		
				}
		
				//программы нет в таблице
				if(!$xflag){
					
					$fields=array(
						"UF_OPP"=>$oppID, 
						"UF_PROF"=>$opp["eduProf"],
						"UF_NAME"=>$opp["name"]."_1",
						"UF_CODE"=>$opp["eduCode"],
						"UF_LEVEL"=>$opp["eduLevel"],
						"UF_FORM"=>"Очная",
						"UF_COURSE"=>1,
						"UF_BM"=>0,"UF_BR"=>0,"UF_P"=>0,"UF_FB"=>0
						);
					if($newelement=$this->BD->add("stud_vacant",$fields)){
						$this->arVacant[$newelement]=array(
							"rowId"=>$newelement,
							"oppID"=>$oppID,
							"eduCode"=>$opp["eduCode"],
							"eduName"=>$opp["name"],
							"eduLevel"=>$opp["eduLevel"],
							"eduProf"=>$opp["eduProf"],
							"eduCourse"=>1,
							"eduForm"=>"Очная",
							"numberBFVacant"=>0,
							"numberBRVacant"=>0,
							"numberBMVacant"=>0,
							"numberPVacant"=>0,
						);
					}
					$xflag=false;
				}		
					
					
			}
			
	}
	
function gethtmlevel($levelName,$levelId){
	$this->generateItems($levelId);
	$sectionsName2=array(
			3=>"Высшее образование - программы бакалавриата",
			2=>"Высшее образование - программы специалитета",
			6=>"Высшее образование - программы ординатуры",
			5=>"Высшее образование - программы подготовки научных и научно-педагогических кадров в аспирантуре (адъюнктуре)",
			4=>"Высшее образование - программы магистратуры",
			7=>"НПО",
			8=>"Среднее профессиональное образование - программы подготовки специалистов среднего звена"
		);
	$html='<h3>'.$sectionsName2[$levelId].'</h3>';
	$html.='<table class="eduVacant"><thead><tr class="spheadR0"><th colspan="9"> </th></tr>';
	$html.='<tr class="spheadR2" >';
	$html.='<th>Код, шифр группы научных специальностей</th>';
	$html.='<th>Наименование профессии, специальности, направления подготовки, наименование группы научных специальностей</th>';
	$html.='<th>Уровень образования</th>';
	$html.='<th>Образовательная программа, направленность, профиль, шифр и наименование научной специальности</th>';
	$html.='<th>Курс</th>';
	$html.='<th>Форма обучения (очная/заочная/очно-заочная)</th>';
	$html.='<th>Количество мест за счёт бюджетных ассигнований федерального бюджета</th>';
	$html.='<th>Количество мест за счёт бюджетных ассигнований бюджетов субъекта Российской Федерации</th>';
	$html.='<th>Количество мест за счёт  бюджетных ассигнований местных бюджетов</th>';
	$html.='<th>Количество мест за счёт средств физических и (или) юридических лиц</th>';
	$html.='</tr></thead><tbody>';
		
		if(count($this->arVacant)==0){
					$html.="<tr id=\"vacant_0\" itemprop=\"vacant\" class=\"maindocselementHB\"  data-id=\"0\" data-iblock=\"15\" title=\"???\">";
					$html.="<td itemprop=\"eduCode\">".$this::emptyCell."</td>";
					$html.="<td itemprop=\"eduName\">".$this::emptyCell."</td>";
					$html.="<td itemprop=\"eduLevel\">".$this::emptyCell."</td>";
					$html.="<td itemprop=\"eduProf\">".$this::emptyCell."</td>";
					$html.="<td itemprop=\"eduCourse\">".$this::emptyCell."</td>";
					$html.="<td itemprop=\"eduForm\">".$this::emptyCell."</td>";
					$html.="<td itemprop=\"numberBFVacant\">-</td>";
					$html.="<td itemprop=\"numberBRVacant\">-</td>";
					$html.="<td itemprop=\"numberBMVacant\">-</td>";
					$html.="<td itemprop=\"numberPVacant\">-</td>";
					$html.="</tr>";
		}
		
	foreach($this->arVacant as $id=>$item){
		if($item['eduProf']=="") {$item['eduProf']="отсутствует";}
		$html.="<tr itemprop=\"vacant\" class=\"maindocselementHB\" id=\"vacant".$id."\" data-id=\"".$id."\" data-opp=\"".$item['oppID']."\" data-iblock=\"15\" title=\"".$item['eduName']."\">";
		$html.="<td itemprop=\"eduCode\">".$item["eduCode"]."</td>";
		$html.="<td itemprop=\"eduName\">".$item["eduName"]."</td>";
		$html.="<td itemprop=\"eduLevel\">".$sectionsName2[$levelId]."</td>";
		$html.="<td itemprop=\"eduProf\">".$item["eduProf"]."</td>";
		$html.="<td itemprop=\"eduCourse\">".$item["eduCourse"]."</td>";
		$html.="<td itemprop=\"eduForm\">".$item["eduForm"]."</td>";
		$html.="<td itemprop=\"numberBFVacant\">".$item["numberBFVacant"]."</td>";
		$html.="<td itemprop=\"numberBRVacant\">".$item["numberBRVacant"]."</td>";
		$html.="<td itemprop=\"numberBMVacant\">".$item["numberBMVacant"]."</td>";
		$html.="<td itemprop=\"numberPVacant\">".$item["numberPVacant"]."</td>";
		$html.="</tr>";
		
	}	
	return $html."</tbody></table>";
}
public function showHtml($buffer=true){
	$this->cashGUID=md5("cl-vacantALL");
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
$stylehtml0=file_get_contents(__DIR__."/cl-vacant.css");
$stylehtml='<style type="text/css">'.$stylehtml0.'</style><h2>Вакантные места для приема (перевода) обучающихся</h2><span>[по состоянию на '.date("d.m.Y").']</span><br><br>';

global $USER;
$editMode=$USER->isAdmin() && $this->isEditmode();
//строим таблицу

	$html="";//trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $editMode || $this->testmode){
	//	$this->generateItems();
		//echo "<pre>";print_r($this->arVacant);echo "</pre>";
		$html="";  
		$html.="<div >";
/*
			179=>"Бакалавриат",
			180=>"Специалитет",
			181=>"Ординатура",
			182=>"Аспирантура",
			184=>"Магистратура",
			185=>"НПО",
			186=>"CПО"
*/

		//$html.=$this->gethtmlevel("СПО",$this->arVacant["СПО"]);
		//$html.=$this->gethtmlevel("Специалитет",$this->arVacant["Специалитет"]);
		$html.=$this->gethtmlevel("Ординатура",181);
		$html.=$this->gethtmlevel("Аспирантура",182);
		//$html.=$this->gethtmlevel("Магистратура",184);
		//foreach ($this->arVacant as $levelName=>$levelSpec){}
		$html.="</div>";
		
		if(!$this->testmode) $memcache->set($this->cashGUID, $html, false, $this->cashTime);
	} 
	$memcache->close();
if($buffer) return $html; else echo $html;
}

}//class
?>