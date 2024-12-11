<?php
	use \Bitrix\Main\Localization\Loc;
	use \Bitrix\Main\Config as Conf;
	use \Bitrix\Main\Config\Option;
	use \Bitrix\Main\Loader;
	use \Bitrix\Main\Entity\Base;
	use \Bitrix\Main\Application;

	Loc::loadMessages(__FILE__);
	Class sonek_d7 extends CModule
	{
		var $exclusionAdminFiles;
		function __construct()
		{
			$arModuleVersion = [];
			include(__DIR__."/version.php");

			$this->exclusionAdminFiles = [
				"..",
				".",
				"menu.php",
				"operation_description.php",
				"task_description.php"
			];

			$this->MODULE_ID = "sonek.d7";
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME = Loc::getMessage("SONEK_D7_MODULE_NAME");
			$this->MODULE_DESCRIPTION = Loc::getMessage("SONEK_D7_MODULE_DESCRIPTION");

			$this->PARTNER_NAME = Loc::getMessage("SONEK_D7_PARTNER_NAME");
			$this->PARTNER_URI = Loc::getMessage("SONEK_D7_PARTNER_URI");

			$this->MODULE_SORT = 1;
			$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
			$this->MODULE_GROUP_RIGHTS = "Y";
		}

		/** Определяем место размещения модуля **/
		public function GetPath($notDocumentRoot = false)
		{
			if($notDocumentRoot)
				return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
			else
				return dirname(__DIR__);
		}

		/** Проверяем что система поддерживает D7 **/
		public function isVersionD7()
		{
			return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
		}

		function InstallDB()
		{
			Loader::includeModule($this->MODULE_ID);

			if(!Application::getConnection(\Sonek\D7\BookTable::getConnectionName())->isTableExists(
				Base::getInstance('\Sonek\D7\BookTable')->getDBTableName()
			)
			)
			{
				Base::getInstance('\Sonek\D7\BookTable')->createDbTable();
			}

			if(!Application::getConnection(\Sonek\D7\Book2Table::getConnectionName())->isTableExists(
				Base::getInstance('\Sonek\D7\Book2Table')->getDBTableName()
			)
			)
			{
				Base::getInstance('\Sonek\D7\Book2Table')->createDbTable();
			}
			if(!Application::getConnection(\Sonek\D7\AuthorTable::getConnectionName())->isTableExists(
				Base::getInstance('\Sonek\D7\AuthorTable')->getDBTableName()
			)
			)
			{
				Base::getInstance('\Sonek\D7\AuthorTable')->createDbTable();
			}
			if(!Application::getConnection(\Sonek\D7\BookAuthorsUsTable::getConnectionName())->isTableExists(
				Base::getInstance('\Sonek\D7\BookAuthorsUsTable')->getDBTableName()
			)
			)
			{
				Base::getInstance('\Sonek\D7\BookAuthorsUsTable')->createDbTable();
			}
		}

		function UnInstallDB()
		{
			Loader::includeModule($this->MODULE_ID);

			Application::getConnection(\Sonek\D7\BookTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Sonek\D7\BookTable')->getDBTableName());
			Application::getConnection(\Sonek\D7\Book2Table::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Sonek\D7\Book2Table')->getDBTableName());
			Application::getConnection(\Sonek\D7\AuthorTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Sonek\D7\AuthorTable')->getDBTableName());
			Application::getConnection(\Sonek\D7\AuthorTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Sonek\D7\BookAuthorsUsTable')->getDBTableName());

			Option::delete($this->MODULE_ID);
		}

		function InstallEvents()
		{
			\Bitrix\Main\EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'TestEventD7', $this->MODULE_ID, '\Sonek\D7\Event', 'eventHandler');
		}

		function UnInstallEvents()
		{
			\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'TestEventD7', $this->MODULE_ID, '\Sonek\D7\Event', 'eventHandler');
		}

		function InstallFiles($arParams = array())
		{
			$path=$this->GetPath()."/install/components";

			if(\Bitrix\Main\IO\Directory::isDirectoryExists($path))
				CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
			else
				throw new \Bitrix\Main\IO\InvalidPathException($path);

			if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin'))
			{
				CopyDirFiles($this->GetPath() . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"); //если есть файлы для копирования
				if ($dir = opendir($path))
				{
					while (false !== $item = readdir($dir))
					{
						if (in_array($item,$this->exclusionAdminFiles))
							continue;
						file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item,
							'<'.'? require($_SERVER["DOCUMENT_ROOT"]."'.$this->GetPath(true).'/admin/'.$item.'");?'.'>');
					}
					closedir($dir);
				}
			}

			return true;
		}

		function UnInstallFiles()
		{
			\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/sonek/');

			if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
				DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/install/admin/', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
				if ($dir = opendir($path)) {
					while (false !== $item = readdir($dir)) {
						if (in_array($item, $this->exclusionAdminFiles))
							continue;
						\Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
					}
					closedir($dir);
				}
			}
			return true;
		}

		function DoInstall()
		{
			global $APPLICATION;

			if($this->isVersionD7())
			{
				\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

				$this->InstallDB();
				$this->InstallEvents();
				$this->InstallFiles();

				/** Работа с .settings.php **/
				$configuration = Conf\Configuration::getInstance();
				$sonek_module_d7 = $configuration->get('sonek_module_d7');
				$sonek_module_d7['install'] = $sonek_module_d7['install'] + 1;
				$configuration->add('sonek_module_d7', $sonek_module_d7);
				$configuration->saveConfiguration();
				/** ---------------------- **/
			}
			else
			{
				$APPLICATION->ThrowException(Loc::getMessage("SONEK_D7_INSTALL_ERROR_VERSION"));
			}

			$APPLICATION->IncludeAdminFile(Loc::getMessage("SONEK_D7_INSTALL_TITLE"), $this->GetPath()."/install/step.php");
		}

		function DoUninstall()
		{
			global $APPLICATION;

			$context = Application::getInstance()->getContext();
			$request = $context->getRequest();

			if ($request["step"] < 2)
			{
				$APPLICATION->IncludeAdminFile(Loc::getMessage("SONEK_D7_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
			}
			elseif ($request["step"] == 2)
			{
				$this->UnInstallFiles();
				$this->UnInstallEvents();
				if($request["savedata"] != "Y")
					$this->UnInstallDB();

				\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

				/** Работа с .settings.php **/
				$configuration = Conf\Configuration::getInstance();
				$sonek_module_d7 = $configuration->get('sonek_module_d7');
				$sonek_module_d7['uninstall'] = $sonek_module_d7['uninstall'] + 1;
				$configuration->add('sonek_module_d7', $sonek_module_d7);
				$configuration->saveConfiguration();
				/** ---------------------- **/

				$APPLICATION->IncludeAdminFile(Loc::getMessage("SONEK_D7_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
			}
		}

		function GetModuleRightList()
		{
			return [
				"reference_id" => ["D", "K", "S", "W"],
				"reference" => [
					"[D] ".Loc::getMessage('SONEK_D7_DENIED'),
					"[K] ".Loc::getMessage('SONEK_D7_READ_COMPONENT'),
					"[S] ".Loc::getMessage('SONEK_D7_WRITE_SETTINGS'),
					"[W] ".Loc::getMessage('SONEK_D7_FULL'),
				],
			];
		}
	}