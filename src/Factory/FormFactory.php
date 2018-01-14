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

use Interop\Container\ContainerInterface;
//use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\Exception;
use Zend\I18n\Translator\TranslatorAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Popov\ZfcCurrent\Plugin\Current;

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
        $om = $container->get('Doctrine\ORM\EntityManager');
		//die(__METHOD__);

		/** @var Form $form (type of) */
		$form = new $requestedName();
		$form->setAttribute('method', 'post')
			->setHydrator(new DoctrineHydrator($om))
            // this injects automatically on vendor/zendframework/zend-form/src/Form.php:691
			# ->setInputFilter($sm->get('Zend\InputFilter\InputFilter'))
			//->init() // @todo: This must call automatically but not work http://stackoverflow.com/a/29503188
		;

        /*if ($form instanceof TranslatorAwareInterface) {
            $cpm = $container->get('ControllerPluginManager');
            $currentPlugin = $cpm->get('current');
            $form->setTranslator($container->get('translator', $currentPlugin->currentModule($form)));
        }*/

        if ($form instanceof TranslatorAwareInterface) {
            $cpm = $container->get('ControllerPluginManager');
            $modulePlugin = $cpm->get('module');

            /** @var \Zend\Mvc\I18n\Translator $translator */
            $translator = $container->get('translator');
            $form->setTranslator($translator);
            $form->setTranslatorTextDomain($modulePlugin->setRealContext($form)->getRealModule()->getName());
        }

		return $form;
	}

}