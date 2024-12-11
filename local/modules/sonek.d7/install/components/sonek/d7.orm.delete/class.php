<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Sonek\D7\BookTable;

class d7OrmDelete extends CBitrixComponent
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
	    /** Удаление записи (нужно указать верный id) **/
		$book_id = 11;
		if (BookTable::getById($book_id)){
			$result = BookTable::delete($book_id);

			return $result;
		} else {
			return 'Такого ID не существует :(';
		}
    }


    public function executeComponent()
    {
        $this -> includeComponentLang('class.php');

        $this -> checkModules();

        $result = $this->var1();

        if ($result->isSuccess())
        {
            $this->arResult='Запись была удалена';
        }
        else
        {
            $error=$result->getErrorMessages();
            $this->arResult='Произошла ошибка при удалении: <pre>'.var_export($error,true).'</pre>';
        }

        $this->includeComponentTemplate();
    }
};