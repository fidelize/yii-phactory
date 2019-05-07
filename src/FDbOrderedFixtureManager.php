<?php
Yii::import('system.test.CDbFixtureManager');

class FDbOrderedFixtureManager extends CDbFixtureManager
{
    /**
     * Fixtures ordenadas
     * @var array
     */
    protected $_orderedFixtures;
    
    /**
     * Indica as tabelas que devem ser resetadas por primeiro, e em qual ordem
     * @var array
     */
    public static $resetFirst = array();
    
    /**
	 * Prepares the fixtures for the whole test.
	 * This method is invoked in {@link init}. It executes the database init script
	 * if it exists. Otherwise, it will load all available fixtures.
	 */
	public function prepare()
	{
		$initFile=$this->basePath . DIRECTORY_SEPARATOR . $this->initScript;
		$this->checkIntegrity(false);
		if(is_file($initFile)){
			require($initFile);
		}else{
      $fixtures = $this->getFixtures();
            
			//echo 'zerando tabela: ';
			foreach($fixtures as $tableName=>$fixturePath)
			{
				//echo $tableName . ', ';
				$this->resetTable($tableName);
			}
			//echo "\n";
            
      $fixtures = array_reverse($fixtures, true);
        
			//echo 'carregando tabela: ';
      foreach($fixtures as $tableName=>$fixturePath){
				//echo $tableName . ', ';
				$this->loadFixture($tableName);
			}
			//echo "\n";
		}
		$this->checkIntegrity(true);
	}
    
    /**
	 * Returns the information of the available fixtures.
	 * This method will search for all PHP files under {@link basePath}.
	 * If a file's name is the same as a table name, it is considered to be the fixture data for that table.
	 * @return array the information of the available fixtures (table name => fixture file)
	 */
	public function getFixtures()
	{
		if($this->_orderedFixtures===null)
		{
            $fixtures = parent::getFixtures();
            
            if (is_array(self::$resetFirst)) {
                
                $orderedFixtures = array();
                
                foreach (self::$resetFirst as $tableName) {
                    
                    if (isset($fixtures[$tableName])) {
                        $orderedFixtures[$tableName] = $fixtures[$tableName];
                        unset($fixtures[$tableName]);
                    }
                }
                
                foreach ($fixtures as $tableName => $path) {
                    $orderedFixtures[$tableName] = $path;
                }
                
                $this->_orderedFixtures = $orderedFixtures;
            }
		}
        
		return $this->_orderedFixtures;
	}


   public function load($fixtures)
    {
        $connection = $this->getDbConnection();
        $pdo = $connection->getPdoInstance();

        $schema = $connection->getSchema();
        $schema->checkIntegrity(false);
        $transaction = $connection->beginTransaction();

        parent::load($fixtures);

        $transaction->commit();

        $schema->checkIntegrity(true);
    }
}
