<?php

namespace ROQUIN\RoqNewsevent\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use GeorgRinger\News\Domain\Model\News;

/**
 * Class Event
 *
 * @package roq_newsevent
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Event extends News
{
    /**
     * Is event
     *
     * @var boolean
     */
    protected $isEvent = false;

    /**
     * Event start date
     *
     * @var \DateTime
     */
    protected $eventStart;

    /**
     * Event end date
     *
     * @var \DateTime
     */
    protected $eventEnd;

    /**
     * Even location (City, County)
     *
     * @var string
     */

    protected $eventLocation = '';

    /**
     * Returns the isEvent
     *
     * @return boolean $isEvent
     */
    public function getIsEvent(): bool
    {
        return $this->isEvent;
    }

    /**
     * Sets the isEvent
     *
     * @param boolean $isEvent
     */
    public function setIsEvent(bool $isEvent)
    {
        $this->isEvent = $isEvent;
    }

    /**
     * Returns the boolean state of isEvent
     *
     * @return boolean
     */
    public function isIsEvent(): bool
    {
        return $this->getIsEvent();
    }

    /**
     * @return \DateTime|null
     */
    public function getEventStart()
    {
        return $this->eventStart;
    }

    /**
     * @return \DateTime|null
     */
    public function getEventStarttime()
    {
        if (null === $this->eventStart) {
            return null;
        }
        $dateTime = clone $this->eventStart;
        if ($this->dateTimeHasTimePortion($dateTime)) {
            $dateTime->setDate(0, 0, 0);
            return $dateTime;
        }
        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getEventStartdate()
    {
        if (null === $this->eventStart) {
            return null;
        }
        $dateTime = clone $this->eventStart;
        $dateTime->setTime(0, 0, 0);
        return $dateTime;
    }

    /**
     * @param \DateTime $eventStart
     */
    public function setEventStart(\DateTime $eventStart)
    {
        $this->eventStart = $eventStart;
    }

    /**
     * @return \DateTime|null
     */
    public function getEventEnd()
    {
        return $this->eventEnd;
    }

    /**
     * @return \DateTime|null
     */
    public function getEventEndtime()
    {
        if (null === $this->eventEnd) {
            return null;
        }

        $dateTime = clone $this->eventStart;
        if ($this->dateTimeHasTimePortion($dateTime)) {
            $dateTime->setDate(0, 0, 0);
            return $dateTime;
        }

        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getEventEnddate()
    {
        if (null === $this->eventEnd) {
            return null;
        }

        $dateTime = clone $this->eventEnd;
        $dateTime->setTime(0, 0);
        return $dateTime;
    }

    /**
     * @param \DateTime $eventEnd
     */
    public function setEventEnd(\DateTime $eventEnd)
    {
        $this->eventEnd = $eventEnd;
    }

    /**
     * Returns the eventLocation
     *
     * @return string $eventLocation
     */
    public function getEventLocation(): string
    {
        return $this->eventLocation;
    }

    /**
     * Sets the eventLocation
     *
     * @param string $eventLocation
     */
    public function setEventLocation(string $eventLocation)
    {
        $this->eventLocation = $eventLocation;
    }

    /**
     * Get year of event start
     *
     * @return integer|null
     */
    public function getYearOfEventStartdate()
    {
        return $this->getEventStart() !== null ? (int)$this->getEventStart()->format('Y') : null;
    }

    /**
     * Get month of event start
     *
     * @return integer|null
     */
    public function getMonthOfEventStartdate()
    {
        return $this->getEventStart() !== null ? (int)$this->getEventStart()->format('m') : null;
    }

    /**
     * Get day of event start
     *
     * @return integer|null
     */
    public function getDayOfEventStartdate()
    {
        return $this->getEventStart() !== null ? (int)$this->getEventStart()->format('d') : null;
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    protected function dateTimeHasTimePortion(\DateTime $dateTime): bool
    {
        return !empty(trim($dateTime->format('His'), '0'));
    }
}
