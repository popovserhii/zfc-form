<?php
/**
 * Fieldset Factory Trait
 *
 * @category Popov
 * @package Popov_Form
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 17.02.15 22:24
 */

namespace Popov\ZfcForm\Factory;

use Psr\Container\ContainerInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\Translator;
use Zend\Form\FormElementManager;
use Zend\Form\Fieldset;
use Zend\ServiceManager\Exception;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Popov\ZfcEntity\Helper\ModuleHelper;

trait FieldsetFactoryTrait
{
    public function canCreateServiceWithName(ContainerInterface $container, $requestedName)
    {
        return (substr($requestedName, -8) === 'Fieldset');
    }

    public function createServiceWithName(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!class_exists($requestedName)) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: failed retrieving "%s"; class does not exist',
                get_class($this) . '::' . __FUNCTION__,
                $requestedName
            //($name ? '(alias: ' . $name . ')' : '')
            ));
        }
        /** @var Fieldset $fieldset (type of) */
        $fieldset = new $requestedName();
        if ($fieldset instanceof ObjectManagerAwareInterface) {
            $om = $container->get('Doctrine\ORM\EntityManager');
            $entityClass = method_exists($fieldset, 'getObjectName')
                ? $fieldset->getObjectName()
                : $this->getEntityObjectName($requestedName);

            if (!class_exists($entityClass)) {
                throw new Exception\ServiceNotFoundException(sprintf(
                    '%s: failed retrieving "%s"; class does not exist',
                    get_class($this) . '::' . __FUNCTION__,
                    $requestedName
                ));
            }

            $entityObject = new $entityClass();

            $fieldset->setHydrator((new DoctrineHydrator($om)))->setObject($entityObject);
            $fieldset->setObjectManager($om);
        }
        if ($fieldset instanceof TranslatorAwareInterface) {
            /** @var ModuleHelper $moduleHelper */
            $moduleHelper = $container->get(ModuleHelper::class);
            /** @var Translator $translator */
            $translator = $container->get(TranslatorInterface::class);
            $fieldset->setTranslator($translator);
            $fieldset->setTranslatorTextDomain($moduleHelper->setRealContext($fieldset)->getModule()->getName());
        }

        return $fieldset;
    }

    protected function getEntityObjectName($requestedName)
    {
        $entityNamespace = $this->convertNamespace($requestedName, 'Form', 'Model', 'Fieldset');

        return $entityNamespace;
    }


    /**
     * Example:
     *    Popov\Invoice\Form\InvoiceProduct\QuantityItem => Popov\Invoice\Model\InvoiceProduct\QuantityItem
     *    QuantityItemFieldset => QuantityItem
     *
     * @param string $name Converted name
     * @param string $from From 'Model'
     * @param string $to To 'Form'
     * @param string $ending End part of first argument which will be replaced or deleted
     * @return string
     */
    protected function convertNamespace($name, $from, $to, $ending)
    {
        $info = $this->getNamespaceInfo($name);
        $parts = [
            $info['module'],
            $info['module'] ? $to : '',
            trim(str_replace($from, '', $info['relative']), '\\'),
            $info['module'] ? strstr($info['class'], $ending, true) : str_replace($ending, $to, $info['class']),
        ];
        $entityNamespace = implode('\\', array_filter($parts));

        return $entityNamespace;
    }

    protected function getNamespaceInfo($namespace)
    {
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
}