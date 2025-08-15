<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class addressPlace extends  iasmuinfo{
	private $cashGUID;
	private $hideCaption=false;
	const cashTime=6000;
	private $tableNP;
	private $capt;
	private $xml;
	private $listitems=array();

private function generateItems(){
$sql=<<<SQL
SELECT 
el.id as id,
el.SORT as sort,
el.name as name, 
be.PROPERTY_495 as address, 
be.PROPERTY_496 as suruse,
ben.XML_ID as itemprop,
ben.VALUE as itemname,
be.PROPERTY_497 as surface,
case
 when be.PROPERTY_493=258 then "Собственность, аренда"
 when be.PROPERTY_493=259 then "Договор практики"
end as obtype,
f.SUBDIR as fdir,
f.FILE_NAME as fname
FROM `b_iblock_element_prop_s66` be 
LEFT JOIN `b_iblock_element` el on el.ID=be.IBLOCK_ELEMENT_ID 
LEFT JOIN `b_iblock_element_prop_m66` bem on el.ID=bem.IBLOCK_ELEMENT_ID and bem.IBLOCK_PROPERTY_ID=496
LEFT JOIN `b_iblock_property_enum` ben on ben.ID=bem.VALUE_ENUM
LEFT JOIN `b_file` f on be.PROPERTY_498=f.ID
where el.ACTIVE="Y" 
SQL;
if($this->tableNP!="") $sql.=" and be.PROPERTY_614=\"{$this->tableNP}\"";
	if($this->xml!="") $sql.=" and ben.XML_ID=\"{$this->xml}\"";
$sql.=" order by el.SORT ";
$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$item=array("name"=>$rec["name"],"fname"=>"","id"=>intval($rec["id"]),"address"=>$rec["address"],"use"=>$rec["suruse"],"surface"=>$rec["surface"],"sort"=>$rec["sort"]);
		if ($rec["fname"]!=""){
			if ($rec["SUBDIR"]!=""){
				$item["file"]="/upload/{$rec["SUBDIR"]}/{$rec["fname"]}";
			} else {
				$item["file"]="/upload/{$rec["fname"]}";
			}	
			$item["fname"]=$rec["fname"];
		}		
		$this->listitems[$item["id"]]=$item;
	}
}
public function setparams($params){
	$this->xml="";
	if (isset($params["hideCaption"])) $this->hideCaption=true;
	if (isset($params["itemprop"])) $this->xml=$params["itemprop"];
	if (is_set($params["typeTbl"])){
		if ($typeTbl==0) {
			$this->tableNP=259;//26318
			$this->capt="Наличие у образовательной организации на праве собственности или ином законном основании зданий, строений, сооружений, территорий, необходимых для осуществления образовательной деятельности";
		}elseif ($typeTbl==1) {
			$this->tableNP=259;
			$this->capt="Перечень медицинских организаций, организаций осуществляющих производство лекарственных средств, аптечных организаций, судебно-экспертных учреждений, иных организаций, осуществляющих деятельность в сфере охраны здоровья, с которыми заключены договоры о практической подготовке обучающихся"; 
		}else{
			$this->tableNP="";
			//$this->capt="Сведения о каждом месте осуществления образовательной деятельности, в том числе не указываемых в соответствии с частью 4 статьи 91 Федерального закона от 29.12.2012 N 273-ФЗ"; 
			$this->capt="О местах осуществления образовательной деятельности, в том числе не указываемых в приложении к лицензии"; 
		}
	}else{
			$this->tableNP="";
			$this->capt="О местах осуществления образовательной деятельности, в том числе не указываемых в приложении к лицензии"; 
	}
	$this->cashGUID="addressPlace_".$this->tableNP;

}

public function showHtml($buffer=true){
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $this->isEditmode()){
		$this->generateItems();
		$this->css=__DIR__."/style.css";
		$cnt=0;
		
		$html="";
		if(!$this->hideCaption) {	$html.="<p title=\"{$this->capt}\" class=\"texticon hidedivlink link\" >{$this->capt}</p>\r\n";
					$html.="<div style=\"display:none;\" itemprop=\"addressPlace\">";
		} else{
					$html.="<div itemprop=\"addressPlace\">";
		}
		if($this->tableNP!="") 		$html.="<table class=\"simpletable\"><thead><tr><th>№</th><th>Наименование объекта</th><th>Адрес объекта</th><th>Назначение объекта</th><th>Площадь м<sup>2</sup></th>";
				else 		$html.="<table class=\"simpletable\"><thead><tr><th>№</th><th>Адрес места осуществления образовательной деятельности</th>";

		$html.="</tr></thead><tbody>";
		if(count($this->listitems)==0){
			$idlink="addressPlace_0";
			$eltitle=$this::emptyCell;
			$html.="<tr class=\"maindocselement\" id=\"{$idlink}\" data-id=\"0\" data-iblock=\"66\" title=\"{$eltitle}\" >";
			if($this->tableNP!="") $html.="<td>{$cnt}</td><td>{$eltitle}</td> <td>".$this::emptyCell."</td><td>".$this::emptyCell."</td><td>".$this::emptyCell."</td>";
					else $html.="<td>{$cnt}</td><td>".$this::emptyCell."</td>";
			$html.="</tr>";
		}
		foreach($this->listitems as $element){
			$cnt++;
			$idlink="addressPlace_".$element["id"];
			$eltitle=htmlspecialcharsEx($element["name"]);
			$html.="<tr class=\"maindocselement\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"66\" title=\"{$eltitle}\" >";
			if($this->tableNP!="") $html.="<td>{$cnt}</td><td>{$eltitle}</td> <td>{$element["address"]}</td><td>{$element["use"]}</td><td>{$element["surface"]}</td>";
					else $html.="<td>{$cnt}</td><td>{$element["address"]}</td>";
			$html.="</tr>";
		}
		$html.="</tbody></table></div>";
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	} 
		
	$memcache->close();	
/* не кешируемая часть */
		 if($buffer) return $html; else echo $html; 
/*--------------------*/		
}
}//end class addressPlace 