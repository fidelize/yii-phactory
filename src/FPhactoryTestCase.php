<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[\AllowDynamicProperties]
abstract class FPhactoryTestCase extends TestCase
{
    private ?\CDbTransaction $_transaction = null;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        \Phactory::reset();
        \Phactory::loader(new FPhactoryLoader());
        \Phactory::builder(new FPhactoryBuilder());

        if (property_exists('Phactory', 'dependencyClass')) {
            \Phactory::$dependencyClass = FPhactoryDependency::class;
        }

        if (\Yii::app()->db->getCurrentTransaction()) {
            \Yii::app()->db->getCurrentTransaction()->rollBack();
        }

        $this->_transaction = \Yii::app()->db->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->_transaction?->rollBack();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     * @param list<non-empty-string> $onlyMethods
     * @param list<mixed> $constructorArgs
     *
     * @return MockObject&T
     */
    protected function createPartialMock(
        string $className,
        array $onlyMethods,
        array $constructorArgs = [],
    ): MockObject {
        if (!class_exists($className)) {
            throw new \RuntimeException('Class does not exist: ' . $className);
        }

        return $this->getMockBuilder($className)
            ->setConstructorArgs($constructorArgs)
            ->onlyMethods($onlyMethods)
            ->getMock();
    }

    /**
     * This method is used to get private AND protected class property values
     */
    protected function getPrivatePropertyValue(object $classObject, string $property): mixed
    {
        $reflection = new ReflectionClass($classObject);
        $reflection_property = $reflection->getProperty($property);

        return $reflection_property->getValue($classObject);
    }

    /**
     * This method is used to execute private AND protected methods
     *
     * @param array<int, mixed> $args
     */
    protected function executePrivateMethod(object $classObject, string $methodName, array $args): mixed
    {
        $reflection = new ReflectionClass($classObject);
        $method = $reflection->getMethod($methodName);

        return $method->invokeArgs($classObject, $args);
    }
}
