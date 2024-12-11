<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"PARAMETERS" => array(
		"NEWS_IBLOCK_ID" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_IBLOCK_ID"),
			"TYPE" => "STRING",
		),
		"NEWS_FIELD_CODE" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_FIELD_CODE"),
			"TYPE" => "STRING",
		),
		"USER_FIELD_CODE" => array(
			"NAME" => GetMessage("SIMPLECOMP_EXAM2_USER_FIELD_CODE"),
			"TYPE" => "STRING",
		),
		"CACHE_TIME" => ["DEFAULT"=>36000000],
	),
);