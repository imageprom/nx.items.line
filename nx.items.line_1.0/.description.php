<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('IP_COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('IP_COMPONENT_DESC'),
	"ICON" => "/images/news_line.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	'PATH' => array(
		'ID' => 'my_components',
		'NAME' => GetMessage('IP_COMPONENTS_TITLE'),
		'CHILD' => array(
			'ID' => 'ip_news',
			'NAME' => GetMessage('IP_COMPONENTS_GROUP')
		)
	),
);

?>
