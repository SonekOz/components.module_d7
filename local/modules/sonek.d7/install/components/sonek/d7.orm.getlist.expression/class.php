<?php
use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \Sonek\D7\BookTable;

class d7OrmGetlistExpression extends CBitrixComponent
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
        $result = BookTable::getList(array(
            'select' => array('CNT'),
            'runtime' => array(
                new Main\Entity\ExpressionField('CNT', 'COUNT(*)')
            ),
        ));

        return $result->fetch();
    }

    function var2()
    {
        $result = BookTable::getList(array(
            'select' => array(
                new Main\Entity\ExpressionField('CNT', 'COUNT(*)')
            ),
        ));

        return $result->fetch();
    }

    function var3()
    {
        $result = BookTable::getList(array(
            'select' => array(
                'ID','NAME', 'ACTIVITY'
            ),
            'filter'  => array('ACTIVITY' => 1),
            'runtime' => array(
                new Main\Entity\IntegerField('ACTIVITY'),
            )
        ));

        return $result->fetchAll();
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

	    if($this->arParams['VAR_PICK'] === 'var3')
	    {
		    $result = $this->var3();
	    }
        $this->includeComponentTemplate();
    }
};