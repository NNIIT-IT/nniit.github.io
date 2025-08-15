<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!isset($arParams["LANG"])) $arParams["LANG"]=$_SESSION["SESS_LANG_UI"];

/*TILE*/
	$title=htmlspecialchars($arResult["PROPERTIES"]["title_".$arParams["LANG"]]["VALUE"]);

	if($title=="")$title=$arResult["FIELDS"]["NAME"];
	$arResult["TITLE"]=$title;
$arResult["FIELDS"]["NAME"]=$title;
$APPLICATION->SetTitle($title);
/*TEXT*/
	$textNews=$arResult["DETAIL_TEXT"];
	if($arParams["LANG"]=="ru") $textNews=$arResult["PREVIEW_TEXT"];
	$textNews=str_replace("http://www.nsk-niil.ru/","/",$textNews);
	$textNews=str_replace("https://www.nsk-niil.ru/","/",$textNews);
	$textNews=str_replace("http://nsk-niil.ru/","/",$textNews);
	$textNews=str_replace("https://nsk-niil.ru/","/",$textNews);
	$arResult["TEXT"]=$textNews;

/*TAGS*/
if ($arParams["SEARCH_PAGE"])
{
	if ($arResult["FIELDS"] && isset($arResult["FIELDS"]["TAGS"]))
	{
		$tags = array();
		foreach (explode(",", $arResult["FIELDS"]["TAGS"]) as $tag)
		{
			$tag = trim($tag, " \t\n\r");
			if ($tag)
			{
				$url = CHTTP::urlAddParams(
					$arParams["SEARCH_PAGE"],
					array(
						"tags" => $tag,
					),
					array(
						"encode" => true,
					)
				);
				$tags[] = '<a href="'.$url.'">'.$tag.'</a>';
			}
		}
		$arResult["FIELDS"]["TAGS"] = implode(", ", $tags);
	}
}
/*VIDEO & AUDIO*/
$mediaProperty = trim($arParams["MEDIA_PROPERTY"]);
if ($mediaProperty)
{
	if (is_numeric($mediaProperty))
	{
		$propertyFilter = array(
			"ID" => $mediaProperty,
		);
	}
	else
	{
		$propertyFilter = array(
			"CODE" => $mediaProperty,
		);
	}

	$elementIndex = array();
	$elementIndex[$arResult["ID"]] = array(
		"PROPERTIES" => array(),
	);

	CIBlockElement::GetPropertyValuesArray($elementIndex, $arResult["IBLOCK_ID"], array(
		"IBLOCK_ID" => $arResult["IBLOCK_ID"],
		"ID" => $arResult["ID"],
	), $propertyFilter);

	foreach ($elementIndex as $idx)
	{
		foreach ($idx["PROPERTIES"] as $property)
		{
			$url = '';
			if ($property["MULTIPLE"] == "Y" && $property["VALUE"])
			{
				$url = current($property["VALUE"]);
			}
			if ($property["MULTIPLE"] == "N" && $property["VALUE"])
			{
				$url = $property["VALUE"];
			}

			if (preg_match('/(?:youtube\\.com|youtu\\.be).*?[\\?\\&]v=([^\\?\\&]+)/i', $url, $matches))
			{
				$arResult["VIDEO"] = 'https://www.youtube.com/embed/'.$matches[1].'/?rel=0&controls=0showinfo=0';
			}
			elseif (preg_match('/(?:vimeo\\.com)\\/([0-9]+)/i', $url, $matches))
			{
				$arResult["VIDEO"] = 'https://player.vimeo.com/video/'.$matches[1];
			}
			elseif (preg_match('/(?:rutube\\.ru).*?\\/video\\/([0-9a-f]+)/i', $url, $matches))
			{
				$arResult["VIDEO"] = 'http://rutube.ru/video/embed/'.$matches[1].'?sTitle=false&sAuthor=false';
			}
			elseif (preg_match('/(?:soundcloud\\.com)/i', $url, $matches))
			{
				$arResult["SOUND_CLOUD"] = $url;
			}
		}
	}
}

