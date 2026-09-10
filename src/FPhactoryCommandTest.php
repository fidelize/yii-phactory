<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

use PHPUnit\Framework\MockObject\MockObject;

class FPhactoryCommandTest extends FPhactoryTestCase
{
    protected MockObject $command;

    /**
     * Command class name or path.
     *
     * @var string application.path.to.command.ClassName
     */
    protected string $classPath = '';

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create mock
        $this->command = $this->getCommandMock();
    }

    /**
     * @param array<int, non-empty-string> $methods
     */
    protected function getCommandMock(array $methods = []): MockObject
    {
        $className = $this->getCommandClassName();

        if (!class_exists($className)) {
            throw new \RuntimeException('Command class does not exist: ' . $className);
        }

        $name = $this->getCommandName();
        $runner = $this->getMockBuilder(\CConsoleCommandRunner::class)->getMock();

        $onlyMethods = array_values(array_filter(
            array_merge(['prompt'], $methods),
            static fn(string $method): bool => $method !== '',
        ));

        return $this->getMockBuilder($className)
            ->setConstructorArgs([$name, $runner])
            ->onlyMethods($onlyMethods)
            ->getMock();
    }

    protected function getCommandName(): string
    {
        $name = preg_replace('/([A-Z])/', '_$1', $this->getCommandClassName()) ?? '';
        $name = preg_replace('/^_/', '', $name) ?? '';

        return strtolower($name);
    }

    /**
     * Parse and autoload class name based on the class path.
     */
    protected function getCommandClassName(): string
    {
        $className = $this->classPath;

        if (str_contains($className, '.')) {
            \Yii::import($className);
            $className = explode('.', $className);
            $className = array_pop($className);
        }

        if (!is_string($className) || $className === '') {
            throw new \RuntimeException('Command class path is not configured');
        }

        return $className;
    }
}
