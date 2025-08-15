<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Р”РѕРєСѓРјРµРЅС‚С‹");
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$oppid=intval($_GET["opp"]);
$levelid=intval($_GET["level"]);
?>
<div class="container"><br>
<b>Р¤РёР»СЊС‚СЂ</b><br>
<input id="search_input" value="" placeholder="Р’РІРµРґРёС‚Рµ С‡Р°СЃС‚СЊ Р¤Р