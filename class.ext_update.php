<?php

use TYPO3\CMS\Backend\Module\BaseScriptClass;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Copyright (c) 2012, ROQUIN B.V. (C), http://www.roquin.nl
 *
 * @author:         Jochem de Groot <jochem@roquin.nl>
 * @file:           class.ext_update.php
 * @created:        26-9-12 16:28
 * @description:    Update class for updating news event from version 2.0.X to newer versions
 */
class ext_update extends BaseScriptClass
{
    /**
     * Main method that is called whenever UPDATE! menu was clicked. This method outputs the result of the update in
     * HTML
     *
     * @return string: HTML to display
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function main()
    {
        if ($this->canUpdateNewsType()) {
            $affectedRows = 0;
            $errorMessage = '';
            $this->content = '';
            $this->doc = GeneralUtility::makeInstance('noDoc');

            $this->doc->backPath = $GLOBALS['BACK_PATH'];

            if ($this->updateNewsEventRecords($errorMessage, $affectedRows) == 0) {
                $this->content .= $this->doc->section(
                    '',
                    'The update has been performed successfully: ' . $affectedRows . ' row(s) affected.'
                );
            } else {
                $this->content .= $this->doc->section(
                    '',
                    'An error occurred while preforming updates. Error: ' . $errorMessage
                );
            }

            return $this->content;
        } elseif ($this->canUpdateEventStartEndFields()) {
            $selectStatement = $this->getDatabase()->exec_SELECTquery(
                'uid,tx_roqnewsevent_start,tx_roqnewsevent_startdate,tx_roqnewsevent_starttime,tx_roqnewsevent_end,tx_roqnewsevent_enddate,tx_roqnewsevent_endtime',
                'tx_news_domain_model_news',
                'tx_roqnewsevent_start = 0 AND tx_roqnewsevent_end = 0 AND (tx_roqnewsevent_startdate != 0 OR tx_roqnewsevent_starttime != 0 OR tx_roqnewsevent_enddate != 0 OR tx_roqnewsevent_endtime != 0)'
            );
            while ($row = $selectStatement->fetch_assoc()) {
                $uid = (int)$row['uid'];
                $row['tx_roqnewsevent_start'] = $row['tx_roqnewsevent_startdate'] + $row['tx_roqnewsevent_starttime'];

                // HF-MOD: Start

                if( $row['tx_roqnewsevent_enddate'] > 0 && $row['tx_roqnewsevent_endtime'] >= 0) {
                $row['tx_roqnewsevent_end'] = $row['tx_roqnewsevent_enddate'] + $row['tx_roqnewsevent_endtime'];
                }

                if( $row['tx_roqnewsevent_enddate'] == 0 && $row['tx_roqnewsevent_endtime'] > 0) {
                    $row['tx_roqnewsevent_end'] = $row['tx_roqnewsevent_startdate'] + $row['tx_roqnewsevent_endtime'];
                }

                if( $row['tx_roqnewsevent_enddate'] == 0 && $row['tx_roqnewsevent_endtime'] == 0) {
                    $row['tx_roqnewsevent_end'] = 0;
                }

                // HF-MOD: End


                $this->getDatabase()->exec_UPDATEquery('tx_news_domain_model_news', 'uid=' . $uid, $row);
            }
            return sprintf('Update of %d rows successful', $selectStatement->num_rows);
        }
    }

    /**
     * Updates news type and news event tx_roqnewsevent_is_event attribute in database news event records
     *
     * @param $errorMessage : stores the error message, if an error has been occurred
     * @param $affectedRows : stores the affected rows, when the query has been executed
     * @return integer: returns error code (0 == success)
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function updateNewsEventRecords(&$errorMessage, &$affectedRows)
    {
        $errorCode = 0;
        $affectedRows = 0;
        $result = false;

        $result = $this->getDatabase()->exec_UPDATEquery(
            'tx_news_domain_model_news',
            "tx_news_domain_model_news.type LIKE 'Tx_RoqNewsevent_Event'",
            [
                'type' => 0,
                'tx_roqnewsevent_is_event' => 1,
            ]
        );

        if ($result) {
            $affectedRows = $this->getDatabase()->sql_affected_rows();
        } else {
            $errorCode = $this->getDatabase()->sql_errno();
            $errorMessage = 'Could not update table tx_news_domain_model_news. '
                            . $this->getDatabase()->sql_error()
                            . ' (Error code: '
                            . $errorCode
                            . ').';
        }

        return $errorCode;
    }

    /**
     * Check if the update is necessary, and whether the "UPDATE!" menu item should be shown.
     *
     * @return boolean: returns true if update should be performed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function access()
    {
        return $this->canUpdateNewsType() || $this->canUpdateEventStartEndFields();
    }

    /**
     * @return bool
     */
    protected function canUpdateNewsType()
    {
        $result = $this->getDatabase()->exec_SELECTquery(
            'tx_news_domain_model_news.type',
            'tx_news_domain_model_news',
            "tx_news_domain_model_news.type LIKE 'Tx_RoqNewsevent_Event'"
        );

        return ($result !== false) && ($this->getDatabase()->sql_num_rows($result) > 0);
    }

    /**
     * @return bool
     */
    protected function canUpdateEventStartEndFields()
    {
        $fields = $this->getDatabase()->admin_get_fields('tx_news_domain_model_news');
        // assume all old fields exist if one is there
        if (array_key_exists('tx_roqnewsevent_startdate', $fields)) {
            if (!array_key_exists('tx_roqnewsevent_start', $fields)) {
                $message = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    'Can not check for possible updates if new tables are missing. Please run Database Compare and add all missing fields',
                    'roq_eventnews: Missing Database Fields',
                    FlashMessage::WARNING,
                    true
                );
                GeneralUtility::makeInstance(FlashMessageService::class)
                              ->getMessageQueueByIdentifier(
                                  'extbase.flashmessages.tx_extensionmanager_tools_extensionmanagerextensionmanager'
                              )
                              ->addMessage($message);
            } else {
                $count = $this->getDatabase()->exec_SELECTcountRows(
                    'uid',
                    'tx_news_domain_model_news',
                    'tx_roqnewsevent_start = 0 AND tx_roqnewsevent_end = 0 AND (tx_roqnewsevent_startdate != 0 OR tx_roqnewsevent_starttime != 0 OR tx_roqnewsevent_enddate != 0 OR tx_roqnewsevent_endtime != 0)'
                );
                return $count > 0;
            }
        }
    }

    /**
     * @return DatabaseConnection
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getDatabase()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
