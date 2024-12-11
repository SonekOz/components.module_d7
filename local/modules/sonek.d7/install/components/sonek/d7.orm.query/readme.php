<?php
/** Список методов ENTITY\QUERY
 * select, group
 ** setSelect, setGroup - устанавливает массив с именами полей
 ** addSelect, addGroup - добавляет имя поля
 ** getSelect, getGroup - возвращает массив с именами полей

 * filter
 ** setFilter - устанавливает одно- или многомерный массив с описанием фильтра
 ** addFilter - добавляет один параметр фильтра со значением
 ** getFilter - возвращает текущее описание фильтра

 * order
 ** setOrder - устанавливает массив с именами полей и порядком сортировки
 ** addOrder - добавляет одно поле с порядком сортировки
 ** getOrder - возвращает текущее описание сортировки

 * limit/offset
 ** setLimit, setOffset - устанавливает значение
 ** getLimit, getOffset - возвращает текущее значение

 * runtime fields
 ** registerRuntimeField - регистрирует новое временное поле для исходной сущности
**/