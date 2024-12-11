<?
	global $USER;
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock"))
{
	ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
	return;
}
if ($this->StartResultCache($arParams['CACHE_TIME'], $USER->GetGroups()))
{
	if(intval($arParams["PRODUCTS_IBLOCK_ID"]) > 0)
	{

		//iblock elements
		$arSelectElems = array (
			"ID",
			"IBLOCK_ID",
			"NAME",
			"PROPERTY_MATERIAL",
			"PROPERTY_ARTNUMBER",
			"PROPERTY_PRICE",
			"PROPERTY_".$arParams["PRODUCT_PROPERTY_ID"],
		);
		$arFilterElems = array (
			"IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"],
			"ACTIVE" => "Y",
			">PROPERTY_".$arParams["PRODUCT_PROPERTY_ID"] => 0,
		);
		$arSortElems = array (
			"NAME" => "ASC"
		);

		$arResult["ELEMENTS"] = array();
		$rsElements = CIBlockElement::GetList($arSortElems, $arFilterElems, false, false, $arSelectElems);
		while($arElement = $rsElements->Fetch())
		{
			$arResult["ELEMENTS"][] = $arElement;
		}

		//iblock sections
		$arSelectSect = array (
			"ID",
			"IBLOCK_ID",
			"NAME",
		);
		$arFilterSect = array (
			"IBLOCK_ID" => $arParams["CLASSIFICATOR_IBLOCK_ID"],
			"ACTIVE" => "Y"
		);
		$arSortSect = array (
			"NAME" => "ASC"
		);

		$arResult["SECTIONS"] = array();
		$rsSections = CIBlockElement::GetList($arSortSect, $arFilterSect, false, false, $arSelectSect);

		$arSectionCount = 0;

		while($arSection = $rsSections->Fetch())
		{
			$arSectionCount++;
			foreach ($arResult["ELEMENTS"] as $element)
			{
				if ($arSection['ID'] === $element['PROPERTY_'.$arParams["PRODUCT_PROPERTY_ID"].'_VALUE'])
				{
					$arSection['ELEMENTS'][] = $element;
				}
			}
			$arResult['END'][] = $arSection;
		}

		$arResult['SECTION_COUNT'] = $arSectionCount;
	}

	$this->SetResultCacheKeys(['SECTION_COUNT']);
	$this->includeComponentTemplate();
}
else
{
	$this->AbortResultCache();
}
$APPLICATION->SetTitle(GetMessage('SIMPLECOMP_EXAM2_TITLE', ["#COUNT#" => $arResult['SECTION_COUNT']]));
?>