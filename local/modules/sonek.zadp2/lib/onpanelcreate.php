<?php

	use Bitrix\Main\Localization\Loc;

	AddEventHandler("main", "OnPanelCreate", Array("MyClass123", "OnPanelCreateHandler"));
	class MyClass123
	{
		// добавим кнопку в панель управления
		public static function OnPanelCreateHandler()
		{
			global $APPLICATION;
			$APPLICATION->AddPanelButton(array(
				"TEXT" => Loc::getMessage('OPC_TITLE'),
				"HREF"      => "/bitrix/admin/sonek.zadp2_zadaniep2_admin.php?lang=ru", // ссылка на кнопке
				"ALT"       => Loc::getMessage('OPC_DESC'),
				"MAIN_SORT" => 300,
				"SORT"      => 10,
				"ICON" => 'bx-panel-components-icon'
			));
		}
		/*
			Теперь при выводе панели управления в публичной части сайта
			будет также всегда выводиться наша кнопка
		*/
	}