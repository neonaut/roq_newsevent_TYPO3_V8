<?php
namespace ROQUIN\RoqNewsevent\Controller;

use GeorgRinger\News\Controller\NewsController;
use GeorgRinger\News\Domain\Model\Dto\NewsDemand;
use GeorgRinger\News\Utility\Page;
use ROQUIN\RoqNewsevent\Domain\Model\Event;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
 */
class EventController extends NewsController
{
    /**
     * eventRepository
     *
     * @var \ROQUIN\RoqNewsevent\Domain\Repository\EventRepository
     * @inject
     */
    protected $eventRepository;

    /**
     * Initializes the settings
     *
     * @param array $settings
     * @return array $settings
     */
    protected function initializeSettings($settings)
    {
        if (isset($settings['event']['dateField'])) {
            $settings['dateField'] = $settings['event']['dateField'];
        } else {
            $settings['dateField'] = 'eventStart';
        }

        return $settings;
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
                $this->setEventViewConfiguration($view);
            }
        }
    }

    /**
     * Override templateRootPath, layoutRootPath and/or partialRootPath of the news view with event specific settings
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function setEventViewConfiguration(ViewInterface $view)
    {
        // Template Path Override
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        // set TemplateRootPaths
        $viewFunctionName = 'setTemplateRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $setting = 'templateRootPaths';
            $parameter = $this->getEventViewProperty($extbaseFrameworkConfiguration, $setting);
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }

        // set LayoutRootPaths
        $viewFunctionName = 'setLayoutRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $setting = 'layoutRootPaths';
            $parameter = $this->getEventViewProperty($extbaseFrameworkConfiguration, $setting);
            // no need to bother if there is nothing to set
            if ($parameter) {
                $view->$viewFunctionName($parameter);
            }
        }

        // set PartialRootPaths
        $viewFunctionName = 'setPartialRootPaths';
        if (method_exists($view, $viewFunctionName)) {
            $setting = 'partialRootPaths';
            $parameter = $this->getEventViewProperty($extbaseFrameworkConfiguration, $setting);
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
     * @return array
     */
    protected function getEventViewProperty($extbaseFrameworkConfiguration, $setting)
    {
        $values = [];
        if (!empty($extbaseFrameworkConfiguration['view']['event'][$setting])
            && is_array($extbaseFrameworkConfiguration['view']['event'][$setting])
        ) {
            $values = $extbaseFrameworkConfiguration['view']['event'][$setting];
        }

        return $values;
    }

    /**
     * Create the demand object which define which records will get shown
     *
     * @param array $settings
     * @return NewsDemand
     */
    protected function eventCreateDemandObjectFromSettings($settings)
    {
        $demand = parent::createDemandObjectFromSettings($settings);
        $orderByAllowed = $demand->getOrderByAllowed();

        if (strlen($orderByAllowed) > 0) {
            $orderByAllowed .= ',';
        }

        // set ordering
        if ($settings['event']['orderByAllowed']) {
            $demand->setOrderByAllowed($orderByAllowed . str_replace(' ', '', $settings['event']['orderByAllowed']));
        } else {
            // default orderByAllowed list
            $demand->setOrderByAllowed($orderByAllowed . 'tx_roqnewsevent_start');
        }

        if ($demand->getArchiveRestriction() == 'archived') {
            if ($settings['event']['archived']['orderBy']) {
                $demand->setOrder($settings['event']['archived']['orderBy']);
            } else {
                // default ordering for archived events
                $demand->setOrder('tx_roqnewsevent_start DESC');
            }
        } else {
            if ($settings['event']['orderBy']) {
                $demand->setOrder($settings['event']['orderBy']);
            } else {
                // default ordering for active events
                $demand->setOrder('tx_roqnewsevent_start ASC');
            }
        }

        if ($settings['event']['startingpoint']) {
            $demand->setStoragePage(
                Page::extendPidListByChildren($settings['event']['startingpoint'], $settings['recursive'])
            );
        }

        return $demand;
    }

    /**
     * Render a menu by dates, e.g. years, months or dates
     *
     * @param array $overwriteDemand
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function eventDateMenuAction(array $overwriteDemand = null)
    {
        $this->settings = $this->initializeSettings($this->settings);
        $demand = $this->eventCreateDemandObjectFromSettings($this->settings);

        $eventRecords = $this->eventRepository->findDemanded($demand);

        if (!$dateField = $this->settings['dateField']) {
            $dateField = 'eventStart';
        }

        $this->view->assignMultiple(
            [
                'listPid' => ($this->settings['listPid'] ? $this->settings['listPid'] : $GLOBALS['TSFE']->id),
                'dateField' => $dateField,
                'events' => $eventRecords,
                'overwriteDemand' => $overwriteDemand,
            ]
        );
    }

    /**
     * Output a list view of news events
     *
     * @param array $overwriteDemand
     * @return string the Rendered view
     */
    public function eventListAction(array $overwriteDemand = null)
    {
        $this->settings = $this->initializeSettings($this->settings);
        $demand = $this->eventCreateDemandObjectFromSettings($this->settings);

        if ($this->settings['disableOverrideDemand'] != 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        $newsRecords = $this->eventRepository->findDemanded($demand);

        $this->view->assignMultiple(
            [
                'news' => $newsRecords,
                'overwriteDemand' => $overwriteDemand,
            ]
        );
    }

    /**
     * Single view of a news event record
     *
     * @param Event $event
     * @param integer $currentPage current page for optional pagination
     * @return void
     */
    public function eventDetailAction(Event $event = null, $currentPage = 1)
    {
        $this->settings = $this->initializeSettings($this->settings);

        if (is_null($event)) {
            if ((int)$this->settings['singleNews'] > 0) {
                $previewNewsId = $this->settings['singleNews'];
            } elseif ($this->request->hasArgument('news_preview')) {
                $previewNewsId = $this->request->getArgument('news_preview');
            } else {
                $previewNewsId = $this->request->getArgument('news');
            }

            if ($this->settings['previewHiddenRecords']) {
                $event = $this->eventRepository->findByUid($previewNewsId, false);
            } else {
                $event = $this->eventRepository->findByUid($previewNewsId);
            }
        }

        if (is_null($event) && isset($this->settings['detail']['errorHandling'])) {
            $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
        }

        $this->view->assignMultiple(
            [
                'newsItem' => $event,
                'currentPage' => (int)$currentPage,
            ]
        );

        Page::setRegisterProperties($this->settings['detail']['registerProperties'], $event);
    }
}
