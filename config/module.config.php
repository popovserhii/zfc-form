<?php
namespace Popov\ZfcForm;

use Popov\ZfcCore\Service;
use Popov\ZfcForm\Factory\FormElementManagerFactory;

return [

    'assetic_configuration' => require_once('assets.config.php'),

    /*'assets_bundle' => [
        'assets' => [
            'js' => [
                __DIR__ . '/../view/public/js/form.js',
            ],
        ]
    ],*/

	'form_elements' => [
		'factories' => [
			//'Popov\Invoice\Form\ProductCityFieldset' => 'Popov\Form\Factory\FieldsetFactory',
			//'Popov\Invoice\Form\InvoiceForm' => 'Popov\Form\Factory\FormFactory',
		],

		'abstract_factories' => [
			\Popov\ZfcForm\Factory\FormFactory::class,
			\Popov\ZfcForm\Factory\FieldsetFactory::class,
		],

        'initializers' => [
            'ConfigAwareInterface' => Service\Factory\ConfigInitializer::class,
            'DomainServiceInitializer' => Service\Factory\DomainServiceInitializer::class,
            'ObjectManagerAwareInterface' => Service\Factory\ObjectManagerInitializer::class,
        ],
	],

	'dependencies' => [
		'invokables' => [
			'Zend\InputFilter\InputFilter' => 'Zend\InputFilter\InputFilter',
		],
        'factories' => [
            FormElementManager::class => FormElementManagerFactory::class,
        ],
		'shared' => [
			'Zend\InputFilter\InputFilter' => false,
		],
		'abstract_factories' => [
			/** @link http://framework.zend.com/manual/current/en/modules/zend.form.advanced-use-of-forms.html#handling-dependencies */
			//'Popov\Form\Factory\FormFactory',
			//'Popov\Form\Factory\FieldsetFactory',
		],

		//'delegators' => [
        //    'Popov\Invoice\Form\ProductCityFieldset' => [
        //        'Popov\Invoice\Form\Factory\InvoiceFormDelegatorFactory',
        //        // can add more of these delegator factories here
        //    ],
        //],
	],


    'templates' =>  [
        'paths' => [
            'form'  => [__DIR__ . '/../view/partial'],
        ],
    ],

];