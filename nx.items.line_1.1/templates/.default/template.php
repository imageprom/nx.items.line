<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//$arResult['SECTIONS'] - справочник разделов при установленном параметре GET_SECTION_LIST

$this->setFrameMode(true);
//$this->SetViewTarget('articles');?>
<?if(count($arResult['ITEMS'])>0):?>
<section class="nx-news-articles">
	<?if($arParams['ADD_TITLE']!=''):?><h6 class="h1_und"><?=$arParams['ADD_TITLE']?></h6><?endif;?>
	<div class="nx-flex-col-st hyphenate">
	<?foreach($arResult['ITEMS'] as $cell => $arItem):?>
	<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
	  $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));?>
		<a href="<?=$arItem['DETAIL_PAGE_URL']?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="element el_cnt_<?=$cell?>">
            <?if(is_array($arItem['PREVIEW_PICTURE'])):?>
                <figure class="prw-block" <?$mod = '#x-';?>>
                    <span href="<?=$arItem['DETAIL_PAGE_URL']?>" class="prw <?if(!is_array($arItem['PREVIEW_PICTURE'])):?>no-photo<?endif?>" >
                            <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>"
                                <?if($arItem['PREVIEW_PICTURE']['WIDTH'] < $arItem['PREVIEW_PICTURE']['HEIGHT']):?>
                                    class="vertical"
                                <?endif;?>
                            />
                    </span>
                    <?if($arItem['PROPERTIES']['STATUS']['VALUE']):?>
                        <figcaption class="catalog-status s-<?=$arItem['PROPERTIES']['STATUS']['VALUE']?>">
                            <?=$arItem['PROPERTIES']['STATUS']['VALUE']?>
                        </figcaption>
                    <?endif;?>
                </figure>
            <?endif?>

            <?if($arItem['DISPLAY_ACTIVE_FROM']):?>
                <time datetime="<?=$arItem['ACTIVE_FROM_F']?>" class="data-time"><?=$arItem['DISPLAY_ACTIVE_FROM']?></time>
			<?endif?>

			<strong class="ttl hyphenate"><?=$arItem['NAME']?></strong>
			<div class="anons"><?=$arItem['PREVIEW_TEXT']?></div>
			<div class="more"><span>узнать больше</span> →</div>
		</a>					
	<?endforeach; // foreach($arResult["ITEMS"] as $arItem):?>
	</div>
</section>
<?endif;?>
<?//$this->EndViewTarget();?>