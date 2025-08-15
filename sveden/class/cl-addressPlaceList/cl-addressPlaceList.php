<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class addressPlaceList extends  iasmuinfo{
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
be.PROPERTY_109 as fullname, 
be.PROPERTY_111 as address, 
be.PROPERTY_113 as suruse,
ben.XML_ID as itemprop,
ben.VALUE as itemname,
be.PROPERTY_114 as surface,
case
 when be.PROPERTY_108=31 then "Собственность, аренда"
 when be.PROPERTY_108=3259 then "Договор практики"
end as obtype,
f.SUBDIR as fdir,
f.FILE_NAME as fname
FROM `b_iblock_element_prop_s9` be 
LEFT JOIN `b_iblock_element` el on el.ID=be.IBLOCK_ELEMENT_ID 
LEFT JOIN `b_iblock_element_prop_m9` bem on el.ID=bem.IBLOCK_ELEMENT_ID and bem.IBLOCK_PROPERTY_ID=113
LEFT JOIN `b_iblock_property_enum` ben on ben.ID=bem.VALUE_ENUM
LEFT JOIN `b_file` f on be.PROPERTY_115=f.ID
where el.ACTIVE="Y" 
SQL;
if($this->xml!="") $sql.=" and ben.XML_ID=\"{$this->xml}\"";
$sql.=" order by el.SORT ";
$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$recname=trim($rec["fullname"]);
		if($recname=="") $recname=$rec["name"];
		$item=array(
			"name"=>$recname,
			"fname"=>"",
			"id"=>intval($rec["id"]),
			"address"=>$rec["address"],
			"use"=>$rec["suruse"],
			"surface"=>$rec["surface"],
			"sort"=>$rec["sort"],
			"itemprop"=>$rec["itemprop"]
		);
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

	$this->cashGUID="addressPlaceList_".$this->xml;

}

public function showHtml($buffer=true){
	//$memcache = new Memcache;
	//$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html="";//trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $this->isEditmode()){
		$this->generateItems();
		$this->css=__DIR__."/style.css";
		$cnt=0;
		
		$html.="<table class=\"simpletable\"><thead><tr><th>№</th><th>Адрес места осуществления образовательной деятельности</th>";
		$html.="</tr></thead><tbody>";
		if(count($this->listitems)==0){
			$idlink=$this->xml;
			$eltitle=$this::emptyCell;
			$html.="<tr itemprop=\"".$this->xml."\" class=\"maindocselement\" id=\"{$idlink}\" data-id=\"0\" data-iblock=\"9\" title=\"{$eltitle}\" >";
			$html.="<td colspan=\"2\">".$this::emptyCell."</td>";
			$html.="</tr>";
		}
		foreach($this->listitems as $element){
			$cnt++;
			$idlink=$this->xml."_".$element["id"];
			$eltitle=str_replace('"','&quot;',htmlspecialcharsEx($element["name"]));
			$html.="<tr itemprop=\"".$element["itemprop"]."\" class=\"maindocselement\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"9\" title=\"{$eltitle}\" >";
			$html.="<td>{$cnt}</td><td title=\"".$element["name"]."\">{$element["address"]}</td>";
			$html.="</tr>";
		}
		$html.="</tbody></table>";
		//$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	} 
		
	//$memcache->close();	
/* не кешируемая часть */
		 if($buffer) return $html; else echo $html; 
/*--------------------*/		
}
}//end class addressPlace 