<?php
namespace fidelize\YiiPhactory;

use PHPUnit\Framework\TestCase;

abstract class FPhactoryTestCase extends TestCase
{
    private $_transaction;
    
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        Phactory::reset();
        Phactory::loader(new FPhactoryLoader);
        Phactory::builder(new FPhactoryBuilder);

        if (property_exists('Phactory', 'dependencyClass')) {
            Phactory::$dependencyClass = 'FPhactoryDependency';
        }

        $this->_transaction = Yii::app()->db->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->_transaction->rollBack();
    }
}