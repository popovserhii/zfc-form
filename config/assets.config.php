<?php
namespace Popov\ZfcForm;

return [

    'default' => [
        'assets' => [
            '@form_js',
        ],
    ],

    'modules' => [
        'form' => [
            'root_path' => __DIR__ . '/../view/assets',
            'collections' => [
                'form_js' => [
                    'assets' => [
                        'js/form.js',
                    ],
                ],
            ],
        ],
    ],
];
