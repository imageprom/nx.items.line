<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule('iblock'))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes();

$arIBlocks = array();
$dbIblock = CIBlock::GetList(
	array('SORT' => 'ASC'),
	array(
		'SITE_ID' => $_REQUEST['site'],
		'TYPE' => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : ''))
);

while($arRes = $dbIblock->Fetch()) {
    $arIBlocks[$arRes['ID']] = $arRes['NAME'];
}

$arISections = array();

$dbSect = CIBlockSection::GetList(
	array('SORT'=>'ASC'),
	array('IBLOCK_ID' => $arCurrentValues['IBLOCKS'], 'GLOBAL_ACTIVE'=>'Y')
);

$arISections[0] = 'none';
while($arRes = $dbSect->Fetch()) {
	$arISections[$arRes['ID']] = $arRes['NAME'];
}

$arSorts = array(
	'ASC' => GetMessage('T_IBLOCK_DESC_ASC'),
	'DESC' => GetMessage('T_IBLOCK_DESC_DESC'),
);

$arStatus[0] = GetMessage('CP_BNL_ALL');
$dbStatusList = CIBlockProperty::GetPropertyEnum(
	'STATUS',
	array(),
	array('IBLOCK_ID' => $arCurrentValues['IBLOCKS'][0])
);

while($arStatusList = $dbStatusList->GetNext()) {
	$arStatus[$arStatusList['ID']] = '['.$arStatusList['ID'].'] '.$arStatusList['VALUE'];
}

$arSortFields = array(
	'ID' => GetMessage('T_IBLOCK_DESC_FID'),
	'NAME' => GetMessage('T_IBLOCK_DESC_FNAME'),
	'ACTIVE_FROM' => GetMessage('T_IBLOCK_DESC_FACT'),
	'SORT' => GetMessage('T_IBLOCK_DESC_FSORT'),
	'TIMESTAMP_X' => GetMessage('T_IBLOCK_DESC_FTSAMP')
);

$arComponentParameters = array(
	'GROUPS' => array(),

	'PARAMETERS'  =>  array(
		'IBLOCK_TYPE'  =>  array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('T_IBLOCK_DESC_LIST_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => $arTypesEx,
			'DEFAULT' => 'news',
			'REFRESH' => 'Y',
		),

		'IBLOCKS'  =>  array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('T_IBLOCK_DESC_LIST_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arIBlocks,
			'DEFAULT' => '',
			'MULTIPLE' => 'Y',
		),
		
		'SECTIONS'  =>  array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CP_BNL_SECT'),
			'TYPE' => 'LIST',
			'VALUES' => $arISections,
			'DEFAULT' => '',
			'MULTIPLE' => 'Y',
		),

        'GET_SECTION_LIST' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('CP_BNL_SECT_LIST'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
		
		'FILTER_NAME' => array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('CP_BNL_FILTER'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
		
		'ELEMENT_STATUS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CP_BNL_STATUS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'VALUES' => $arStatus,
		),
		
		'NEWS_COUNT'  =>  array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('T_IBLOCK_DESC_LIST_CONT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '20',
		),

		'FIELD_CODE' => CIBlockParameters::GetFieldCode(GetMessage('CP_BNL_FIELD_CODE'), 'DATA_SOURCE'),

		'SORT_BY1'  =>  array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('T_IBLOCK_DESC_IBORD1'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'ACTIVE_FROM',
			'VALUES' => $arSortFields,
			'ADDITIONAL_VALUES' => 'Y',
		),

		'SORT_ORDER1'  =>  array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('T_IBLOCK_DESC_IBBY1'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'DESC',
			'VALUES' => $arSorts,
			'ADDITIONAL_VALUES' => 'Y',
		),

		'SORT_BY2'  =>  array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('T_IBLOCK_DESC_IBORD2'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'SORT',
			'VALUES' => $arSortFields,
			'ADDITIONAL_VALUES' => 'Y',
		),

		'SORT_ORDER2'  =>  array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('T_IBLOCK_DESC_IBBY2'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'ASC',
			'VALUES' => $arSorts,
			'ADDITIONAL_VALUES' => 'Y',
		),

		'DETAIL_URL' => CIBlockParameters::GetPathTemplateParam(
			'DETAIL',
			'DETAIL_URL',
			GetMessage('IBLOCK_DETAIL_URL'),
			'',
			'URL_TEMPLATES'
		),
		
		'AVAILABLE_CODE' => array(
			'PARENT' => 'PRICE_SETTINGS',
			'NAME' => GetMessage('CP_BNL_AV'),
			'TYPE' => 'TEXT',
			'DEFAULT' => '',
		),

		'ACTIVE_DATE_FORMAT' => CIBlockParameters::GetDateFormat(GetMessage('T_IBLOCK_DESC_ACTIVE_DATE_FORMAT'), 'ADDITIONAL_SETTINGS'),
		'CACHE_TIME'  =>  array('DEFAULT' => 300),
		'CACHE_GROUPS' => array(
			'PARENT' => 'CACHE_SETTINGS',
			'NAME' => GetMessage('CP_BNL_CACHE_GROUPS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
	),
);