/*SLIDER*/
$sliderProperty = trim($arParams["SLIDER_PROPERTY"]);
if ($sliderProperty)
{
	if (is_numeric($sliderProperty))
	{
		$propertyFilter = array(
			"ID" => $sliderProperty,
		);
	}
	else
	{
		$propertyFilter = array(
			"CODE" => $sliderProperty,
		);
	}

	$elementIndex = array();
	$elementIndex[$arResult["ID"]] = array(
		"PROPERTIES" => array(),
	);

	CIBlockElement::GetPropertyValuesArray($elementIndex, $arResult["IBLOCK_ID"], array(
		"IBLOCK_ID" => $arResult["IBLOCK_ID"],
		"ID" => $arResult["ID"],
	), $propertyFilter);

	foreach ($elementIndex as $idx)
	{
		foreach ($idx["PROPERTIES"] as $property)
		{
			$files = array();
			if ($property["MULTIPLE"] == "Y" && $property["VALUE"])
			{
				$files = $property["VALUE"];
			}
			if ($property["MULTIPLE"] == "N" && $property["VALUE"])
			{
				$files = array($property["VALUE"]);
			}

			if ($files)
			{
				$arResult["SLIDER"] = array();
				foreach ($files as $fileId)
				{
					$file = CFile::GetFileArray($fileId);
					if ($file && $file["WIDTH"] > 0 && $file["HEIGHT"] > 0)
					{
						$arResult["SLIDER"][] = $file;
					}
				}
			}
		}
	}
}

/*GALLERY*/

$arResult["MORE_PHOTO"] = array();

if(isset($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]) && is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]))
{
//$arResult["MORE_PHOTO"]["VALUES"]=$arResult["PROPERTIES"]["MORE_PHOTO"];
foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $kkk=>$FILE)
{
$FILE = CFile::GetFileArray($FILE);
if(is_array($FILE))
	$arResult["MORE_PHOTO"][$kkk]=$FILE;

}
}
/*pict*/

/*
$arResult["PREVIEW_PICTURE"] = array();

if(isset($arResult["PROPERTIES"]["PREVIEW_PICTURE"]["VALUE"]))
{
	$FILE = CFile::GetFileArray($arResult["PROPERTIES"]["PREVIEW_PICTURE"]["VALUE"]);
	if(is_array($FILE))
		$arResult["PREVIEW_PICTURE"]=$FILE;

}
*/

/* THEME */
$arParams["TEMPLATE_THEME"] = trim($arParams["TEMPLATE_THEME"]);
if ($arParams["TEMPLATE_THEME"] != "")
{
	$arParams["TEMPLATE_THEME"] = preg_replace("/[^a-zA-Z0-9_\-\(\)\!]/", "", $arParams["TEMPLATE_THEME"]);
	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
		$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
		$arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
	}
	if ($arParams["TEMPLATE_THEME"] != "")
	{
		if (!is_file($_SERVER["DOCUMENT_ROOT"].$this->GetFolder()."/themes/".$arParams["TEMPLATE_THEME"]."/style.css"))
			$arParams["TEMPLATE_THEME"] = "";
	}
}
if ($arParams["TEMPLATE_THEME"] == "")
	$arParams["TEMPLATE_THEME"] = "blue";
GLOBAL $_SESSION;
$arResult["LANG"] = $_SESSION["SESS_LANG_UI"];

//для эпилога
$cp = $this->__component; // объект компонента

if (is_object($cp))
{
    // добавим в arResult 
    $cp->arResult['OG_TITLE']=$title;
    $cp->arResult["OG_IMAGE"]=$arResult["PREVIEW_PICTURE"]["SRC"];
    $cp->SetResultCacheKeys(array('OG_TITLE','OG_IMAGE'));
    // сохраним их в копии arResult, с которой работает шаблон
    $arResult['OG_TITLE'] = $cp->arResult['OG_TITLE'];
    $arResult['OG_IMAGE'] = $cp->arResult['OG_IMAGE'];
}
