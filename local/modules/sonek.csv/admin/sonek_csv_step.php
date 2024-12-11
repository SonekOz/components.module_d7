<?php
$IBLOCK_ID=2;
$URL="/upload/academy_import.csv";
$step_element=10;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!$USER->CanDoOperation('edit_php'))
    $APPLICATION->AuthForm('Доступ запрещен');

    CModule::IncludeModule("iblock");

class CSV {

    private $_csv_file = null;

    /**
     * @param string $csv_file  - путь до csv-файла
     */
    public function __construct($csv_file) {
        if (file_exists($csv_file)) { //Если файл существует
            $this->_csv_file = $csv_file; //Записываем путь к файлу в переменную
        }
        else { //Если файл не найден то вызываем исключение
            throw new Exception("Файл \"$csv_file\" не найден");
        }
    }

    /**
     * Метод для чтения из csv-файла. Возвращает массив с данными из csv
     * @return array;
     */
    public function getCSVStep($step=0,$step_element=10) {
        $handle = fopen($this->_csv_file, "r"); //Открываем csv для чтения

        $array_line_full = array(); //Массив будет хранить данные из csv
        //Проходим весь csv-файл, и читаем построчно. 3-ий параметр разделитель поля
        $array_title = array();//Массив будет хранить заголовки
        $i=0;
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {

            if(!$array_title)
            {
                $array_title=$line;
            }
            else
            {
                if($step<=$i && ($step+$step_element)>$i)
                {
                    foreach($line as $cell=>$value)
                    {
                        if($array_title[$cell])
                        {
                            $line[$array_title[$cell]]=$value;
                            unset($line[$cell]);
                        }
                    }
                    $array_line_full[] = $line; //Записываем строчки в массив
                }
                $i++;
            }
        }
        fclose($handle); //Закрываем файл
        return $array_line_full; //Возвращаем прочтенные данные
    }

}


?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
if($_REQUEST['step']):
    $csv = new CSV($_SERVER["DOCUMENT_ROOT"].$URL); //Открываем наш csv
    /**
     * Чтение из CSV
     */
    if($_REQUEST["step"]=='start') $_REQUEST["step"]=0;
    $get_csv = $csv->getCSVStep($_REQUEST["step"],$step_element);

    $arFilter["IBLOCK_ID"]=$IBLOCK_ID;

    foreach($get_csv as $value)
    {
        $arFilter["PROPERTY_ARTNUMBER"][]=$value["ARTNUMBER"];
    }
    $res = CIBlockElement::GetList(Array("PROPERTY_ARTNUMBER"=>"ASC"), $arFilter, false,false,array('ID','PROPERTY_ARTNUMBER'));

    while($ar_fields = $res->GetNext())
    {
        $element[$ar_fields["PROPERTY_ARTNUMBER_VALUE"]][]=$ar_fields;
    }

    $el = new CIBlockElement;

    $arResult=NULL;
    foreach($get_csv as $csv_element)
    {

        if($element[$csv_element["ARTNUMBER"]])
        {

            foreach($element[$csv_element["ARTNUMBER"]] as $ib_element)
            {

                $arLoadProductArray = Array(
                    "DETAIL_PICTURE" => CFile::MakeFileArray($csv_element["PICTURE"]),
                );

                    if($el->Update($ib_element['ID'], $arLoadProductArray,false,true,true))
                        $arResult.= "<br>Обновлен элемент : ".$ib_element['ID'];
                    else
                        $arResult.= "<br>Ошибка обновления в элемент ".$ib_element['ID'].": ".$el->LAST_ERROR;
            }
        }
    }

    if(count($get_csv)>1)
    {
        @set_time_limit(0);
        CAdminMessage::ShowMessage(array("MESSAGE"=>'Загрузка элементов с '.$_REQUEST["step"].' по '.($_REQUEST["step"]+$step_element), "TYPE"=>"OK", "HTML"=>true, "DETAILS"=>$arResult));
        ?><script>setTimeout("Obmen('<?echo CUtil::JSEscape($_REQUEST["step"]+$step_element)?>')", 100);</script>
        <?
    }
    else
    {
        CAdminMessage::ShowMessage(array("MESSAGE"=>'Обмен завершен', "TYPE"=>"OK", "HTML"=>true,"DETAILS"=>'Все элементы были загружены'));
        ?>
        <script>
            document.getElementById('opt_start').disabled = false;
        </script>
    <?
    };
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else:
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <form action="" method="post">
        <input type="button" name="csv" id="opt_start" value="Произвести обмен" OnClick="Obmen('start');">
    </form>
    <div id="sonek_csv">

    </div>
    <script>

        function Obmen(step)
        {

                CHttpRequest.Action = function(result)
                {
                    CloseWaitWindow();
                    document.getElementById('sonek_csv').innerHTML = result;
                };
                ShowWaitWindow();
                if(step == 'start')
                {
                    document.getElementById('opt_start').disabled = true;
                }
                var url = 'sonek.csv_sonek_csv_step.php?lang=<?echo LANGUAGE_ID?>&<?echo bitrix_sessid_get()?>&step='+step;
                CHttpRequest.Send(url);
        }
    </script>
<?
    require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
endif;
