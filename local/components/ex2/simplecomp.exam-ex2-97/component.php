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

if ($this->StartResultCache($arParams['CACHE_TIME'], $USER->GetID()))
{
	if ($USER->IsAuthorized())
	{
		//iblock elements
		$arSelectElems = array (
			"ID",
			"IBLOCK_ID",
			"NAME",
			$arParams['NEWS_FIELD_CODE'],
		);
		$arFilterElems = array (
			"IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"],
			"ACTIVE" => "Y"
		);
		$arSortElems = array (
			"NAME" => "ASC"
		);

		$arResult["ELEMENTS"] = array();
		$rsElements = CIBlockElement::GetList($arSortElems, $arFilterElems, false, false, $arSelectElems);
		while($arElement = $rsElements->Fetch())
		{
			$arResult["ELEMENTS"][$arElement[$arParams['NEWS_FIELD_CODE'].'_VALUE']][] = $arElement;
		}

		// user
		$arOrderUser = array("id");
		$sortOrder = "asc";
		$arFilterUser = array(
			"ACTIVE" => "Y",
		);

		$arResult["USERS"] = array();
		$rsUsers = CUser::GetList($arOrderUser, $sortOrder, $arFilterUser,["SELECT"=>['UF_AUTHOR_TYPE'], "FIELDS" => ['ID', 'UF_AUTHOR_TYPE', 'LOGIN']]); // выбираем пользователей
		while($arUser = $rsUsers->Fetch())
		{
			foreach ($arResult['ELEMENTS'] as $key => $element)
			{
				if ($arUser['ID'] == $key)
				{
					$arUser['ELEMENTS'] = $element;
					$arResult['USERS'][] = $arUser;
				}
			}
		}

		$this->includeComponentTemplate();
	}
}
$APPLICATION->SetTitle(GetMessage('SIMPLECOMP_EXAM2_TITLE', ['#COUNT#' => 1]));
?>