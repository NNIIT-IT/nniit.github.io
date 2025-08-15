<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class edunir extends  iasmuinfo{
	const cashTime=6000;
	const arBlocks=array(6,11);
	
	private $edulevelID;
	private $ovz;
	private $listitems=array();
	private $cashGUID;
		
	public function setparams($params){
		$this->edulevelID=0;
		$this->ovz=0;
		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);

		}
		if(isset($params["ovz"])) {
			$this->ovz=intval($params["ovz"]);
		}
		$this->cashGUID="edunir_".$this->edulevelID;
	}
	private function generateItems(){
$sql=<<<ssql
SELECT
nir.id as id,
opp.NAME as eduName,
opp.id as oppId,
nir.NAME as nirName,
prOpp.#EDUCODE# as eduCode,
if(prOpp.#PROFILE# is null,"отсутствует",prOpp.#PROFILE#) as profile,
if(prOpp.#ADEDU#=22955,1,0) as ovz,
if(prOpp.#BEEDU#>0,1,0) as beEdu,
if(prOpp.#HIDE#>0,1,0) as hide,
bs.name as eduLevel,
bs.id as section,
if(nirs.#PERECHENNIR#="" or nirs.#PERECHENNIR# is null,"отсутствует",nirs.#PERECHENNIR#) as perechenNir,
if(nirs.#BASENIR#="" or nirs.#BASENIR# is null,"отсутствует",nirs.#BASENIR#) as baseNir,
if(nirs.#NPRNIR#="" or nirs.#NPRNIR# is null,"отсутствует",nirs.#NPRNIR#) as nprNir,
if(nirs.#STUDNIR#="" or nirs.#STUDNIR# is null,"0",nirs.#STUDNIR#) as studNir,
if(nirs.#MONOGRAFNIR#="" or nirs.#MONOGRAFNIR# is null,"0",nirs.#MONOGRAFNIR#) as monografNir,
if(nirs.#ARTICLENIR#="" or nirs.#ARTICLENIR# is null,"0",nirs.#ARTICLENIR#) as articleNir,
if(nirs.#PATENTRNIR#="" or nirs.#PATENTRNIR# is null,"0",nirs.#PATENTRNIR#) as patentRNir,
if(nirs.#PATENTZNIR#="" or nirs.#PATENTZNIR# is null,"0",nirs.#PATENTZNIR#) as patentZNir,
if(nirs.#SVIDRNIR#="" or nirs.#SVIDRNIR# is null,"0",nirs.#SVIDRNIR#) as svidRNir,
if(nirs.#SVIDZNIR#="" or nirs.#SVIDZNIR# is null,"0",nirs.#SVIDZNIR#) as svidZNir,
if(nirs.#FINANCENIR#="" or nirs.#FINANCENIR# is null,"0",nirs.#FINANCENIR#) as financeNir,
if(nirs.#FINANCENIR#="" or nirs.#NAPRAVNIR# is null,nirs.#PERECHENNIR#,nirs.#NAPRAVNIR#) as napravNir
from  
b_iblock_element opp 
left join b_iblock_element_prop_s#iblock0# prOpp on prOpp.IBLOCK_ELEMENT_ID=opp.id 
left join b_iblock_element_prop_s#iblock1# nirs on nirs.#OPPNIR#=opp.id 
left join b_iblock_element nir on nir.id=nirs.IBLOCK_ELEMENT_ID 
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=opp.id
left join b_iblock_section bs on bs.id=se.IBLOCK_SECTION_ID 

where opp.IBLOCK_ID=#iblock0# and nir.ACTIVE="Y" and opp.ACTIVE="Y" and  se.IBLOCK_ELEMENT_ID=opp.id
order by bs.id desc, prOpp.#EDUCODE#
ssql;
$sql=$this->sqltoiblock($sql,$this::arBlocks);
	$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$this->listitems[$rec["section"]]["name"]=$rec["eduLevel"];
		$row=$rec;
		$row["profile"]=preg_replace('/^(\d+\.\d+\.\d+).(\-|\–|\s)/',"",$row["profile"]);
		$this->listitems[$rec["section"]]["items"][$rec["id"]]=$row;
	}

}


public function showHtml($buffer=false){

	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$htm=trim($memcache->get($this->cashGUID));
	if ($htm=="" || $htm==false || $this->isEditmode()){
	$this->generateItems();
	

$htm=<<<shtml
<table class="simpletable nirtable "> 
<thead>
<tr>
<th>Код специальности, направления подготовки, шифр группы научных специальностей</th>
<th>Наименование профессии, специальности, направления подготовки, наименование группы научных специальностей</th>
<th>Образовательная программа, направленность, профиль, шифр и наименование научной специальности</th>
<th>Уровень образования</th>
<th>Перечень научных направлений, в рамках которых ведется научная (научно-исследовательская) деятельность</th>
<th>Результаты научной (научно-исследовательской) деятельности</th>
<th>Сведения о научно-исследовательской базе для осуществления научной (научно-исследовательской) деятельности</th>
<th>Название научного направления/научной школы</th>
</tr>
</thead>
<tbody>
shtml;

$arLeves=array(
186=>"Среднее профессиональное образование - программы подготовки специалистов среднего звена",
179=>"Высшее образование - программы бакалавриата",
180=>"Высшее образование - программы специалитета",
184=>"Высшее образование - программы магистратуры",
182=>"Высшее образование - программы подготовки научных и научно-педагогических кадров в аспирантуре (адъюнктуре)",
181=>"Высшее образование - программы ординатуры",
424=>"Дополнительное профессиональное образование",
);

if(count($this->listitems)==0){
	$htm.="<tr itemprop=\"eduNir\" title=\"\" class=\"maindocselement\" id=\"nir0\" data-id=\"0\" data-iblock=\"".$this::arBlocks[1]."\">";
	$htm.="<td itemprop=\"eduCode\">".$this::emptyCell."</td>";
			$htm.='<td itemprop="eduName">'.$this::emptyCell.'</td>';
			$htm.='<td itemprop="eduProf">'.$this::emptyCell.'</td>';
			$htm.='<td itemprop="eduLevel">'.$this::emptyCell.'</td>';
			$htm.='<td itemprop="perechenNir">'.$this::emptyCell.'</td>';
			$resultNir="<p>Количество НПР, принимающих участие в научной (научно-исследо&shy;ватель&shy;ской) деятельности:";
			$resultNir.="<span itemprop=\"nprNir\">".$this::emptyCell."</span></p>";
			$resultNir.="<p>Количество студентов, принима&shy;ющих участие в научной (научно-исследо&shy;ватель&shy;ской) деятельности:";
			$resultNir.="<span itemprop=\"studNir\">".$this::emptyCell."</span></p>";
			$resultNir.="<p>Количество изданных монографий научно-педаго&shy;гических работников образова&shy;тельной организации по всем научным направле&shy;ниям за последний год:";
			$resultNir.=" <span itemprop=\"monografNir\">".$this::emptyCell."</span></p>";
			$resultNir.="<p>Количество изданных и принятых к публикации статей в изданиях, рекомендо&shy;ван&shy;ных ВАК / зарубежных для публикации научных работ за последний год: ";
			$resultNir.="<span itemprop=\"articleNir\">".$this::emptyCell."</span></p>";
			$resultNir.="<p>Количество патентов, полученных на разработки за последний год: российских/ зарубежных: ";
			$resultNir.="<span itemprop=\"patentRNir\">".$this::emptyCell."</span>/<span itemprop=\"patentZNir\">".$this::emptyCell."</span></p>";
			$resultNir.="<p>Количество свидетельств о регистрации объекта интел&shy;лектуаль&shy;ной собственности, выданных на разработки за последний год: российских/зарубежных: ";
			$resultNir.="<span itemprop=\"svidRNir\">".$this::emptyCell."</span>/<span itemprop=\"svidZNir\">".$this::emptyCell."</span></p>";
			$resultNir.="<p title=\"Определяется:\n";
			$resultNir.="- сумма доходов по договорам на научные исследования и разработки с подписанными актами выполненных работ, сгруппированных по дате акта выполненных работ за каждый год обучения;\n";
			$resultNir.="- количество НПР, приведенных к целочисленным значениям ставок.\">";
			$resultNir.="Среднегодовой объем финансирования научных исследований на одного научно-педагогического работника организации (в приведенных к целочисленным значениям ставок) (тыс. руб): ";
			$resultNir.="<span itemprop=\"financeNir\">{$item["financeNir"]}</span></p>";
			$htm.='<td itemprop="resultNir">'.$resultNir.'</td>';
			
			$htm.='<td  itemprop="baseNir">'.$this::emptyCell.'</td>';
			$htm.='<td  itemprop="napravNir">'.$this::emptyCell.'</td>';
			$htm.='</tr>';
}
	foreach($this->listitems as $sectionID=>$section){
		$levelName=$arLeves[$sectionID];
	 if(count($section["items"])>0) 
	 $htm.="<tr><th colspan=\"8\">".$levelName."</th></tr>";
	 if(count($section["items"])>0){
	   $cnt=count($section["items"]);
		$spCodOld=0;
		foreach($section["items"] as $item){
		//==================================================================
			$title=str_replace("<br>"," ",$item["eduName"]." ".$item["profile"]);
			if(mb_strlen($title)>100){$title=mb_substr($title,0,100)."...";}
			$id=$item["id"];
			$idlink="edunir_".$id;
			$htm.="<tr itemprop=\"eduNir\" class=\"maindocselement\" id=\"{$idlink}\" data-id=\"{$id}\" data-iblock=\"".$this::arBlocks[1]."\" title=\"{$title}\">";
			$htm.="<td itemprop=\"eduCode\">".$item["eduCode"]."</td>";
			$htm.='<td itemprop="eduName">'.$item["eduName"].'</td>';
			$htm.='<td itemprop="eduProf">'.$item["profile"].'</td>';
			$htm.='<td itemprop="eduLevel">'.$levelName.'</td>';
			$x=str_replace("\r\n","<br>",$item["perechenNir"]);
			$x=str_replace("<br><br>","<br>",$x);
			$htm.='<td itemprop="perechenNir">'.$x.'</td>';
			$x=str_replace("<br><br>","<br>",$x);

			$resultNir="<p>Количество НПР, принимающих участие в научной (научно-исследо&shy;ватель&shy;ской) деятельности:";
			$resultNir.="<span itemprop=\"nprNir\">{$item["nprNir"]}</span></p>";

			$resultNir.="<p>Количество студентов, принима&shy;ющих участие в научной (научно-исследо&shy;ватель&shy;ской) деятельности:";
			$resultNir.="<span itemprop=\"studNir\">{$item["studNir"]}</span></p>";

			$resultNir.="<p>Количество изданных монографий научно-педаго&shy;гических работников образова&shy;тельной организации по всем научным направле&shy;ниям за последний год:";
			$resultNir.=" <span itemprop=\"monografNir\">{$item["monografNir"]}</span></p>";

			$resultNir.="<p>Количество изданных и принятых к публикации статей в изданиях, рекомендо&shy;ван&shy;ных ВАК / зарубежных для публикации научных работ за последний год: ";
			$resultNir.="<span itemprop=\"articleNir\">{$item["articleNir"]}</span></p>";

			$resultNir.="<p>Количество патентов, полученных на разработки за последний год: российских/ зарубежных: ";
			$resultNir.="<span itemprop=\"patentRNir\">{$item["patentRNir"]}</span>/<span itemprop=\"patentZNir\">{$item["patentZNir"]}</span></p>";

			$resultNir.="<p>Количество свидетельств о регистрации объекта интел&shy;лектуаль&shy;ной собственности, выданных на разработки за последний год: российских/зарубежных: ";
			$resultNir.="<span itemprop=\"svidRNir\">{$item["svidRNir"]}</span>/<span itemprop=\"svidZNir\">{$item["svidZNir"]}</span></p>";

			$resultNir.="<p title=\"Определяется:\n";
			$resultNir.="- сумма доходов по договорам на научные исследования и разработки с подписанными актами выполненных работ, сгруппированных по дате акта выполненных работ за каждый год обучения;\n";
			$resultNir.="- количество НПР, приведенных к целочисленным значениям ставок.\">";
			$resultNir.="Среднегодовой объем финансирования научных исследований на одного научно-педагогического работника организации (в приведенных к целочисленным значениям ставок) (тыс. руб): ";
			$resultNir.="<span itemprop=\"financeNir\">{$item["financeNir"]}</span></p>";
			
			$htm.='<td itemprop="resultNir">'.$resultNir.'</td>';

			$x=str_replace("\r\n","<br>",$item["baseNir"]);
			$htm.='<td  itemprop="baseNir">'.$x.'</td>';
			$x=str_replace("\r\n","<br>",$item["napravNir"]);
			$htm.='<td  itemprop="napravNir">'.$x.'</td>';

			$htm.='</tr>';
				//==================================================================
				}
		}
	}
	$htm.="</tbody></table>";
	$memcache->set($this->cashGUID, $htm, false, $this->cashTime);
	}
	$memcache->close();	
	if($this->isEditMode())	
		$htm=str_replace(array("#hide#","#noitems#"),array("class=\"gray\"","class=\"hide\""),$htm);
	else
		$htm=str_replace(array("#hide#","#noitems#"),array("class=\"hide\"","class=\"gray\""),$htm);
		
	if($buffer) return $htm; else echo $htm;
	}//showHtml
}//class
