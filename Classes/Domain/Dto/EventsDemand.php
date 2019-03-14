<?php
namespace ROQUIN\RoqNewsevent\Domain\Dto;

use GeorgRinger\News\Domain\Model\Dto\NewsDemand;

class EventsDemand extends NewsDemand
{
    public $eventIsEventAction;
    public $eventActionName;
    public $eventClassName;
}
