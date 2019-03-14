<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    [
        'tx_roqnewsevent_is_event' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_is_event',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'tx_roqnewsevent_start' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_start',
            'config' => [
                'renderType' => 'inputDateTime',
                'type' => 'input',
                'eval' => 'datetime',
                'checkbox' => 1,
            ],
        ],
        'tx_roqnewsevent_end' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_end',
            'config' => [
                'renderType' => 'inputDateTime',
                'type' => 'input',
                'eval' => 'datetime',
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
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    ',' .
    '--div--;LLL:EXT:roq_newsevent/Resources/Private/Language/locallang_db.xml:tx_roqnewsevent_domain_model_event,' .
    'tx_roqnewsevent_is_event,' .
    'tx_roqnewsevent_start,' .
    'tx_roqnewsevent_end,' .
    'tx_roqnewsevent_location'
);
