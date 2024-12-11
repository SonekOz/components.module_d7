<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Sonek\D7\BookTable;

class d7OrmAdd extends CBitrixComponent
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

	/** Корректное добавление записи **/
    function var1()
    {
        $result = BookTable::add(array(
            'NAME' => 'Book 1',
            'RELEASED' => '2024',
	        'ISBN' => '556-4512851403',
	        'AUTHOR' => 'Alexander Osipov',
	        'TIME_ARRIVAL' => new Type\DateTime('23.10.2024 00:00:00'),
	        'DESCRIPTION' => 'its just desc'
        ));

        return $result;
    }

    /** Добавление записи без обязательного поля "Название". **/
    function var2()
    {
        $result = BookTable::add(array(
            'RELEASED' => '2024',
            'ISBN' => '556-4512851403',
            'AUTHOR' => 'Alexander Osipov',
            'TIME_ARRIVAL' => new Type\DateTime('23.10.2024 00:00:00'),
            'DESCRIPTION' => 'its just desc'
        ));

        return $result;
    }

    /** Добавление записи без указания поля, для которого установлено значение по умолчанию **/
    function var3()
    {
        $result = BookTable::add(array(
            'NAME' => 'Book 1',
	        'RELEASED' => '2024',
	        'ISBN' => '556-4512851403',
	        'AUTHOR' => 'Alexander Osipov',
	        'DESCRIPTION' => 'its just desc'
        ));

        return $result;
    }

    public function executeComponent()
    {
        $this -> includeComponentLang('class.php');

        $this -> checkModules();

        //все верно
        //$result = $this->var1();

        //Не указал обязательное поле: название
        //$result = $this->var2();

        /** Добавление используя поле по умолчанию. **/
        $result = $this->var3();

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