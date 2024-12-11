<?php
	use Bitrix\Main\UserField\Types\EnumType;

AddEventHandler("main", "OnUserTypeBuildList", array("CUserTypeMLibConLink", "GetUserTypeDescription"));

class CUserTypeMLibConLink extends EnumType
{
	/** Декларирует тип свойства и его название **/
	static function GetUserTypeDescription(): array
	{
		return array(
			"USER_TYPE_ID" => "medialib_collection_link",
			"CLASS_NAME" => "CUserTypeMLibConLink",
			"DESCRIPTION" => GetMessage("USER_TYPE_MLIB_CON_LINK_DESCRIPTION"),
			"BASE_TYPE" => 'enum',
		);
	}
	/** --------------------------------------- **/

	public static function OnBeforeSave($userField,	$value)
	{
		$value = \Bitrix\Main\Web\Json::encode($value);
        return $value;
	}

	public function onAfterFetch($arProperty, $arValue): array
	{
		if (!empty($arValue["VALUE"])) {
			$arValue = \Bitrix\Main\Web\Json::decode(html_entity_decode($arValue["VALUE"]));
		}

		return $arValue;
	}
}
?>