<?php

	use Bitrix\Main\Localization\Loc;

	IncludeModuleLangFile(__FILE__);

	if($APPLICATION->GetGroupRight("sonek.zadp2") > "D")
	{
		$aMenu = [
			"parent_menu" => "global_menu_services",
			"SORT" => 100,
			"text" => Loc::getMessage('L_MAIN_BUTTON_TEXT'),
			"page_icon" => "form_menu_icon",
			"items_id" => "menu_sonekzadp2",
			"module_id" => "sonek.zadp2",
			'items' => [
				[
					"text" => Loc::getMessage('L_ELEMENT_LIST'),
					"url" => "sonek.zadp2_zadaniep2_admin.php?lang=".LANGUAGE_ID,
				],
				[
					"text" => Loc::getMessage('L_ADD_ELEMENT_PAGE'),
					"url" => "sonek.zadp2_zadaniep2_edit.php?lang=".LANGUAGE_ID,
				],
			],
		];

		return $aMenu;
	}

	return false;