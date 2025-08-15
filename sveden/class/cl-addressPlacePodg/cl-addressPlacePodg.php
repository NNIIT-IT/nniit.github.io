<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//require_once(__DIR__."/../asmuinfo/asmuinfo.php");
require_once(__DIR__."/../cl-purposeCab/cl-purposeCab.php");
use asmuinfoclasses;
class addressPlacePodg extends purposeCab{

public function getHtml($buffer=true){
	$this->activeTableID=3;
	$this->css=__DIR__."/style.css";
	$this->cashGUID="addressPlacePodg_".$this->activeTableID;
	$this->listitems=array();
	$arPropIDsKey=array_keys($arPropIDs);
	$this->activeTable=$this->arTables[$this->activeTableID];

	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $this->isEditmode()){
		
	
		$this->generateItems();
		$html="";
		$addr=array();
		foreach ($this->listitems as $addressGUID=>$addressOb){
			$addr[]=$addressOb["name"];
		}
		$addr=array_filter($addr);
		foreach ($addr as $address){
				$html.="<div><div>".$address."</div></div>";
		}
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($buffer) return $html; else echo $html;
}
}//end class cl-addressPlacePodg