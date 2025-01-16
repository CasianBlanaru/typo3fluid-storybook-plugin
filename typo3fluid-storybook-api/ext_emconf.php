
<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Storybook Fluid API',
    'description' => 'Provides an API to render TYPO3 Fluid templates and partials through HTTP/HTTPS requests.',
    'category' => 'plugin',
    'state' => 'stable',
    'author' => 'Blanaru Casian',
    'author_email' => 'casianus@me.com',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
    ],
];
