<?php

Yii::import('system.test.CDbTestCase');
Yii::import('fidelize.tests.phactory.FPhactoryBuilder');
Yii::import('fidelize.tests.phactory.FPhactoryLoader');
Yii::import('fidelize.tests.phactory.FPhactoryDependency');

abstract class FPhactoryTestCase extends CDbTestCase
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