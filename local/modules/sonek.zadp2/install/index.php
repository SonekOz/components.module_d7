<?php
IncludeModuleLangFile(__FILE__);
class sonek_zadp2 extends CModule
{
	const MODULE_ID = 'sonek.zadp2';
	var $MODULE_ID = 'sonek.zadp2';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = [];
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("SONEK_ZADP2_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SONEK_ZADP2_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("SONEK_ZADP2_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("SONEK_ZADP2_PARTNER_URI");

		$this->MODULE_SORT = 1;
	}

	function InstallDB($arParams = [])
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CSonekZadp2', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = [])
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CSonekZadp2', 'OnBuildGlobalMenu');
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = [])
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/local/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/local/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}

		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/lib'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if (file_exists($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/'.self::MODULE_ID))
					{
						file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/'.self::MODULE_ID.'/'.$item,
							'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/local/modules/'.self::MODULE_ID.'/lib/'.$item.'");?'.'>');
					}
					else
					{
						mkdir($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/'.self::MODULE_ID);
						file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/'.self::MODULE_ID.'/'.$item,
							'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/local/modules/'.self::MODULE_ID.'/lib/'.$item.'");?'.'>');
					}
				}
				closedir($dir);
			}
		}
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/local/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}

		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/lib'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					unlink($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/'.self::MODULE_ID.'/'.$item);
					rmdir($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/'.self::MODULE_ID);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
