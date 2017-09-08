<?php
/**
 * Global Fieldset Factory
 *
 * @category Popov
 * @package Popov_Form
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.02.15 22:24
 */

namespace Popov\ZfcForm\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\Exception;
use Zend\I18n\Translator\TranslatorAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Popov\Current\Plugin\Current;

class FormFactory implements AbstractFactoryInterface {

	public function canCreateServiceWithName(ServiceLocatorInterface $sm, $name, $requestedName) {
		//\Zend\Debug\Debug::dump([debug_backtrace()[1]['class'], debug_backtrace()[1]['function'], (substr($requestedName, -4) === 'Form'), $requestedName]);

		return (substr($requestedName, -4) === 'Form');
	}

	public function createServiceWithName(ServiceLocatorInterface $fm, $name, $requestedName) {
		if (!class_exists($requestedName)) {
			throw new Exception\ServiceNotFoundException(sprintf(
				'%s: failed retrieving "%s%s"; class does not exist',
				get_class($this) . '::' . __FUNCTION__,
				$requestedName,
				($name ? '(alias: ' . $name . ')' : '')
			));
		}

		/** @var FormElementManager $fm */
		$sm = $fm->getServiceLocator();
        $om = $sm->get('Doctrine\ORM\EntityManager');
        $cpm = $sm->get('ControllerPluginManager');
        /** @var Current $currentPlugin */
        $currentPlugin = $cpm->get('current');
		//die(__METHOD__);

		/** @var Form $form (type of) */
		$form = new $requestedName();
		$form->setAttribute('method', 'post')
			->setHydrator(new DoctrineHydrator($om))
            // this injects automatically on vendor/zendframework/zend-form/src/Form.php:691
			# ->setInputFilter($sm->get('Zend\InputFilter\InputFilter'))
			//->init() // @todo: This must call automatically but not work http://stackoverflow.com/a/29503188
		;

        if ($form instanceof TranslatorAwareInterface) {
            $form->setTranslator($sm->get('translator', $currentPlugin->currentModule($form)));
        }

		return $form;
	}

}