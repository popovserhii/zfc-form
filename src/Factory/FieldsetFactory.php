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

class FieldsetFactory implements AbstractFactoryInterface {

	use FieldsetFactoryTrait;

}