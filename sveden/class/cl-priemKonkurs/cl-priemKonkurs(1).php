<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemKonkurs extends  iasmuinfo{
	private $cashTime=600;
	
	
	
	

	private $listitems=array();
	private $listitemsTemp=array();
	private $eduLevelId=877;
	private $modyfyData=0;
	private $cashGUID;
	private $open;
	private $god;
	private $orgLevel;
	private $orgName;
	private $countZayav=0;
	function setparams($params){
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
		$this->eduLevelId=877;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		if(isset($params["god"])) $this->god=intval($params["god"]); else $this->god=date("Y");
	
	}
	private function getlevelName($levelID){
		if($levelID==877) return "РЎРїРµС†РёР°Р»РёС‚РµС‚";
		elseif ($levelID==878) return "РђСЃРїРёСЂР°РЅС‚СѓСЂР°";
		elseif ($levelID==879) return "РћСЂРґРёРЅР°С‚СѓСЂР°";
		else return "РЎРїРµС†РёР°Р»РёС‚РµС‚";
	}
	private function getZayavData($level){
$this->orgLevel=array(
		"000007144"=>1,
		"000009490"=>2,
		"000006028"=>2,
		"000012671"=>2,
		"000002890"=>2,
		"000000248"=>2,
		"000014037"=>3,
		"000002009"=>3,
		"000000244"=>3,
		"000007760"=>3,
		"000000245"=>4,
		"000013826"=>5,
		"000000020"=>6,
		"000000000"=>0,
	);
$this->orgName=array(
		"000007144"=>"РђР»С‚Р°Р№СЃРєРёР№ РєСЂР°Р№",
		"000009490"=>"Р РµСЃРїСѓР±Р»РёРєР° РђР»С‚Р°Р№",
		"000006028"=>"Р РµСЃРїСѓР±Р»РёРєР° РђР»С‚Р°Р№",
		"000012671"=>"Р РµСЃРїСѓР±Р»РёРєР° РўС‹РІР°",
		"000002890"=>"Р РµСЃРїСѓР±Р»РёРєР° РҐР°РєР°СЃРёСЏ",
		"000000248"=>"Р РµСЃРїСѓР±Р»РёРєР° Р‘СѓСЂСЏС‚РёСЏ",
		"000014037"=>"РћРђРћ Р Р–Р”",
		"000002009"=>"РћРђРћ Р Р–Р”",
		"000000244"=>"Р¤РњР‘Рђ Р РѕСЃСЃРёРё",
		"000007760"=>"Р¤РЎР