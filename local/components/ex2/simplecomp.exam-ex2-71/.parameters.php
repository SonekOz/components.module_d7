<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"PARAMETERS" => array(
		"PRODUCTS_IBLOCK_ID" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_CAT_IBLOCK_ID"),
			"TYPE" => "STRING",
		),
		"CLASSIFICATOR_IBLOCK_ID" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_CLASS_IBLOCK_ID"),
			"TYPE" => "STRING",
		),
		"DETAIL_TEMPLATE_LINK" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_DETAIL_TEMPLATE_LINK"),
			"TYPE" => "STRING",
		),
		"PRODUCT_PROPERTY_ID" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_PRODUCT_PROPERTY_ID"),
			"TYPE" => "STRING",
		),
		"CACHE_TIME"  =>  ["DEFAULT"=>36000000],
	),
);