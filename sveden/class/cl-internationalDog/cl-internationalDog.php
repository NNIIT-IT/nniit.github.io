<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class internationalDog extends  iasmuinfo{
	const iblock=9;
	private $cashGUID;
	private $open;
	const cashTime=600;
	private $listitems=array();
	private $hideCaption;
private function generateItems(){
	//el.PREVIEW_TEXT as invText,
	$this->listitems=array();
	$sql="SELECT id, TRIM(UF_STATE_NAME) as stateName, UF_ORG_NAME as orgName,UF_DOG_REG as dogReg, UF_MFILE as zakFile, UF_FILE_NAME as zakFileName   FROM `international_dog`";
	$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$sortid=preg_replace('/[^a-zA-ZА-Яа-я0-9\s]/',"",$rec["stateName"]).$rec["id"];
		if(mb_strtoupper($rec["stateName"])=="РОССИЯ") $sortid="1".$sortid; else $sortid="0".$sortid;
		$this->listitems[$sortid]["stateName"]=($rec["stateName"]!="")?$rec["stateName"]:$this::emptyCell;
		$this->listitems[$sortid]["orgName"]=($rec["orgName"]!="")?$rec["orgName"]:$this::emptyCell;
		$this->listitems[$sortid]["dogReg"]=($rec["dogReg"]!="")?$rec["dogReg"]:$this::emptyCell;
			

		$this->listitems[$sortid]=$rec;
	}
	ksort($this->listitems);
}
public function setparams($params){
		
		$this->open=0;
		$this->hideCaption=false;
		if(isset($params["open"])) $this->open=1;
		$this->hideCaption=(isset($params["hideCaption"]));

}
public function showHtml($buffer=false){
	$this->cashGUID="internationalDog";
	$this->css=__DIR__."/style.css";
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	$tblName="Информация о заключенных и планируемых к заключению договорах с иностранными и (или) международными организациями по вопросам образования и науки";
	if ($html=="" || $html==false || $this->isEditmode()){

		$this->generateItems();
		if(!$this->hideCaption){
			$html="<span class=\"hidedivlink link\">{$tblName}</span><div ";
			if ($this->open==0) $html.=" style=\"display:none;\"";
			$html.="><br><br>";
		}else{
			$html="";
		}
		$html.="<table class=\"simpletable\"><thead><tr>";
		$html.="<th>Название государства</th><th>Наименование организации</th>";
		$html.="<th>Реквизиты договора (наименование, дата, номер, срок действия)</th>";
		//$html.="<th>Заключение Минздрава России</th>";
		$html.="</tr></thead><tbody>";
		if (count($this->listitems)>0){
		$oldsort="";
		foreach ($this->listitems as $sortid=>$item){
			$sortid1=mb_substr($sortid,0,1);
			if($oldsort=="") $oldsort=$sortid1;
			if($oldsort!=$sortid1){
				 $oldsort=$sortid1;
				$html.="<tr><td colspan=\"3\"></td></tr>";
			}

			$id=$item["id"];
			$idlink="internationalDog_".$id;
			$title=$item["stateName"]."...";
			$sortids=base64_encode($sortid);
			$html.="<tr title=\"{$title}\" data-sort=\"{$sortids}\" class=\"maindocselementHB\" id=\"{$idlink}\" data-id=\"{$id}\" data-iblock=\"".$this::iblock."\" itemprop=\"internationalDog\">";

			$html.="<td itemprop=\"stateName\">{$item["stateName"]}</td>";
			$html.="<td itemprop=\"orgName\">{$item["orgName"]}</td>";
			$html.="<td itemprop=\"dogReg\">{$item["dogReg"]}</td>";
			
			$html.="</tr>";
			}	
		}else{
			$idlink="internationalDog_0";
			$html.="<tr title=\"Новая запись\" class=\"maindocselementHB\" id=\"{$idlink}\" data-iblock=\"".$this::iblock."\" itemprop=\"internationalDog\">";
			$html.="<td itemprop=\"stateName\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"orgName\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"dogReg\"><a class=\"link\" href=\"#\">".$this::emptyCell."</a></td>";
			$html.="</tr>";
		}
		$html.="</tbody></table></div><br>";
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($buffer) return $html; else echo $html;
}
}//end class xobjects  