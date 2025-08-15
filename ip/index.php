<meta name="referrer" content="origin" />
<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//header("Access-Control-Allow-Origin: https://do.asmu.ru");
$v="";
 if (!empty($_SERVER['HTTP_CLIENT']))
  {
    $ip=$_SERVER['HTTP_CLIENT'];$v="; ";
  }elseif (!empty($_SERVER['HTTP_CLIENT_IP']))
  {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip=$_SERVER['REMOTE_ADDR'];
  }

  $ipp= preg_replace("/[^,.0-9]/", '', $ip).$v;

//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
//header("Access-Control-Allow-Headers: X-Requested-With");
echo $ipp;/*
$ip = $ipp;
$xml = simplexml_load_string(file_get_contents('http://rest.db.ripe.net/search?query-string=' . $ip));
$array = json_decode(json_encode($xml), TRUE);		
 
$data = array();
foreach ($array['objects'] as $row) {
	foreach ($row as $row2) {
		foreach ($row2['attributes'] as $row3) {
			foreach ($row3 as $row4) {
				$data[$row4['@attributes']['name']][] = $row4['@attributes']['value'];
			}
		}
	}
}
 
print_r($data);*/