<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class internationalAccr extends  iasmuinfo{
	const iBlock=13;
	private $cashGUID;
	private $open;
	const cashTime=6000;
	private $listitems=array();

	
private function generateItems(){
	//el.PREVIEW_TEXT as invText,
	$this->listitems=array();
	$sql="SELECT ID, UF_DATE_END as dateEnd, UF_ORG_NAME as orgName, UF_EDU_NAME as eduName, UF_EDU_CODE as eduCode FROM `international_accr`";
	$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$this->listitems[$rec["ID"]]["eduName"]=($rec["eduCode"]!="")?$rec["eduCode"]:$this::emptyCell;
		$this->listitems[$rec["ID"]]["eduName"]=($rec["eduName"]!="")?$rec["eduName"]:$this::emptyCell;
		$this->listitems[$rec["ID"]]["orgName"]=($rec["orgName"]!="")?$rec["orgName"]:$this::emptyCell;
		$this->listitems[$rec["ID"]]["dateEnd"]=($rec["dateEnd"]!="")?$rec["dateEnd"]:$this::emptyCell;
		$this->listitems[$rec["ID"]]=$rec;
	}
	
}
public function setparams($params){
		
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
}
public function showHtml(){
	$this->cashGUID="internationalAccr";
	$this->css=__DIR__."/style.css";
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	$tblName="Информация о международной аккредитации";
	if ($html=="" || $html==false || $this->isEditmode()){
		$this->generateItems();
		$html="<span class=\"hidedivlink link\">{$tblName}</span><div ";
		if ($this->open==0) $html.=" style=\"display:none;\"";
		$html.="><br><br>";
		$html.="<table class=\"simpletable\"><thead><tr>";
		$html.="<th>Код</th>";
		$html.="<th>Наименование профессии, специальности, направления подготовки</th>";
		$html.="<th>Наименование аккредитующей организации</th>";
		$html.="<th>Срок действия международной аккредитации (дата окончания действия свидетельства о международной аккредитации)</th>";
		$html.="</tr></thead><tbody>";
		if (count($this->listitems)>0){
		foreach ($this->listitems as $id=>$item){
			$idlink="internationalAccr_".$id;
			$title=$item["stateName"]."...";
			$html.="<tr title=\"{$title}\" class=\"maindocselementHB\" id=\"{$idlink}\" data-id=\"{$id}\" data-iblock=\"".$this::iBlock."\" itemprop=\"internationalAccr\">";
			$html.="<td itemprop=\"eduCode\">{$item["eduCode"]}</td>";
			$html.="<td itemprop=\"eduName\">{$item["eduName"]}</td>";
			$html.="<td itemprop=\"orgName\">{$item["orgName"]}</td>";
			$html.="<td itemprop=\"dateEnd\">{$item["dateEnd"]}</td>";
			$html.="</tr>";
			}	
		}else{
			$idlink="internationalAccr_0";
			$html.="<tr title=\" \" class=\"maindocselementHB\" id=\"".$idlink."\" data-id=\"0\" data-iblock=\"".$this::iBlock."\" itemprop=\"internationalAccr\">";
			$html.="<td itemprop=\"eduCode\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"eduName\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"orgName\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"dateEnd\">".$this::emptyCell."</td>";
			$html.="</tr>";
		}
		$html.="</tbody></table></div><br>";
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	echo $html;
}
}//end class xobjects  