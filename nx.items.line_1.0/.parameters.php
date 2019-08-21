<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes();

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
{
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arISections = Array();
$db_sec = CIBlockSection::GetList(Array("SORT"=>"ASC"), Array('IBLOCK_ID'=>$arCurrentValues["IBLOCKS"], 'GLOBAL_ACTIVE'=>'Y') );
$arISections[0] = 'none';
while($arRes = $db_sec->GetNext())
{   
	$arISections[$arRes["ID"]] = $arRes["NAME"];
}

$arSorts = Array(
	"ASC" => GetMessage("T_IBLOCK_DESC_ASC"),
	"DESC" => GetMessage("T_IBLOCK_DESC_DESC"),
);


$db_status_list = CIBlockProperty::GetPropertyEnum("STATUS", Array(), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCKS"][0]));
$ar_status[0]="Все";
while($ar_status_list = $db_status_list->GetNext())
{
  $ar_status[$ar_status_list["ID"]] ="[".$ar_status_list["ID"]."] ".$ar_status_list["VALUE"] ;
}


$arSortFields = Array(
		"ID" => GetMessage("T_IBLOCK_DESC_FID"),
		"NAME" => GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM" => GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT" => GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X" => GetMessage("T_IBLOCK_DESC_FTSAMP")
);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCKS"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"MULTIPLE" => "Y",
		),
		
		"SECTIONS"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => "Разделы",
			"TYPE" => "LIST",
			"VALUES" => $arISections,
			"DEFAULT" => '',
			"MULTIPLE" => "Y",
		),
		
		"FILTER_NAME" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => "Фильтр",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		
		"ELEMENT_STATUS" =>array(
			"PARENT" => "BASE",
			"NAME" => "STATUS",
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $ar_status,
		),
		
		"NEWS_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("CP_BNL_FIELD_CODE"), "DATA_SOURCE"),
		"SORT_BY1"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
			"TYPE" => "LIST",
			"DEFAULT" => "ACTIVE_FROM",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER1"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_BY2"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER2"  =>  Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
			"TYPE" => "LIST",
			"DEFAULT" => "ASC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("IBLOCK_DETAIL_URL"),
			"",
			"URL_TEMPLATES"
		),
		
		"AVIABLE_CODE" => Array(
			"PARENT" => "PRICE_SETTINGS",
			"NAME" => "Наличие",
			"TYPE" => "TEXT",
			"DEFAULT" => "",
		),
		
	
			
		"ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "ADDITIONAL_SETTINGS"),
		"CACHE_TIME"  =>  Array("DEFAULT"=>300),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
?>