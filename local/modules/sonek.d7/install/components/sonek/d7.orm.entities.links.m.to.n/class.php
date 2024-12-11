<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Sonek\D7\BookAuthorsUsTable;
use \Sonek\D7\Book2Table;

class d7Orm11 extends CBitrixComponent
{

    /**
     * проверяет подключение необходиимых модулей
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('sonek.d7'))
            throw new Main\LoaderException(Loc::getMessage('SONEK_D7_MODULE_NOT_INSTALLED'));
    }

    function var1()
    {
        $result = Book2Table::getList(array(
            'select'  => array(
				'NAME',
	            'AUTHOR_NAME' => '\Sonek\D7\BookAuthorsUsTable:BOOK.AUTHOR.NAME',
	            'AUTHOR_LAST_NAME' => '\Sonek\D7\BookAuthorsUsTable:BOOK.AUTHOR.LAST_NAME'),
        ));

        return $result->fetchAll();
    }


    public function executeComponent()
    {
        $this -> includeComponentLang('class.php');

        $this -> checkModules();

        $this -> arResult = $this->var1();

        $this->includeComponentTemplate();
    }
};