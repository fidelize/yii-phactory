<?php
namespace fidelize\YiiPhactory;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class FPhactoryTestCase extends TestCase
{
    private $_transaction;
    
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        \Phactory::reset();
        \Phactory::loader(new FPhactoryLoader);
        \Phactory::builder(new FPhactoryBuilder);

        if (property_exists('Phactory', 'dependencyClass')) {

            \Phactory::$dependencyClass = FPhactoryDependency::class;
        }

        $this->_transaction = \Yii::app()->db->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->_transaction->rollBack();
    }
    
    /**
     * @param object $classObject
     * @param string $property
     * This method is used to get private AND protected class property values
     */
    protected function getPrivatePropertyValue($classObject, $property)
    {
        $reflection = new ReflectionClass($classObject);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        return $reflection_property->getValue($classObject);
    }
    
    /**
     * @param object $classObject
     * @param string $methodName
     * @param array $args
     * This method is used to execute private AND protected methods
     */
    protected function executePrivateMethod($classObject, $methodName, $args)
    {
        $reflection = new ReflectionClass($classObject);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($classObject, $args);
    }
}
