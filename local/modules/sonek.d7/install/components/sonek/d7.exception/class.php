<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Sonek\D7\Division;

class d7Exception extends CBitrixComponent
{
    /**
     * проверяет подключение необходимых модулей
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('sonek.d7'))
            throw new Main\LoaderException(Loc::getMessage('ACADEMY_D7_MODULE_NOT_INSTALLED'));
    }

    function var1()
    {
        //$arResult = Division::divided(4,2);

        $arResult = Division::divided(4,0);

        return $arResult;
    }

    public function executeComponent()
    {
        try
        {
            $this -> includeComponentLang('class.php');

            $this -> checkModules();

            $this->arResult = $this->var1();

            $this->includeComponentTemplate();
        }
        catch (\Sonek\D7\DivisionError $e)
        {
            ShowError($e -> getMessage());
            var_dump($e -> getParameters1());
            var_dump($e -> getParameters2());
        }
    }
};