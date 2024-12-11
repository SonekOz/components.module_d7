<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Sonek\D7\BookTable;

class d7OrmValidator extends CBitrixComponent
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
        $result = BookTable::add(array(
            'NAME' => 'Книга для теста',
            'RELEASED' => '2002',
            'ISBN' => '00000000001',
            'AUTHOR' => 'Сергей Покоев',
            'TIME_ARRIVAL' => new Type\DateTime('04.09.2015 00:00:00'),
            'DESCRIPTION' => 'тестовый текст
            вторая строчка'
        ));

        return $result;
    }

    function var2()
    {
        $result = BookTable::add(array(
            'NAME' => 'TestBook 1',
            'RELEASED' => '2024',
            'ISBN' => '00000 00000 00000 22 33',
            'AUTHOR' => 'Alexander Osipov',
            'TIME_ARRIVAL' => new Type\DateTime('24.10.2024 13:07:00'),
            'DESCRIPTION' => 'It\'s just text'
        ));

        return $result;
    }

    public function executeComponent()
    {
        $this -> includeComponentLang('class.php');

        $this -> checkModules();

	    if($this->arParams['VAR_PICK'] === 'var1')
	    {
		    $result = $this->var1();
	    }

	    if($this->arParams['VAR_PICK'] === 'var2')
	    {
		    $result = $this->var2();
	    }

        if ($result->isSuccess())
        {
            $id = $result->getId();
            $this->arResult='Запись добавлена с id: '.$id;
        }
        else
        {
            $error=$result->getErrorMessages();
            $this->arResult='Произошла ошибка при добавлении: <pre>'.var_export($error,true).'</pre>';
        }

        $this->includeComponentTemplate();
    }
};