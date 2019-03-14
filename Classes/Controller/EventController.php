<?php

namespace ROQUIN\RoqNewsevent\Controller;

use GeorgRinger\News\Controller\NewsController;
use GeorgRinger\News\Domain\Model\Dto\NewsDemand;
use GeorgRinger\News\Domain\Model\News;
use GeorgRinger\News\Domain\Repository\NewsRepository;
use GeorgRinger\News\Utility\Page;
use NN\NnAddress\Domain\Model\DemandInterface;
use ROQUIN\RoqNewsevent\Domain\Dto\EventsDemand;
use ROQUIN\RoqNewsevent\Domain\Model\Event;
use ROQUIN\RoqNewsevent\Domain\Repository\EventRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Copyright (c) 2012, ROQUIN B.V. (C), http://www.roquin.nl
 *
 * @author:         J. de Groot
 * @file:           EventController.php
 * @description:    News event Controller, extending functionality from the News Controller
 */

/**
 * @package TYPO3
 * @subpackage roq_newsevent
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @property  array settings
 */
class EventController extends NewsController
{
    /** @var string $eventActionName */
    protected $eventActionName;

    /** @var string $eventClassName */
    protected $eventClassName;

    /** @var bool $eventIsEventAction */
    private $eventIsEventAction;

    /**
     * Inject a event repository to enable DI
     *
     * @param EventRepository $eventRepository
     */
    public function injectEventRepository(EventRepository $eventRepository)
    {
        $this->newsRepository = $eventRepository;
    }

    public function injectNewsRepository(NewsRepository $newsRepository)
    {
        // Do not use
    }

    /**
     * Overrides setViewConfiguration: Use event view configuration instead of news view configuration if an event
     * controller action is used
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function setViewConfiguration(ViewInterface $view)
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        // Fetch the current controller action which is set in the news plugin
        $controllerConfigurationAction = implode(
            ';',
            $extbaseFrameworkConfiguration['controllerConfiguration']['News']['actions']
        );

        parent::setViewConfiguration($view);

        // Check if the current controller configuration action matches with one of the event controller actions
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems'] as
                 $switchableControllerActions => $value
        ) {
            $action = str_replace('News->', '', $switchableControllerActions);

            if (strpos($action, $controllerConfigurationAction) !== false) {
                // the current controller configuration action matches with one of the event controller actions: set event view configuration
                $this->eventSetViewConfiguration($view);
            }
        }
    }

    /**
     * Override templateRootPath, layoutRootPath and/or partialRootPath of the news view with event specific settings
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function eventSetViewConfiguration(ViewInterface $view)
    {
        // Template Path Override
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        // set TemplateRootPaths
        $viewFunctionName = 'setTemplateRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $parameter = $this->eventGetViewProperty($extbaseFrameworkConfiguration, 'templateRootPaths');
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }

        // set LayoutRootPaths
        $viewFunctionName = 'setLayoutRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $parameter = $this->eventGetViewProperty($extbaseFrameworkConfiguration, 'layoutRootPaths');
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }

        // set PartialRootPaths
        $viewFunctionName = 'setPartialRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $parameter = $this->eventGetViewProperty($extbaseFrameworkConfiguration, 'partialRootPaths');
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }
    }

    /**
     * Handles the path resolving for *rootPath(s)
     *
     * numerical arrays get ordered by key ascending
     *
     * @param array $extbaseFrameworkConfiguration
     * @param string $setting parameter name from TypoScript
     *
     * @return mixed
     */
    protected function eventGetViewProperty(array $extbaseFrameworkConfiguration, string $setting)
    {
        if (!empty($extbaseFrameworkConfiguration['view']['event'][$setting])
            && is_array($extbaseFrameworkConfiguration['view']['event'][$setting])
        ) {
            return $extbaseFrameworkConfiguration['view']['event'][$setting];
        }

        return [];
    }

    /**
     * @param array $settings
     * @param string $class
     * @return EventsDemand|NewsDemand|DemandInterface
     */
    protected function createDemandObjectFromSettings($settings, $class = EventsDemand::class)
    {
        $demand = parent::createDemandObjectFromSettings($settings, $class); // TODO: Change the autogenerated stub

        if ($this->eventIsEventAction) {
            $demand->eventIsEventAction = true;
            $demand->eventActionName = $this->eventActionName;
            $demand->eventClassName = $this->eventClassName;

            $orderByAllowed = $demand->getOrderByAllowed();

            if ($orderByAllowed !== '') {
                $orderByAllowed .= ',';
            }

            // set ordering
            if ($settings['event']['orderByAllowed']) {
                $demand->setOrderByAllowed($orderByAllowed . str_replace(' ', '', $settings['event']['orderByAllowed']));
            } else {
                // default orderByAllowed list
                $demand->setOrderByAllowed($orderByAllowed . 'tx_roqnewsevent_start');
            }

            if ($demand->getArchiveRestriction() === 'archived') {
                if ($settings['event']['archived']['orderBy']) {
                    $demand->setOrder($settings['event']['archived']['orderBy']);
                } else {
                    // default ordering for archived events
                    $demand->setOrder('tx_roqnewsevent_start DESC');
                }
            } else if ($settings['event']['orderBy']) {
                $demand->setOrder($settings['event']['orderBy']);
            } else {
                // default ordering for active events
                $demand->setOrder('tx_roqnewsevent_start ASC');
            }

            if ($settings['event']['startingpoint']) {
                $demand->setStoragePage(
                    Page::extendPidListByChildren($settings['event']['startingpoint'], $settings['recursive'])
                );
            }

        }
        return $demand;
    }

    protected function eventHook(string $action, string $class)
    {
        $this->settings['dateField'] = $this->settings['event']['dateField'] ?? 'eventStart';
        $this->eventIsEventAction = true;
        $this->eventActionName = $action;
        $this->eventClassName = $class;
    }

    /**
     * Render a menu by dates, e.g. years, months or dates
     *
     * @param array $overwriteDemand
     * @return void
     */
    public function eventDateMenuAction(array $overwriteDemand = null)
    {
        $this->eventHook(__METHOD__, __CLASS__);
        $demand = $this->createDemandObjectFromSettings($this->settings);

        $eventRecords = $this->newsRepository->findDemanded($demand);

        $this->view->assignMultiple([
            'events' => $eventRecords,
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand,
            'categories' => null,
            'tags' => null,
            'settings' => $this->settings,
            'listPid' => $this->settings['listPid'] ?: $GLOBALS['TSFE']->id,
            'dateField' => $this->settings['dateField'],
        ]);
    }

    /**
     * Output a list view of news events
     *
     * @param array $overwriteDemand
     */
    public function eventListAction(array $overwriteDemand = null)
    {
        $this->eventHook(__METHOD__, __CLASS__);
        $this->listAction($overwriteDemand);
    }

    /**
     * Output a selected list view of news
     */
    public function eventSelectedListAction()
    {
        $this->eventHook(__METHOD__, __CLASS__);
        $this->selectedListAction();
    }

    /**
     * Single view of a news event record
     *
     * @param Event|News $news news item
     * @param int $currentPage current page for optional pagination
     */
    public function eventDetailAction(Event $news = null, $currentPage = 1)
    {
        $this->eventHook(__METHOD__, __CLASS__);
        $this->detailAction($news, $currentPage);
    }
}
