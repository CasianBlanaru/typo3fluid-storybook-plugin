<?php

declare(strict_types=1);

use PixelCoda\TYPO3StorybookApi\Middleware\StorybookApi;

return [
    'frontend' => [
        'pixelcoda/render' => [
            'target' => StorybookApi::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
        ],
    ],
];
