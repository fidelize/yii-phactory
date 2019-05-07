<?php
namespace fidelize\YiiPhactory;

class FPhactoryCommandTest extends FPhactoryTestCase
{
    /**
     * @var CConsoleCommand
     */
    protected $command;

    /**
     * Command class name or path.
     * @var string application.path.to.command.ClassName
     */
    protected $classPath;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        // Create mock
        $this->command = $this->getCommandMock();
    }

    /**
     * @return CController
     */
    protected function getCommandMock(array $methods = [])
    {
        $className = $this->getCommandClassName();
        $name = $this->getCommandName();
        $runner = $this->getMockBuilder('CConsoleCommandRunner')->getMock();

        $mock = $this->getMockBuilder($className)
            ->setConstructorArgs([$name, $runner])
            ->setMethods(['prompt'])
            ->getMock()
        ;
        return $mock;
    }

    /**
     * @return string
     */
    protected function getCommandName()
    {
        $name = preg_replace('/([A-Z])/', '_$1', $this->getCommandClassName());
        $name = preg_replace('/^_/', '', $name);
        return strtolower($name);
    }

    /**
     * Parse and autoload class name based on the class path.
     * @return string Command class name
     */
    protected function getCommandClassName()
    {
        $className = $this->classPath;

        if (false !== strpos($className, '.')) {
            Yii::import($className);
            $className = explode('.', $className);
            $className = array_pop($className);
        }

        return $className;
    }
}
