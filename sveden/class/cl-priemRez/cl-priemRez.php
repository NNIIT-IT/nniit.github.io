<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
/*		require_once($_SERVER['DOCUMENT_ROOT'].'/dompdf8/autoload.inc.php');
		require_once($_SERVER["DOCUMENT_ROOT"]."/local/common/class/pdfsign/pdfsign.php");
		use Dompdf\Dompdf;
		use Dompdf\Options;
*/
use asmuinfoclasses;
/*необходимо скорректировать на численность вакантных мест*/
class priemRez extends  iasmuinfo{
	private $edulevelID;
	
	private $listitems=array();
	const cashTime=342000;
	const documentfolder="/sveden/education/priem/";
	const emptyCell="Отсутствует";
	private $cashGUID;
	private $htmlStyle;
	private $currentYear;
	private $startYear;
	
	public function setparams($params){
		$this->edulevelID=0;
	
		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);
		}
		$this->htmlStyle=file_get_contents(__DIR__."/cl-priemRez.css");
		$this->currentYear=intval(date("Y"));
		$this->startYear=intval(date("Y"));
		if(intval(date("m"))<6) $this->startYear--;
		$this->cashGUID=md5("eduPriem_".$this->edulevelID."_".$this->currentYear."_".$this->startYear);
	}
	private function generateItems(){
		$this->listitems=array();
		$listitemsTmp=array();
		//получение списка ОП
		if(CModule::IncludeModule("iblock")){ 
			$arFilter=array("IBLOCKID"=>59,"ID"=>implode("|",$oppListData),"ACTIVE"=>"Y");
			$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,array("ID","NAME","PROPERTY_EDUCODE","IBLOCK_SECTION_ID","PROPERTY_SROKOBUCH","PROPERTY_PROFILE" ));
			while($ar_res = $res->Fetch()){
				
				$srokObuch=$ar_res["PROPERTY_SROKOBUCH_VALUE"];
				if(intval(date("m"))>6) $yearX=intval(date("Y")); else $yearX=intval(date("Y")-1);
				$item=array(
					"eduName"=>htmlspecialcharsBX($ar_res["NAME"])." ".htmlspecialcharsBX($ar_res["PROPERTY_PROFILE_VALUE"]),
					"eduCode"=>$ar_res["PROPERTY_EDUCODE_VALUE"],
					"eduLevel"=>$this::sectionsName[$ar_res["IBLOCK_SECTION_ID"]],
					"eduForm"=>"",
					"numberBFpriem"=>0,
					"numberBRpriem"=>0,
					"numberBMpriem"=>0,
					"numberPpriem"=>0,
					"score"=>0,
					"id"=>0,
					"add"=>true,
					"opp"=>$ar_res["ID"],
					"year"=>$yearX
				);
				
				if($srokObuch["s1"]!=""){
					$listitemsTmp[$ar_res["ID"]]["Очная"]=$item;
					$listitemsTmp[$ar_res["ID"]]["Очная"]["eduForm"]="Очная";
				}
				if($srokObuch["s2"]!=""){
					$listitemsTmp[$ar_res["ID"]]["Заочная"]=$item;
					$listitemsTmp[$ar_res["ID"]]["Очная"]["eduForm"]="Заочная";
				}
				if($srokObuch["s3"]!=""){
					$listitemsTmp[$ar_res["ID"]]["Очно-заочная"]=$item;
					$listitemsTmp[$ar_res["ID"]]["Очная"]["eduForm"]="Очно-заочная";
				}
			}			
				
		}//iblock

		$sql="SELECT * FROM rez_priem WHERE UF_GOD>=".($this->startYear-3)." ORDER BY UF_OPP";
			$ob=$this->BD->query($sql);
			while ($arItem=$ob->fetch()){
				$item=array();
				$form=$arItem["UF_FORM"];
				if(!in_array($form,array("Очная","Заочная","Очно-заочная"))){
					$form="Очная";
				}
				$idOpp=intval($arItem["UF_OPP"]);
				if($idOpp>0 && in_array($idOpp,array_keys($listitemsTmp))){
					$listitemsTmp[$idOpp][$form]["numberBFpriem"]=intval($arItem["UF_BF"]);
					$listitemsTmp[$idOpp][$form]["numberBFpriem"]=intval($arItem["UF_BF"]);
					$listitemsTmp[$idOpp][$form]["numberBRpriem"]=intval($arItem["UF_BR"]);
					$listitemsTmp[$idOpp][$form]["numberBMpriem"]=intval($arItem["UF_BM"]);
					$listitemsTmp[$idOpp][$form]["numberPpriem"]=intval($arItem["UF_P"]);
					$listitemsTmp[$idOpp][$form]["score"]=$arItem["UF_S"];
					$listitemsTmp[$idOpp][$form]["year"]=intval($arItem["UF_GOD"]);
					$listitemsTmp[$idOpp][$form]["id"]=intval($arItem["ID"]);
					
				}	
			}
			foreach ($listitemsTmp as $oppID=>$forms){
				$xform=array();
				if(is_array($forms["Очная"])) $xform=$forms["Очная"];
				elseif(is_array($forms["Заочная"])) $xform=$forms["Заочная"];
				elseif(is_array($forms["Очно-заочная"])) $xform=$forms["Очно-заочная"];
				$xform["numberBFpriem"]="-";
				$xform["numberBRpriem"]="-";
				$xform["numberBMpriem"]="-";
				$xform["numberPpriem"]="-";
				$xform["score"]='-';
				$xform["id"]="-";
				$xform["add"]=false;

				if(!is_array($forms["Очная"])){$listitemsTmp[$oppID]["Очная"]=$xform;$listitemsTmp[$oppID]["Очная"]["eduForm"]="Очная";}
				if(!is_array($forms["Заочная"])){$listitemsTmp[$oppID]["Заочная"]=$xform;$listitemsTmp[$oppID]["Заочная"]["eduForm"]="Заочная";}
				if(!is_array($forms["Очно-заочная"])){$listitemsTmp[$oppID]["Очно-заочная"]=$xform;$listitemsTmp[$oppID]["Очно-заочная"]["eduForm"]="Очно-заочная";}
			}
			foreach ($listitemsTmp as $oppID=>$forms){
			
				foreach($forms as $form=>$score){
					if($score["eduName"]!="")
						$this->listitems[$score["year"]][$oppID][$form]=$score;
				}
			}
			//echo "<pre>";print_r($this->listitems);echo"</pre>";
}

