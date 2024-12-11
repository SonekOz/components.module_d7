<?php

$_SERVER["DOCUMENT_ROOT"] = "/"; //Путь до корня сайта, так как $_SERVER["DOCUMENT_ROOT"] не доступен при запуске под cron

define("NO_KEEP_STATISTIC", true); //Запретит сбор статистики на данной странице.

define("NOT_CHECK_PERMISSIONS", true); //Отключить проверку прав на доступ к файлам и каталога

define("BX_CRONTAB", true); //Если данная константа инициализирована значением "true", то функция проверки агентов на запуск будет отбирать только те агенты для которых не критично количество их запусков

set_time_limit(0); //Снимаем ограничение на время исполнения скрипта

define("LANG", "ru"); //Устанавливаем язык сайта

$IBLOCK_ID=2;
$URL="/upload/academy_import.csv";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


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
    public function getCSV() {
        $handle = fopen($this->_csv_file, "r"); //Открываем csv для чтения

        $array_line_full = array(); //Массив будет хранить данные из csv
        //Проходим весь csv-файл, и читаем построчно. 3-ий параметр разделитель поля
        $array_title = array();//Массив будет хранить заголовки
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            if(!$array_title)
            {
                $array_title=$line;
            }
            else
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
        }
        fclose($handle); //Закрываем файл
        return $array_line_full; //Возвращаем прочтенные данные
    }

}

?>
<?
if(CModule::IncludeModule("iblock"))
{
    $csv = new CSV($_SERVER["DOCUMENT_ROOT"].$URL); //Открываем наш csv
    /**
     * Чтение из CSV
     */
    $get_csv = $csv->getCSV();
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
                        echo "<br>Обновлен элемент : ".$ib_element['ID'];
                    else
                        echo "<br>Ошибка обновления в элемент ".$ib_element['ID'].": ".$el->LAST_ERROR;
            }
        }
    }
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");