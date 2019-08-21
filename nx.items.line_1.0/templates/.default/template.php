<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->setFrameMode(true);
$this->SetViewTarget('articles');?>
<?if(count($arResult['ITEMS'])>0):?>
<section class="electra-articles">
	<?if($arParams['ADD_TITLE']!=''):?><h6 class="h1_und"><?=$arParams['ADD_TITLE']?></h6><?endif;?>
	<div class="nx-flex-col-st hyphenate">
	<?foreach($arResult['ITEMS'] as $cell => $arElement):?>
	<?$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
	  $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));?>
		<a href="<?=$arElement['DETAIL_PAGE_URL']?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>" class="element el_cnt_<?=$cell?>">
			
			<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
					<time datetime="<?=date('c', MakeTimeStamp($arItem["ACTIVE_FROM"]))?>" class="data-time"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></time>
			<?endif?>

			<b  class="ttl hyphenate"><?=$arElement["NAME"]?></b>
			<div class="anons"><?=$arElement["PREVIEW_TEXT"]?></div>
			<p class="more"><span>узнать больше</span> →</p>
		</a>					
	<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
	</div>
</section>
<?endif;?>
<?$this->EndViewTarget();?>