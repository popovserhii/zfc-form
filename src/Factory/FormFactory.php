<?php
/**
 * Global Fieldset Factory
 *
 * @category Popov
 * @package Popov_Form
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 17.02.15 22:24
 */

namespace Popov\ZfcForm\Factory;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Interop\Container\ContainerInterface;
//use Zend\ServiceManager\AbstractFactoryInterface;
use Popov\ZfcEntity\Helper\ModuleHelper;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\Exception;
use Zend\I18n\Translator\TranslatorAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Popov\ZfcCurrent\Plugin\CurrentHelper;

class FormFactory implements AbstractFactoryInterface
{
	public function canCreate(ContainerInterface$container, /*$name, */$requestedName) {
		return (substr($requestedName, -4) === 'Form');
	}

	public function __invoke(ContainerInterface $container, $requestedName, array $options = null/*$requestedName*/) {
		if (!class_exists($requestedName)) {
			throw new Exception\ServiceNotFoundException(sprintf(
				'%s: failed retrieving "%s"; class does not exist',
				get_class($this) . '::' . __FUNCTION__,
				$requestedName
				//($name ? '(alias: ' . $name . ')' : '')
			));
		}

		/** @var FormElementManager $fm */
		//$container = $fm->getServiceLocator();
		//die(__METHOD__);

		/** @var Form $form (type of) */
		$form = new $requestedName();
		$form->setAttribute('method', 'post');
        if ($form instanceof ObjectManagerAwareInterface) {
            $om = $container->get('Doctrine\ORM\EntityManager');
            $form->setHydrator(new DoctrineHydrator($om))
                // this injects automatically on vendor/zendframework/zend-form/src/Form.php:691
                # ->setInputFilter($sm->get('Zend\InputFilter\InputFilter'))
                //->init() // @todo: This must call automatically but not work http://stackoverflow.com/a/29503188
            ;
        }
        /*if ($form instanceof TranslatorAwareInterface) {
            $cpm = $container->get('ControllerPluginManager');
            $currentPlugin = $cpm->get('current');
            $form->setTranslator($container->get('translator', $currentPlugin->currentModule($form)));
        }*/

        if ($form instanceof TranslatorAwareInterface) {
            /** @var ModuleHelper $moduleHelper */
            $moduleHelper = $container->get(ModuleHelper::class);
            /** @var Translator $translator */
            $translator = $container->get(TranslatorInterface::class);
            $form->setTranslator($translator);
            $form->setTranslatorTextDomain($moduleHelper->setRealContext($form)->getModule()->getName());
        }

		return $form;
	}

}