<?php

namespace Sonek\Event;

class event
{
    static public function eventHandler(\Bitrix\Main\Entity\Event $event)
    {
        $fields = $event->getParameter("fields");
	    echo'Обработчик события из модуля sonek.event
        ';
        _print_r($fields);
    }
}