<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class resultVIPrikaz extends  iasmuinfo{
	private $cashGUID;
	private $open;
	private $eduLevelId=0;
	private $god;
	private $accepted;
	const cashTime=6000;
	private $listitems=array();
	
private function generateItems(){
	//el.PREVIEW_TEXT as invText,
	$this->listitems=array();
	$sql="SELECT * FROM `abit_prikaz_list` ";
	if (intval($this->god)==0) $this->god=date("Y");
	$where="where YEAR(UF_DATE)=".$this->god;
	$asEduLevelStr=array(876=>"Бакалавриат",877=>"Специалитет",879=>"Ординатура",878=>"Аспирантура",3743=>"СПО");
	if(intval($this->eduLevelId)>0 && in_array($this->eduLevelId,array_keys($asEduLevelStr))){
		$eduLevel=$asEduLevelStr[$this->eduLevelId];
	}elseif(intval($this->eduLevelId)==0 && in_array($this->eduLevelId,$asEduLevelStr)){
		$eduLevel=$this->eduLevelId;
	}
	$where.=" and UF_ACTIVE and UF_EDULEVEL=\"{$eduLevel}\" and not UF_GUID like \"000000000-%\"";
	if($this->accepted==1) $where.=" and UF_STATUS=\"Зачислен\"";
	else $where.="  and (UF_STATUS<>\"Зачислен\" or UF_STATUS is NULL or UF_REVOKED)";
//Echo "<!--".$sql.$where."-->";
$sql.=$where." order by UF_DATE";
	$rez=$this->BD->query($sql);
	while ($ob=$rez->fetch()){
		$rec=array();
		$rec["UF_NUMBER"]=$ob["UF_NUMBER"];
		$rec["UF_FILE"]=intval($ob["UF_FILE"]);
		$rec["UF_DATE"]=(string)$ob["UF_DATE"]->format("d.m.Y");
		$rec["ID"]=$ob["ID"];
		$rec["GUID"]=$ob["GUID"];
		$rec["TEXT_LINK"]=$ob["UF_TEXT_LINK"];
		$rec["REVOKED"]=$ob["UF_REVOKED"];

		$this->listitems[]=(array)$rec;
	}
}
public function setparams($params){
		
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->eduLevelId=0;
		$accepted=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		$this->cashGUID="resultVI_".$this->eduLevelId;
		if(isset($params["god"])) $this->god=intval($params["god"]); else $this->god=date("Y");
		if(isset($params["accepted"])) $this->accepted=intval($params["accepted"]);

		$this->cashGUID="resultVIPrikaz".$this->god."_".$this->accepted."_".$this->eduLevelId;
		
}
public function showHtml($buffer=false){

	$this->css=__DIR__."/style.css";
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $this->isEditmode()){
		$this->listitems=array();
		$this->generateItems();
		$dataurl=urlencode($_SERVER['REQUEST_URI']);
		

		$oldsort="";$html="<div data-url=\"{$dataurl}\" data-iblock=\"63\" data-width=\"600\" data-height=\"300\">";


		foreach ($this->listitems as $sortid=>$item){
			$id=$item["ID"];
			$idlink=$item["GUID"];
			$xdate=$item["UF_DATE"];
			$title="Приказ №".$item["UF_NUMBER"]." от ".$xdate;
			$textLink=$title;
			if($item["TEXT_LINK"]!="") 
				$textLink=$item["TEXT_LINK"]; 
			
			$fileSrc="";
			if($item["UF_FILE"]!=0) $fileSrc=CFILE::GetPath($item["UF_FILE"]);
			
			$html.="<p title=\"{$title}\" class=\"maindocselementHB\" data-nodel=\"Y\" id=\"{$idlink}\" data-id=\"{$id}\"  itemprop=\"resultPrikaz\" >";
			if($fileSrc!=""){
				$html.="<a href=\"{$fileSrc}\">{$textLink}</a>";
			} else $html.="<span>{$title}</span>";
				$html.="</p>";
			
		}	
		$html.="</div>";
		
		if(count($this->listitems)==0){
			$html="<p itemprop=\"resultPrikaz\"> В настоящее время приказы отсутствуют</p>";
		}		
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($buffer) return $html; else echo $html;
}
}//end class xobjects  