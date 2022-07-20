<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

class CNXItemsLine extends \CBitrixComponent {
	public static function GetData(&$arItem, &$arParams){
        $arButtons = CIBlock::GetPanelButtons(
            $arItem['IBLOCK_ID'],
            $arItem['ID'],
            0,
            array('SECTION_BUTTONS'=>false, 'SESSID'=>false)
        );
        $arItem['EDIT_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'];
        $arItem['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'];

        if(strlen($arItem['ACTIVE_FROM']) > 0) {
            $arItem['DISPLAY_ACTIVE_FROM'] = CIBlockFormatProperties::DateFormat($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arItem['ACTIVE_FROM'], CSite::GetDateFormat()));
            $arItem['ACTIVE_FROM_F'] = date('c', MakeTimeStamp($arItem['ACTIVE_FROM']));
        }
        else {
            $arItem['DISPLAY_ACTIVE_FROM'] = '';
            $arItem['ACTIVE_FROM_F'] = '';
        }

        if(isset($arItem['PREVIEW_PICTURE']))
            $arItem['PREVIEW_PICTURE'] = CFile::GetFileArray($arItem['PREVIEW_PICTURE']);
        if(isset($arItem['DETAIL_PICTURE']))
            $arItem['DETAIL_PICTURE'] = CFile::GetFileArray($arItem['DETAIL_PICTURE']);

    }
}