<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class eduPOAccred extends  iasmuinfo{
	private $cashGUID;
	const cashTime=6000;
	private $levels=array();
	private $capt;
	private $listitems=array();
	
private function generateItems(){
}
public function setparams($params){
	
	$this->cashGUID="eduPOAccred";

}

public function showHtml($buffer=true){
		$this->css=__DIR__."/style.css";
		$cnt=0;
$html=<<<HTML
	<span class="hidedivlink link" >Р