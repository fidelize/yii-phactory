<?php

Yii::import('system.test.CDbFixtureManager'); 

class FDbFixtureManager extends CDbFixtureManager
{
    /**
     * Prepares the fixtures for the whole test.
     * This method is invoked in {@link init}. It executes the database init script
     * if it exists. Otherwise, it will load all available fixtures.
     */
    public function prepare()
    {
        $this->checkIntegrity(false);

        $transaction = $this->dbConnection->beginTransaction();

        try {
            parent::prepare();

            $transaction->commit();
        }
        catch (Exception $e) {
            
            $transaction->rollBack();

            throw $e;
        }

        $this->checkIntegrity(true);
    }

    public function loadFixture($tableName)
    {
        $loadFixturePai = parent::loadFixture($tableName);
        if ($loadFixturePai !== false) {
            $schema=$this->getDbConnection()->getSchema();
            $table=$schema->getTable($tableName);
            if ($table->sequenceName!==null) {
                self::atualizaSequence($tableName, $table->sequenceName);
            }
        }
        return $loadFixturePai;
    }

    public static function atualizaSequence($tableName, $sequenceName)
    {
        $sequenceAtualizada = Yii::app()->db->createCommand(
            "select setval('" . $sequenceName . "',(select max(id)+1 from " . $tableName . "))"
        )->execute();
        return ($sequenceAtualizada > 0);
    }

    /**
     * Garante que o checkIntegrity seja setado para todos os schemas configurados.
     * @param Array $fixtures
     */
    public function load($fixtures)
    {
        $this->checkIntegrity(false);
        parent::load($fixtures);
        $this->checkIntegrity(true);
    }
}