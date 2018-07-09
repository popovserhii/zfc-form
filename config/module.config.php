<?php
namespace Popov\ZfcForm;

use Popov\ZfcCore\Service;
use Popov\ZfcForm\Factory\FormElementManagerFactory;

return [

    'assetic_configuration' => require_once('assets.config.php'),

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

	'form_elements' => [
		'factories' => [
			//'Popov\Invoice\Form\ProductCityFieldset' => 'Popov\Form\Factory\FieldsetFactory',
			//'Popov\Invoice\Form\InvoiceForm' => 'Popov\Form\Factory\FormFactory',
		],

		'abstract_factories' => [
			Factory\FormFactory::class,
			Factory\FieldsetFactory::class,
		],

        'initializers' => [
            'ConfigAwareInterface' => Service\Factory\ConfigInitializer::class,
            'DomainServiceInitializer' => Service\Factory\DomainServiceInitializer::class,
            'ObjectManagerAwareInterface' => Service\Factory\ObjectManagerInitializer::class,
        ],
	],

    // MVC
    'view_manager' => [
        'prefix_template_path_stack' => [
            'form::' => __DIR__ . '/../view/partial',
        ],
    ],

    // middleware
    'templates' =>  [
        'paths' => [
            'form'  => [__DIR__ . '/../view/partial'],
        ],
    ],

];