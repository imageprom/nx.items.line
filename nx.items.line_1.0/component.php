<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"]) <= 0)
	$arParams["IBLOCK_TYPE"] = "news";
if($arParams["IBLOCK_TYPE"]=="-")
	$arParams["IBLOCK_TYPE"] = "";

	
global $arrFilter, $arrFilterAdd;
if(!is_array($arrFilter)) $arrFilter = array();	
if(!is_array($arrFilterAdd)) $arrFilterAdd = array();

$arParams['AJAX'] = isset($_REQUEST['nx_ajax_ibl_action']) && $_REQUEST['nx_ajax_ibl_action'] == 'Y';

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
	$arrFilter = array();
}
else {
	global ${$arParams["FILTER_NAME"]};
	global ${$arParams["FILTER_NAME"].'Add'};
	$arrFilter = ${$arParams["FILTER_NAME"]};
	$arrFilterAdd = ${$arParams["FILTER_NAME"].'Add'};
	if(!is_array($arrFilter)) $arrFilter = array();
	if(!is_array($arrFilterAdd)) $arrFilterAdd = array();
		
	$fCount = count ($arrFilter);
	$fAdddCount = count ($arrFilterAdd);

	if($fCount == 0 && $fAdddCount > 0) {$arrFilter = $arrFilterAdd; unset($arrFilterAdd);}	
    if($fAdddCount == 0) unset($arrFilterAdd);
	
}
if(!is_array($arParams["IBLOCKS"]))
	$arParams["IBLOCKS"] = array($arParams["IBLOCKS"]);
foreach($arParams["IBLOCKS"] as $k=>$v)
	if(!$v)
		unset($arParams["IBLOCKS"][$k]);
		
if(!is_array($arParams["SECTIONS"]))
	$arParams["SECTIONS"] = array($arParams["SECTIONS"]);
	
foreach($arParams["SECTIONS"] as $k=>$v)
	if(!$v || $v == 0)
		unset($arParams["SECTIONS"][$k]);

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

if($arParams["ELEMENT_STATUS"]) $arParams["ELEMENT_STATUS"] = intval($arParams["ELEMENT_STATUS"]);

$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));

