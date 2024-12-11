<?
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class sonek_event extends CModule
{
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__."/version.php");

        $this->MODULE_ID = 'sonek.event';
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("SONEK_EVENT_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("SONEK_EVENT_MODULE_DESC");

		$this->PARTNER_NAME = Loc::getMessage("SONEK_EVENT_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("SONEK_EVENT_PARTNER_URI");
	}

    //Определяем место размещения модуля
    public function GetPath($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace($_SERVER["DOCUMENT_ROOT"],'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    //Проверяем что система поддерживает D7
    public function isVersionD7()
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    function InstallDB()
    {
        return true;
    }

    function UnInstallDB()
    {
        return true;
    }

	function InstallEvents()
	{
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler('sonek.d7', '\Sonek\D7\Book::OnBeforeAdd', $this->MODULE_ID, '\Sonek\Event\Event', 'eventHandler');
	}

	function UnInstallEvents()
	{
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler('sonek.d7', '\Sonek\D7\Book::OnBeforeAdd', $this->MODULE_ID, '\Sonek\Event\Event', 'eventHandler');
	}

	function InstallFiles()
	{
        return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
        if($this->isVersionD7())
        {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage("SONEK_EVENT_INSTALL_ERROR_VERSION"));
        }
	}

	function DoUninstall()
	{
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();
	}
}
?>