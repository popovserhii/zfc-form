<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Popov\ZfcForm;

class ConfigProvider
{
    public function __invoke()
    {
        $config = include __DIR__ . '/../config/module.config.php';
        //$config['dependencies'] = $config['service_manager'];
        //unset($config['service_manager']);

        return $config;
    }
}