if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arrFilter, $arrFilterAdd))) {
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult=array(
		"ITEMS"=>array(),
	);
	
	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"IBLOCK_ID",
		"ACTIVE_FROM",
		"DETAIL_PAGE_URL",
		"NAME",
		
	));
	
	$arFilter = array (
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"=> $arParams["IBLOCKS"],
		"ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	
	);
	
	if(count($arParams["SECTIONS"])>0) {
		$arFilter['SECTION_ID'] =  $arParams["SECTIONS"];
	}
	
	if($arParams["AVIABLE_CODE"]) {
		$arFilter['>PROPERTY_'.$arParams["AVIABLE_CODE"]] = 0;
		$arSelect [] = 'PROPERTY_'.$arParams["AVIABLE_CODE"];
	}
	
	if($arParams["ELEMENT_STATUS"]) {
		$arFilter['PROPERTY_STATUS'] = $arParams["ELEMENT_STATUS"];
		$arSelect [] = 'PROPERTY_STATUS';
		
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCKS"][0], 'CODE'=>'STATUS'));
		if ($prop_fields = $properties->GetNext()) {
		  $arResult['STATUS'] = $prop_fields;
		   $arResult['STATUS']['DATA'] =  CIBlockPropertyEnum::GetByID($arParams["ELEMENT_STATUS"]);
		}	
	}
	
	$arOrder = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);
	if(!array_key_exists("ID", $arOrder))
		$arOrder["ID"] = "DESC";
	
	
	
	$rsItems = CIBlockElement::GetList($arOrder, array_merge($arFilter, $arrFilter), false, array("nTopCount"=>$arParams["NEWS_COUNT"]), $arSelect);
	$rsItems->SetUrlTemplates($arParams["DETAIL_URL"]);
	$currentCount  = 0;
	$ids = array();
	
	while($artItem = $rsItems->GetNextElement()) {   
		$arItem = $artItem->GetFields();
		$arButtons = CIBlock::GetPanelButtons(
			$arItem["IBLOCK_ID"],
			$arItem["ID"],
			0,
			array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		);
		$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		if(strlen($arItem["ACTIVE_FROM"])>0)
			$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
		else
			$arItem["DISPLAY_ACTIVE_FROM"] = "";

		if(isset($arItem["PREVIEW_PICTURE"]))
			$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
		if(isset($arItem["DETAIL_PICTURE"]))
			$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

	    $arItem["PROPERTIES"] = $artItem->GetProperties();
		$ids[] = $arItem['ID']; 
		$arResult["ITEMS"][]=$arItem;
		$arResult["LAST_ITEM_IBLOCK_ID"]=$arItem["IBLOCK_ID"];
		
		$currentCount ++;
	}
	
	if( ($currentCount < $arParams["NEWS_COUNT"]) && isset($arrFilterAdd)) {
		$adFilter = array_merge($arFilter, $arrFilterAdd);
		if($adFilter['!ID']) {
			if(!is_array($adFilter['!ID'])) {
			    
				$ids[] =  $adFilter['!ID'];
				$adFilter['!ID'] = $ids;
			}
			else $adFilter['!ID'] = array_merge($adFilter['!ID'], $ids);
		} else $adFilter['!ID'] = $ids;
		
	
		$rsItems = CIBlockElement::GetList($arOrder, $adFilter, false, array("nTopCount"=>($arParams["NEWS_COUNT"]-$currentCount)), $arSelect);
		$rsItems->SetUrlTemplates($arParams["DETAIL_URL"]);
		while($artItem = $rsItems->GetNextElement())
		{   $arItem = $artItem->GetFields();
			$arButtons = CIBlock::GetPanelButtons(
				$arItem["IBLOCK_ID"],
				$arItem["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

			if(strlen($arItem["ACTIVE_FROM"])>0)
				$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
			else
				$arItem["DISPLAY_ACTIVE_FROM"] = "";

			if(isset($arItem["PREVIEW_PICTURE"]))
				$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
			if(isset($arItem["DETAIL_PICTURE"]))
				$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

			$arItem["PROPERTIES"] = $artItem->GetProperties();
			$ids[] = $arItem['ID']; 
			$arResult["ITEMS"][]=$arItem;
			$arResult["LAST_ITEM_IBLOCK_ID"]=$arItem["IBLOCK_ID"];
			
			$currentCount ++;
		}
	}

	if( ($currentCount < $arParams["NEWS_COUNT"]) && isset($arrFilterAdd['PROPERTY_BRAND']) && count($arrFilterAdd) > 1) {

		unset($arrFilterAdd['PROPERTY_BRAND']);
		$adFilter = array_merge($arFilter, $arrFilterAdd);
		if($adFilter['!ID']) {
			if(!is_array($adFilter['!ID'])) {
			    
				$ids[] =  $adFilter['!ID'];
				$adFilter['!ID'] = $ids;
			}
			else $adFilter['!ID'] = array_merge($adFilter['!ID'], $ids);
		} else $adFilter['!ID'] = $ids;
		
	
		$rsItems = CIBlockElement::GetList($arOrder, $adFilter, false, array("nTopCount"=>($arParams["NEWS_COUNT"]-$currentCount)), $arSelect);
		$rsItems->SetUrlTemplates($arParams["DETAIL_URL"]);
		while($artItem = $rsItems->GetNextElement())
		{   $arItem = $artItem->GetFields();
			$arButtons = CIBlock::GetPanelButtons(
				$arItem["IBLOCK_ID"],
				$arItem["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

			if(strlen($arItem["ACTIVE_FROM"])>0)
				$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
			else
				$arItem["DISPLAY_ACTIVE_FROM"] = "";

			if(isset($arItem["PREVIEW_PICTURE"]))
				$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
			if(isset($arItem["DETAIL_PICTURE"]))
				$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

			$arItem["PROPERTIES"] = $artItem->GetProperties();
				
			$arResult["ITEMS"][]=$arItem;
			$arResult["LAST_ITEM_IBLOCK_ID"] = $arItem["IBLOCK_ID"];
		}
	}
	
	
	$arISections = Array();
	$db_sec = CIBlockSection::GetList(Array("SORT"=>"ASC"), Array('IBLOCK_ID'=>$arCurrentValues["IBLOCKS"],  'ID'=>$arParams["SECTIONS"]) );
	while($arRes = $db_sec->GetNext())
	{   
		$arResult["SECTION"] = $arRes;
	}
	
	$this->SetResultCacheKeys(array(
		"LAST_ITEM_IBLOCK_ID",
	));
	
	$this->IncludeComponentTemplate();
}

if(
	$arResult["LAST_ITEM_IBLOCK_ID"] > 0
	&& $USER->IsAuthorized()
	&& $APPLICATION->GetShowIncludeAreas()
	&& CModule::IncludeModule("iblock")
) {
	$arButtons = CIBlock::GetPanelButtons($arResult["LAST_ITEM_IBLOCK_ID"], 0, 0, array("SECTION_BUTTONS"=>false));
	$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
}

if($arParams['AJAX']) {
	$this->setFrameMode(false);
	define("BX_COMPRESSION_DISABLED", true);
	ob_start();
	$this->IncludeComponentTemplate("ajax");
	$json = ob_get_contents();
	$APPLICATION->RestartBuffer();
	while(ob_end_clean());
	header('Content-Type: application/json; charset='.LANG_CHARSET);
	echo $json;
	CMain::FinalActions();
	die();
}