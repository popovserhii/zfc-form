<?php
/**
 * Fieldset Factory Trait
 *
 * @category Popov
 * @package Popov_Form
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.02.15 22:24
 */

namespace Popov\ZfcForm\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Form\FormElementManager;
use Zend\Form\Fieldset;
use Zend\ServiceManager\Exception;
use Zend\Stdlib\Exception\BadMethodCallException;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

use Popov\ZfcCurrent\Plugin\Current;

trait FieldsetFactoryTrait {

	public function canCreateServiceWithName(ServiceLocatorInterface $fm, $name, $requestedName) {
		//\Zend\Debug\Debug::dump([(substr($requestedName, -8) === 'Fieldset'), $requestedName]);
		return (substr($requestedName, -8) === 'Fieldset');
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
		$cpm = $sm->get('ControllerPluginManager');
		//$dm = $sm->get('doctrine.documentmanager.odm_default');
		$om = $sm->get('Doctrine\ORM\EntityManager');
        /** @var Current $currentPlugin */
        //$currentPlugin = $cpm->get('current');
        $modulePlugin = $cpm->get('module');


		/** @var Fieldset $fieldset (type of) */
		$fieldset = new $requestedName();

		if ($fieldset instanceof ObjectManagerAwareInterface) {
            //\Zend\Debug\Debug::dump($fieldset->getName());
            $entityClass = $this->getEntityObjectName($requestedName);
            $entityObject = new $entityClass();
			$fieldset->setHydrator((new DoctrineHydrator($om))
                //->addStrategy('quantityItem', new \DoctrineModule\Stdlib\Hydrator\Strategy\DisallowRemoveByValue())
                //)->setObject($sm->get($this->getEntityObjectName($requestedName)));
                )->setObject($entityObject);
			$fieldset->setObjectManager($om);
		}

        if ($fieldset instanceof TranslatorAwareInterface) {
            /** @var \Zend\Mvc\I18n\Translator $translator */
            $translator = $sm->get('translator');
            //\Zend\Debug\Debug::dump([get_class($translator)]); //die(__METHOD__);
            //$translator->setTranslatorTextDomain($currentPlugin->currentModule($fieldset));
            $fieldset->setTranslator($translator);
            $fieldset->setTranslatorTextDomain($modulePlugin->setRealContext($fieldset)->getRealModule()->getName());
            //$fieldset->setModule($modulePlugin->setRealContext($fieldset)->getModule());
        }

		return $fieldset;
	}

	protected function getEntityObjectName($requestedName) {
		/*$info = $this->getNamespaceInfo($requestedName);
		$parts = [
			$info['module'],
			'Model',
			trim(str_replace('Form', '', $info['relative']), '\\'),
			strstr($info['class'], 'Fieldset', true)
		];*/
		//$entityNamespace = implode('\\', array_filter($parts));
		$entityNamespace = $this->convertNamespace($requestedName, 'Form', 'Model', 'Fieldset');

		//\Zend\Debug\Debug::dump([$entityNamespace]);

		return $entityNamespace;
	}

	protected function getNamespaceInfo($namespace) {
		static $cache = [];
		if (!isset($cache[$namespace])) {
			$namespaceParts = explode('\\', $namespace);
			if (count($namespaceParts) > 1) {
				$cache[$namespace] = [
					'category' => $namespaceParts[0],
					'module' => $namespaceParts[0] . '\\' . $namespaceParts[1],
					'relative' => implode('\\', array_slice($namespaceParts, 2, -1)),
					'class' => $namespaceParts[count($namespaceParts) - 1],
				];
			} else {
				$cache[$namespace] = [
					'category' => '',
					'module' => '',
					'relative' => '',
					'class' => $namespace,
				];
			}
		}

		return $cache[$namespace];
	}

	/**
	 * Example:
	 * 	Popov\Invoice\Form\InvoiceProduct\QuantityItem => Popov\Invoice\Model\InvoiceProduct\QuantityItem
	 * 	QuantityItemFieldset => QuantityItem
	 *
	 *
	 * @param string $name Converted name
	 * @param string $from From 'Model'
	 * @param string $to To 'Form'
	 * @param string $ending End part of first argument which will be replaced or deleted
	 * @return string
	 */
	protected function convertNamespace($name, $from, $to, $ending) {
		$info = $this->getNamespaceInfo($name);
		$parts = [
			$info['module'],
			$info['module'] ? $to : '',
			trim(str_replace($from, '', $info['relative']), '\\'),
			$info['module'] ? strstr($info['class'], $ending, true) : str_replace($ending, $to, $info['class'])
		];
		$entityNamespace = implode('\\', array_filter($parts));

		return $entityNamespace;
	}

}