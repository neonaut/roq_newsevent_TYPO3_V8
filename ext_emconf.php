<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'News event (HF-MOD)',
    'description' => 'Event extension based on the versatile news system. Supplies additional event functionality to news records.',
    'category' => 'plugin',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'ROQUIN B.V., Oliver Eglseder, Sacha P. Suter',
    'author_email' => 'extensions@roquin.nl, oliver.eglseder@in2code.de, support@hausformat.com',
    'author_company' => 'ROQUIN B.V., in2code GmbH, .hausformat',
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.11-8.7.99',
            'news' => '5.0.0-6.0.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
