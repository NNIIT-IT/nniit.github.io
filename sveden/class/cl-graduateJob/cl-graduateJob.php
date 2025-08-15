<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class graduateJob extends  iasmuinfo{
	const cashTime=6000;
	private $cashGUID;
	private $listitems=array();
	private $yearOut;
	private $ver=1;	
	
private function generateItems(){
	//el.PREVIEW_TEXT as invText,
$sql=<<<SQL
SELECT 
mt.id as id,
mt.UF_CNT_TRUD as cntTrud,
mt.UF_YEAR_OUT as yearOut,
mt.UF_CNT_ALL as cnt,
mt.UF_EDU_PROF as eduProf,
mt.UF_SPEC_NAME as eduName,
mt.UF_SPEC_CODE as eduCode,
mt.UF_SPEC_ID as specObj
FROM `graduate_job` mt
where DATEDIFF(now(),MAKEDATE(mt.UF_YEAR_OUT,92))/30.42<48
SQL;
	$rez=$this->BD->query($sql);


	while ($rec=$rez->fetch()){
		$levelName="";
		$sortIdx=0;
		$spcode=trim($rec["eduCode"]);
		//echo mb_substr($spcode,2,4)." ";
		if(mb_substr($spcode,2,4)==".08."){
			$levelName="Высшее образование - программы ординатуры";
			$sortIdx=5;
		}
		if(mb_substr($spcode,2,4)==".03."){
			$levelName="Высшее образование - бакалавриат";
			$sortIdx=2;
		}
		if(mb_substr($spcode,2,4)==".02."){
			$levelName="Среднее профессиональное образование - программы подготовки специалистов среднего звена";
			$sortIdx=1;
		}
		if(mb_substr($spcode,2,4)==".05."){
			$levelName="Высшее образование - программы специалитета";
			$sortIdx=3;
		}
		if(mb_substr($spcode,2,4)==".04."){
			$levelName="Высшее образование - программы магистратуры";
			$sortIdx=4;
		}
		if(mb_substr($spcode,0,3)=="3.1"){
			$levelName="Высшее образование - программы подготовки научных и научно-педагогических кадров в аспирантуре (адъюнктуре)";
			$sortIdx=6;
		}
		$key=$sortIdx.md5($rec["eduCode"].$rec["eduName"].$rec["eduProf"]);
				$this->listitems[$key]["levelName"]=$levelName;
		if($rec["yearOut"]==$this->yearOut) {
			if(!isset($this->listitems[$rec["specCode"]]["v1"])) {
				$this->listitems[$key]["v1"]=$rec["cnt"];
				$this->listitems[$key]["t1"]=$rec["cntTrud"];
				$this->listitems[$key]["id1"]=$rec["id"];

			}	
		}
		if($rec["yearOut"]==($this->yearOut+1)) {
			if(!isset($this->listitems[$key]["v2"])) {
				$this->listitems[$key]["v2"]=$rec["cnt"];
				$this->listitems[$key]["t2"]=$rec["cntTrud"];
				$this->listitems[$key]["id2"]=$rec["id"];
			}	
		}
		if($rec["yearOut"]==($this->yearOut+2)) {
			if(!isset($this->listitems[$rec["specCode"]]["v3"])) {
				$this->listitems[$key]["v3"]=$rec["cnt"];
				$this->listitems[$key]["t3"]=$rec["cntTrud"];
				$this->listitems[$key]["id3"]=$rec["id"];
			}	
		}
		if(!isset($this->listitems[$key]["eduName"])) $this->listitems[$key]["eduName"]=$rec["eduName"];
		//$this->listitems[$key]["eduProf"]="Все";
		if(!isset($this->listitems[$key]["eduProf"]) || $this->listitems[$key]["eduProf"]=="") 
			$this->listitems[$key]["eduProf"]=$rec["eduProf"];
		if(!isset($this->listitems[$key]["eduCode"]) || $this->listitems[$key]["eduCode"]=="") 
			$this->listitems[$key]["eduCode"]=$rec["eduCode"];



	}
	ksort($this->listitems);
}
public function setparams($params){
		if(isset($params["ver"])){
			$this->ver=intval($params["ver"]);
		}
		if(isset($params["god"])){
			$this->yearOut=intval($params["god"]);//-2;
		}else{
			//if (intval(date("m"))>6) $this->yearOut=intval(date("Y"))-3;
			//if (intval(date("m"))<7) $this->yearOut=intval(date("Y"))-4;
		}
		$this->cashGUID="graduateJob_".date("Y_m");

		

}
public function showHtml($buffer=false){
	$this->css=__DIR__."/style.css";
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID.$this->ver));
	$tblName="Информация о трудоустройстве выпускников";
	if ($html=="" || $html==false || $this->isEditmode()){
		$this->generateItems();
		//echo "<div style=\"display:none\"><pre>";print_r($this->listitems);echo "</pre></div>";
		$currentLevel="";
		if($this->ver==1){
			$html="<span style=\"margin-left:-1em;\"  class=\"texticon hidedivlink link\">{$tblName}</span><div style=\"display:none;\"><br><br>";
		} else{
			$html="<div style=\"\"><br><br>";
		}
		$html.="<table class=\"simpletable\" style=\"font-size:10pt;\"><thead ><tr>";
		$html.="<th rowspan=\"2\">Код профессии, специальности, направления подготовки, научной специальности, шифр группы научных специальностей</th>";
		$html.="<th rowspan=\"2\">Наименование профессии, специальности, направления подготовки, наименование группы научных специальностей</th>";
		$html.="<th rowspan=\"2\">Образовательная программа, направленность, профиль, шифр и наименование научной специальности</th>";
		$period=(intval($this->yearOut)-1)."-".$this->yearOut." учебный год";
		$html.="<th colspan=\"2\">{$period}</th>";
		//$th1=$this->yearOut+1;		$th2=$this->yearOut+2;
		//$html.="<th colspan=\"2\">{$th1}</th>";
		//$html.="<th colspan=\"2\">{$th2}</th>";
		$html.="</tr><tr>";
		$html.="<th>Численность выпускников</th>";
		$html.="<th>Численность трудоустроенных выпускников</th>";
		//$html.="<th>Кол-во выпускников</th>";
		//$html.="<th>Кол-во трудоустроенных выпускников</th>";
		//$html.="<th>Кол-во выпускников</th>";
		//$html.="<th>Кол-во трудоустроенных выпускников</th>";
		$html.="</tr></thead><tbody>";
		$idlink0="graduateJob_0";
		$levelName="";
		if (count($this->listitems)>0){

			foreach ($this->listitems as $key=> $item){
				$id1=intval($item["id1"]);
				$id2=intval($item["id2"]);
				$id3=intval($item["id3"]);

				$v1=intval($item["v1"]);
				$v2=intval($item["v2"]);
				$v3=intval($item["v3"]);

				$v1=($v1==0)?"выпуск отсутствует":$v1;
				$v2=($v2==0)?"выпуск отсутствует":$v2;
				$v3=($v3==0)?"выпуск отсутствует":$v3;

				$t1=intval($item["t1"]);
				$t2=intval($item["t2"]);
				$t3=intval($item["t3"]);

				$t1=($t1==0)?"выпуск отсутствует":$t1;
				$t2=($t2==0)?"выпуск отсутствует":$t2;
				$t3=($t3==0)?"выпуск отсутствует":$t3;


				$idlink1=$key."job_1".$id1;
				$idlink2=$key."job_2".$id2;
				$idlink3=$key."job_3".$id3;
	
				$tt1=$item["eduName"]." ".$this->yearOut;
				$tt2=$item["eduName"]." ".($this->yearOut+1);
				$tt2=$item["eduName"]." ".($this->yearOut+2);
				if($currentLevel!=$item["levelName"]){
					$currentLevel=$item["levelName"];
					$html.="<tr><th colspan=\"5\">".$currentLevel."</th></tr>";
				}
				$html.="<tr title=\"{$title}\" itemprop=\"graduateJob\">";
				$html.="<td itemprop=\"eduCode\">{$item['eduCode']}</td>";
				$html.="<td itemprop=\"eduName\">{$item['eduName']}</td>";
				$html.="<td itemprop=\"eduProf\">{$item['eduProf']}</td>";
				$html.="<td title=\"{$tt1}\" class=\"maindocselementHB\" id=\"{$idlink1}\" data-id=\"{$id1}\" data-iblock=\"14\"  itemprop=\"v1\" title=\"Информация о трудоустройстве 3 года назад\">{$v1}</td>";
				$html.="<td itemprop=\"t1\">{$t1}</td>";
	
				//$html.="<td title=\"{$tt2}\" class=\"maindocselementHB\" id=\"{$idlink2}\" data-id=\"{$id2}\" data-iblock=\"14\"  itemprop=\"v2\" title=\"Информация о трудоустройстве  2 года назад\">{$v2}</td>";
				//$html.="<td itemprop=\"t2\">{$t2}</td>";
	
				//$html.="<td title=\"{$tt3}\" class=\"maindocselementHB\" id=\"{$idlink3}\" data-id=\"{$id3}\" data-iblock=\"14\"  itemprop=\"v3\" title=\"Информация о трудоустройстве  1 год назад\">{$v3}</td>";
				//$html.="<td itemprop=\"t3\">{$t3}</td>";
				$html.="</tr>";
			}	
		}else{
			$html.="<tr class=\"maindocselementHB\" id=\"{$idlink0}\" data-id=\"0\" data-iblock=\"14\" itemprop=\"graduateJob\" title=\"Информация о трудоустройстве\">";
			$html.="<td itemprop=\"eduCode\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"eduName\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"eduProf\">".$this::emptyCell."</td>";
			$html.="<td itemprop=\"v1\">выпуск отсутствует</td>";
			$html.="<td itemprop=\"t1\">выпуск отсутствует</td>";
			$html.="<td itemprop=\"v2\">выпуск отсутствует</td>";
			$html.="<td itemprop=\"t2\">выпуск отсутствует</td>";
			$html.="<td itemprop=\"v3\">выпуск отсутствует</td>";
			$html.="<td itemprop=\"t3\">выпуск отсутствует</td>";
			$html.="</tr>";
		}
		$html.="</tbody></table></div><br>";
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($buffer) return $html; else echo $html;
	
}
}//end class xobjects  