<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
/*РЅРµРѕР±С…РѕРґРёРјРѕ СЃРєРѕСЂСЂРµРєС‚РёСЂРѕРІР°С‚СЊ РЅР° С‡РёСЃР»РµРЅРЅРѕСЃС‚СЊ РІР°РєР°РЅС‚РЅС‹С… РјРµСЃС‚*/
class educhislen extends  iasmuinfo{
	private $edulevelID;
	private $russ;
	private $listitems=array();
	const cashTime=1800;
	private $cashGUID;
	public function setparams($params){
		$this->edulevelID=0;
		$this->russ=0;
		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);
		}
		if(isset($params["russ"])) {
			$this->russ=intval($params["russ"]);
		}
		$this->cashGUID=md5("educhislen_".$this->edulevelID."_".$this->russ);
	}
	private function generateItems(){
/*
$sql=<<<ssql
select distinct
if (pr.PROPERTY_84 is null,sc.UF_SPEC_CODE,pr.PROPERTY_84) as code, 
el.name as name,
if (pr.PROPERTY_121 is null,"",pr.PROPERTY_121) as profile, 
sc.UF_EDU_LEVEL as level, 
el.IBLOCK_SECTION_ID as section, 
sc.UF_EDU_FORM as form, 
sum(sc.UF_CNT_FB) as fb, 
sum(sc.UF_CNT_RB) as rb, 
sum(sc.UF_CNT_MB) as mb, 
sum(sc.UF_CNT_P) as p, 
sum(sc.UF_CNT_IN) as inyaz, 
sum(sc.UF_CNT_ALL) as cntall 
from stud_count sc 
left join `b_iblock_element_prop_s104` pr on pr.PROPERTY_84=sc.UF_SPEC_CODE or pr.PROPERTY_121 like concat(sc.UF_SPEC_CODE,"%") 
left join `b_iblock_element` el on pr.IBLOCK_ELEMENT_ID=el.id 
where 
el.IBLOCK_SECTION_ID is not NULL 
and pr.PROPERTY_578 is NULL 
and pr.PROPERTY_577 is NULL 
and (pr.PROPERTY_603 is NULL or pr.PROPERTY_603=25863) 
and (((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y")  or el.id is NULL)
 #where# 
group by sc.UF_SPEC_CODE,sc.UF_EDU_FORM, sc.UF_EDU_LEVEL, el.IBLOCK_SECTION_ID 
order by sc.UF_EDU_LEVEL desc, sc.UF_SPEC_CODE, sc.UF_EDU_FORM 
ssql;
*/
$sql=<<<ssql
select code,name, profile,level,section,form,
sum(tbl.fb) as fb, 
sum(tbl.rb) as rb, 
sum(tbl.mb) as mb, 
sum(tbl.p) as p, 
sum(tbl.fbf) as fbf, 
sum(tbl.rbf) as rbf, 
sum(tbl.mbf) as mbf, 
sum(tbl.pf) as pf, 
sum(tbl.inyaz) as inyaz, 
sum(tbl.cntall ) cntall 
from(
select distinct 
sc.id,
if (pr.PROPERTY_84 is null,sc.UF_SPEC_CODE,pr.PROPERTY_84) as code, 
el.name as name,
if (pr.PROPERTY_121 is null,"",pr.PROPERTY_121) as profile, 
sc.UF_EDU_LEVEL as level, 
el.IBLOCK_SECTION_ID as section, 
sc.UF_EDU_FORM as form, 
if(sc.UF_CNT_IN=0,sc.UF_CNT_FB, 0) as fb,
if(sc.UF_CNT_IN=0,sc.UF_CNT_RB,0) as rb, 
if(sc.UF_CNT_IN=0,sc.UF_CNT_MB,0) as mb, 
if(sc.UF_CNT_IN=0,sc.UF_CNT_P,0) as p, 
if(sc.UF_CNT_IN>0,sc.UF_CNT_FB,0) as fbf, 
if(sc.UF_CNT_IN>0,sc.UF_CNT_RB,0) as rbf, 
if(sc.UF_CNT_IN>0,sc.UF_CNT_MB,0) as mbf, 
if(sc.UF_CNT_IN>0,sc.UF_CNT_P,0) as pf, 
if(sc.UF_CNT_IN>0,sc.UF_CNT_IN,0) as inyaz, 
if(sc.UF_CNT_IN=0,sc.UF_CNT_ALL,0) as cntall 
from stud_count sc 
left join `b_iblock_element_prop_s104` pr on ((pr.PROPERTY_84=sc.UF_SPEC_CODE and (pr.PROPERTY_121 ="" or pr.PROPERTY_121 is NULL)) or (LEFT(pr.PROPERTY_121, 8)=LEFT(sc.UF_SPEC_NAME, 8)) and (pr.PROPERTY_121 !="" or pr.PROPERTY_121 is not NULL))
left join `b_iblock_element` el on pr.IBLOCK_ELEMENT_ID=el.id
where 
pr.PROPERTY_628 is NULL
and el.IBLOCK_SECTION_ID is not NULL 
and pr.PROPERTY_578 is NULL 
and pr.PROPERTY_577 is NULL 
and (pr.PROPERTY_603 is NULL or pr.PROPERTY_603=25863) 
and (((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y")  or el.id is NULL)
 #where# 
) tbl
group by code,profile,level,section,form
order by level desc, code, form
ssql;
		$where="";
		//echo "<!--".$sql."-->";
		if($this->russ==1) $where=" and sc.UF_COUNTRY=\"Р РћРЎРЎР