public function showHtml($buffer=true){
	
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$htmlFinal=trim($memcache->get($this->cashGUID));
	if ($htmlFinal=="" || $htmlFinal==false || $this->isEditmode()){
		$this->generateItems();
$htmlHeader=<<<htmlHeader
<table class="priemrez"><thead>
	<tr>
	  <td rowspan="2">Код</td>
	  <td rowspan="2">Наименование специальности, направления подготовки, научной специальности</td>
	  <td rowspan="2">Уровнь образования</td>
	  <td rowspan="2">Форма обучения</td>
	  <td colspan="4">Результаты приема обучающихся (количество человек)</td>
	  <td rowspan="2">Средняя сумма набранных баллов по всем вступительным испытаниям</td>
	</tr>
	<tr>
	  <td>за счёт бюджетных ассигнований федерального бюджета</td>
	  <td>за счёт бюджетов субъектов Российской Федерации</td>
	  <td>за счёт местных бюджетов</td>
	  <td>за счёт средств физических и (или) юридических лиц</td>
	</tr></thead><tbody>
htmlHeader;
				
				/*генерация файлов о результатах приема*/
				$docs=array();
				$arTablesHtml=array();
				$activGod=0;
				if(count($this->listitems)==0){
					$god=date("Y");
					$arTablesHtml[$god]=$htmlHeader;
					$arTablesHtml[$god].='<tr id="rezprkom'.$arItem["ID"].'" itemprop="eduPriem" class="maindocselementHB" id="row0" data-id="0" data-iblock="9" title=" ">'."\r\n";
					$arTablesHtml[$god].='<td itemprop="eduCode">'.$this::emptyCell.'</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="eduName">'.$this::emptyCell.'</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="eduLevel" style="text-align: center;">'.$this::emptyCell.'</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="eduForm" style="text-align: center;">'.$this::emptyCell.'</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="numberBF" style="text-align: center;">0</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="numberBR" style="text-align: center;">0</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="numberBM" style="text-align: center;">0</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="numberP" style="text-align: center;">0</td>'."\r\n";
					$arTablesHtml[$god].='<td itemprop="score" style="text-align: center;"> 0</td>'."\r\n";
					$arTablesHtml[$god].='</tr>'."\r\n";
					$arTablesHtml[$god].="</tbody></table><br>";
				}
				foreach($this->listitems as $god=>$listitems){
					$arTablesHtml[$god]=$htmlHeader;
					$activGod=max($god,$activGod);
					foreach($listitems as $oppId=>$forms){
						$keyX=0;
						foreach($forms as $form=>$arItem){
							$keyX++;
							//print_r($arItem);
							$xform=$arItem['eduForm'];
							$addClass=($xform!="Очная")?" hide ":"  ";
							$addAttr= ($xform=="Очная")?" rowspan=\"3\" ":"";
							$addItemClass=($arItem["add"])?"maindocselementHB":"";
							$arItemName=$arItem["eduCode"]." ".$arItem["eduName"];
							
							$arTablesHtml[$god].="<tr  itemprop=\"eduPriem\" >\r\n";
							$arTablesHtml[$god].="<td itemprop=\"eduCode\" {$addAttr}  class=\"{$addClass}\" >{$arItem['eduCode']} </td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"eduName\" {$addAttr}  class=\"{$addClass}\" >{$arItem['eduName']} </td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"eduLevel\" {$addAttr} class=\"{$addClass}\" >{$arItem['eduLevel']} </td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"eduForm\" ";
							
							$arTablesHtml[$god].=" class=\"{$addItemClass}\" id=\"rezprkom{$oppId}_{$keyX}\" ";
							$arTablesHtml[$god].=" data-UF_OPP=\"{$oppId}\" data-UF_FORM=\"{$arItem['eduForm']}\" ";

							$arTablesHtml[$god].=" data-id=\"{$arItem['id']}\" data-iblock=\"9\" title=\"{$arItemName}\" ";
							$arTablesHtml[$god].=">{$arItem['eduForm']}</td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"numberBF\" >  {$arItem['numberBFpriem']}</td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"numberBR\" >{$arItem['numberBRpriem']}</td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"numberBM\" >{$arItem['numberBMpriem']}</td>\r\n";
							$arTablesHtml[$god].="<td itemprop=\"numberP\" >{$arItem['numberPpriem']}</td>\r\n";
							$score=($arItem['score']=="-")?"-":(number_format($arItem['score'],2));
							$arTablesHtml[$god].="<td itemprop=\"score\" >{$score}</td>";
							$arTablesHtml[$god].="</tr>\r\n";
						
						} 
					}	
					$arTablesHtml[$god].="</tbody></table><br>";
				} //foreach($this->listitems as $god=>$listitems)
			//$fileUrl=$this->getSignFileUrl($arTablesHtml[$activGod],$activGod,false);
			$htmlFinal="";	
			
			foreach ($arTablesHtml as $god=>$html){	$htmlFinal.="<h2>".$god." год</h2>".$html."<br>";}
			$result = $memcache->replace( $this->cashGUID, $htmlFinal);
			if( $result == false ){
				$memcache->set($this->cashGUID, $htmlFinal, false, $this->cashTime);
			} 
	}//if ($html=="" || $html==false || $this->isEditmode())
	$memcache->close();
	if($buffer) {
		return $htmlFinal; 
	}else {
		echo $htmlFinal;
	}	
}		
}