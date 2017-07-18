<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tmp_roq_newsevent_columns = [
    'tx_roqnewsevent_is_event' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_is_event',
        'config' => [
            'type' => 'check',
            'default' => 0,
        ],
    ],
    'tx_roqnewsevent_startdate' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_startdate',
        'config' => [
            'type' => 'input',
            'size' => 7,
            'eval' => 'date',
            'checkbox' => 1,
        ],
    ],
    'tx_roqnewsevent_starttime' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_starttime',
        'config' => [
            'type' => 'input',
            'size' => 4,
            'eval' => 'time',
            'checkbox' => 1,
        ],
    ],
    'tx_roqnewsevent_enddate' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_enddate',
        'config' => [
            'type' => 'input',
            'size' => 7,
            'eval' => 'date',
            'checkbox' => 1,
        ],
    ],
    'tx_roqnewsevent_endtime' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_endtime',
        'config' => [
            'type' => 'input',
            'size' => 4,
            'eval' => 'time',
            'checkbox' => 1,
        ],
    ],
    'tx_roqnewsevent_location' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_location',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    $tmp_roq_newsevent_columns
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    ',--div--;LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_domain_model_event,tx_roqnewsevent_is_event, tx_roqnewsevent_startdate, tx_roqnewsevent_starttime, tx_roqnewsevent_enddate, tx_roqnewsevent_endtime, tx_roqnewsevent_location'
);
