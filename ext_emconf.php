<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'News event for TYPO3 8/news 7',
    'description' => 'Event extension based on the versatile news system. Supplies additional event functionality to news records.',
    'category' => 'plugin',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'ROQUIN B.V., Oliver Eglseder, Sacha P. Suter, Paul Golmann',
    'author_email' => 'extensions@roquin.nl, oliver.eglseder@in2code.de, support@hausformat.com, p.golmann@neonaut.de',
    'author_company' => 'ROQUIN B.V., in2code GmbH, .hausformat, Neonaut GmbH',
    'version' => '7.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.00-8.7.99',
            'news' => '7.0.0-7.0.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
