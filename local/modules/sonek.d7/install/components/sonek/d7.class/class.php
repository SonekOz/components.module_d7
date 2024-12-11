<?php
	use \Bitrix\Main\Loader;
	use \Bitrix\Main\Localization\Loc;

	class D7Class extends CBitrixComponent
	{
		var $test;

		protected function checkModules()
		{
			if (!Loader::includeModule('sonek.d7'))
			{
				ShowError(Loc::getMessage('SONEK_D7_MODULE_NOT_INSTALLED'));
				return false;
			}

			return true;
		}

		function var1()
		{
			$arResult['var1'] = 'Отработал метод var1 компонента class';

			return $arResult;
		}

		public function executeComponent()
		{
			$this->includeComponentLang('class.php');

			if($this->checkModules())
			{
				$this->arResult = array_merge($this->arResult, $this->var1());

				$this->includeComponentTemplate();
			}
		}
	}