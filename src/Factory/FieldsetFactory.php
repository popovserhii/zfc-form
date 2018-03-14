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

//use Zend\ServiceManager\AbstractFactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class FieldsetFactory implements AbstractFactoryInterface {

	use FieldsetFactoryTrait;

	public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->canCreateServiceWithName($container, $requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return $this->createServiceWithName($container, $requestedName, $options);
    }
}