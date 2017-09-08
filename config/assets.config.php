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
                'base_css' => [
                    'assets' => [
                        /*'css/test.css',*/
                    ],
                    'filters' => [
                        'CssRewriteFilter' => [
                            'name' => \Assetic\Filter\CssRewriteFilter::class,
                        ],
                    ],
                ],
                'form_js' => [
                    'assets' => [
                        'js/form.js',
                    ],
                ],
            ],
        ],
    ],
];
