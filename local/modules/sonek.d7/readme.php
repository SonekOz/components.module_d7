<?php
/** Дополнительные API классы для работы с модулями в D7 **/
/**
 * \Bitrix\Main\IO - класс для работами с файлами, папками, путями и тп. Наследует 3 класса:
 ** \Bitrix\Main\IO\Path - класс для работы с путями
 ** \Bitrix\Main\IO\Directory - класс для работы с папками (копирование/перемещение/удаление)
 ** \Bitrix\Main\IO\File - класс для работы с файлами

 * \Bitrix\Main\Web\HttpClient - класс для работы с HTTP. Содержит методы, которые управляют запросы на удаленные сервера. Содержит только динамические методы, например:
 * $http = new \Bitrix\Main\Web\HttpClient(array $options = null);
 ** $http->get($url) - гет запрос
 ** $http->post($url, $postData) - пост запрос
 ** $http->setCookies($cookies) - установка нужных куки
 ** $http->setAuthorization($user, $pass) - позволяет авторизоваться, если сервер требует удаленной авторизации
 ** $http->download($url, $filePath) - метод загрузки файла с удаленного сервера

 * \Bitrix\Main\Type\Date - класс для работы с датой

 * \Bitrix\Main\Type\DateTime - класс для работы с датой и временем

 * \Bitrix\Main\Web\Uri - класс для работы с URL. Позволяет разбирать URL и получать те или иные параметры, например, имя домена
**/