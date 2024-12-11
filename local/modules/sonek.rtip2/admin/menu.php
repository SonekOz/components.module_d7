<?php
	IncludeModuleLangFile(__FILE__);

	if($APPLICATION->GetGroupRight("sonek.rtip2") > "D")
	{
		$aMenu = [];
		$aMenu[] = [
			"parent_menu" => "global_menu_settings",
			"SORT" => 100,
			"url" => "sonek.rtip2_rubric_admin.php?lang=".LANGUAGE_ID,
			"text" => "rubric_admin.php",
			"title" => "rubric_admin.php",
			"page_icon" => "form_menu_icon",
			"items_id" => "menu_sonekrtip2",
			"module_id" => "sonek.rtip2",
		];
		$aMenu[] = [
			"parent_menu" => "global_menu_content",
			"SORT" => 100,
			"url" => "form_list.php?lang=".LANGUAGE_ID,
			"text" => "Список форм модуля веб-форм",
			"title" => "Тестовое меню модуля академии",
			"icon" => "form_menu_icon",
			"page_icon" => "form_menu_icon",
			"items_id" => "menu_sonekrtip2",
			"module_id" => "sonek.rtip2",
			"items" => []
		];
		require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/include.php");
		$f = CForm::GetMenuList(["LID" => LANGUAGE_ID]);
		while($fr = $f->GetNext())
		{
			if(strlen($fr["MENU"]) > 0)
			{
				$aMenu["items"][] = [
					"text" => $fr["MENU"],
					"url" => "form_result_list.php?lang=".LANGUAGE_ID."&WEB_FORM_ID=".$fr["ID"],
					"icon" => "form_menu_icon",
					"page_icon" => "form_page_icon",
					"more_url" => [
						"form_view.php?WEB_FORM_ID=".$fr["ID"],
						"form_result_list.php?WEB_FORM_ID=".$fr["ID"],
						"form_result_edit.php?WEB_FORM_ID=".$fr["ID"],
						"form_result_print.php?WEB_FORM_ID=".$fr["ID"],
						"form_result_view.php?WEB_FORM_ID=".$fr["ID"],
					],
					"title" => "Список результатов формы *#NAME#*"
				];
			}
		}

		return $aMenu;
	}

	return false;