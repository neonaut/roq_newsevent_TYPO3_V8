<?php

namespace ROQUIN\RoqNewsevent\Domain\Repository;

/**
 * Copyright (c) 2012, ROQUIN B.V. (C), http://www.roquin.nl
 *
 * @author:         J. de Groot
 * @file:           EventRepository.php
 * @description:    News event Repository, extending functionality from the News Repository
 */

use GeorgRinger\News\Domain\Model\DemandInterface;
use GeorgRinger\News\Domain\Repository\NewsRepository;
use ROQUIN\RoqNewsevent\Domain\Dto\EventsDemand;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * @package TYPO3
 * @subpackage roq_newsevent
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventRepository extends NewsRepository
{
    /**
     * Returns the constraint to determine if a news event is active or not (archived)
     *
     * @param QueryInterface $query
     *
     * @return OrInterface $constraint
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function eventCreateIsActiveConstraint(QueryInterface $query): OrInterface
    {
        $constraint = $query->logicalOr([
            // future events:
            $query->greaterThan('tx_roqnewsevent_start', $GLOBALS['SIM_EXEC_TIME']),
            // current multiple day events:
            $query->logicalAnd([
                // has begun
                $query->lessThan('tx_roqnewsevent_start', $GLOBALS['SIM_EXEC_TIME']),
                // but is not finished
                $query->greaterThan('tx_roqnewsevent_end', $GLOBALS['SIM_EXEC_TIME']),
            ]),
        ]);

        return $constraint;
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface|EventsDemand $demand
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand)
    {
        $constraints = parent::createConstraintsFromDemand($query, $demand);

        if ($demand->eventIsEventAction) {
            // events only
            $constraints['eventsOnly'] = $query->equals('tx_roqnewsevent_is_event', 1);
            // the event must have an event start date
            $constraints['eventsWithStartOnly'] = $query->logicalNot($query->equals('tx_roqnewsevent_start', 0));

            // override archived filter
            if ($demand->getArchiveRestriction() === 'archived') {
                $constraints['archived'] = $query->logicalNot($this->eventCreateIsActiveConstraint($query));
            } elseif ($demand->getArchiveRestriction() === 'active') {
                $constraints['active'] = $this->eventCreateIsActiveConstraint($query);
            }
        }

        return $constraints;
    }
}
