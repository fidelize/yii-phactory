<?php
namespace fidelize\YiiPhactory;

abstract class FPhactoryActiveRecordTest extends FPhactoryTestCase
{
    /**
     * Se deve ou não testar se cada relação do banco de dados com essa tabela
     * de fato existe nos modelos ActiveRecord.
     */
    protected $relationsTest = true;

    /**
     * Testa o método ::model()
     */
    public function testModel()
    {
        $this->assertInstanceOf(
            $this->getModelClass(),
            $this->getModelObject(),
            'Esperado que o método model retorne a instância'
        );
    }

    /**
     * Garante que a tabela do modelo exista no banco de dados
     */
    public function testTableName()
    {
        $command = \Yii::app()->db->createCommand();
        $command->select = $this->getModelObject()->primaryKey();
        $command->from = $this->getModelObject()->tableName();

        try {
            $command->queryRow();
        }
        catch (CDbException $e) {
            throw $e;
            $this->fail('Ocorreu um erro ao tentar selecionar da tabela');
        }
    }

    /**
     * Garante que todos os atributos têm pelo menos uma regra de validação
     */
    public function testRules()
    {
        $object = $this->getModelObject();
        $attributes = $object->attributes;

        foreach ($object->rules() as $rule) {

            $ruleAttributes = explode(',', $rule[0]);

            foreach ($ruleAttributes as $attribute) {

                $attribute = trim($attribute);

                if (array_key_exists($attribute, $attributes)) {
                    unset($attributes[$attribute]);
                }
            }
        }

        $primaryKeys = (array) $object->primaryKey();

        foreach ($primaryKeys as $pk) {
            unset($attributes[$pk]);
        }

        if (count($attributes)) {

            $attributes = implode('", "', array_keys($attributes));
            $message = 'Os atributos "' . $attributes . '" não possuem nenhuma regra de validação.';

            $this->fail($message);
        }
    }

    /**
     * Verifica se todos os atributos possuem um label
     */
    public function testAttributesLabels()
    {
        $object = $this->getModelObject();
        $labels = $object->attributeLabels();
        $attributes = array_keys($object->attributes);
        $attributesWithoutLabels = array();

        foreach ($attributes as $attribute) {
            if (!isset($labels[$attribute])) {
                $attributesWithoutLabels[] = $attribute;
            }
        }

        if (count($attributesWithoutLabels)) {

            $attributes = implode('", "', $attributesWithoutLabels);
            $message = 'Os atributos "' . $attributes . '" não possuem label.';

            $this->fail($message);
        }
    }

    /**
     * Testa se as FKs da tabela do modelo foram definidas como relations
     * @TODO testar belongsTo e hasMany
     */
    public function testRelations()
    {
        if ($this->relationsTest == false) {

            return $this->markTestSkipped(
                'Este case está configurado para não testar as relations do modelo'
            );
        }

        $object = $this->getModelObject();

        $schema = $object->metaData->tableSchema;
        $foreignKeys = $schema->foreignKeys;
        $relations = $object->relations();

        if (!empty($foreignKeys)) {

            foreach ($relations as $data) {

                list($type, $relatedClass, $fkAttribute) = $data;

                if (is_array($fkAttribute)) {
                   continue; // TODO Quando for um relacionamento "complicado"
                }

                if (isset($foreignKeys[$fkAttribute])) {
                    unset($foreignKeys[$fkAttribute]);
                }
            }
        }

        if (count($foreignKeys)) {

            $attributes = implode('", "', array_keys($foreignKeys));
            $message = 'Os atributos "' . $attributes . '" têm Foreign Key, mas não têm relation definida.';

            $this->fail($message);
        }
    }

    /**
     * Testa se os atributos marcados como safe, on => search realmente são filtrados
     * no método search()
     */
    public function testSearch()
    {
        $class = $this->getModelClass();
        $object = new $class('search');
        $attributesWithoutSearch = array();

        $object->unsetAttributes();

        $dadosColunas = $object->metaData->tableSchema->columns;
        $colunasDaTabela = array_keys($dadosColunas);
        $safeAttributes = $object->getSafeAttributeNames();
	
	    $oReflectionClass = new \ReflectionClass($class);

        foreach ($safeAttributes as $attribute) {
            if ($oReflectionClass->hasProperty($attribute)) {
                $property = $oReflectionClass->getProperty($attribute);

                if (!$property->isPublic()) {
                    continue;
                }
            }
	
            if (strpos($attribute, "+") !== false){
                continue;
            }

            $timestamps = [
                'timestamp without time zone',
                'timestamp(0) without time zone'
            ];

            if (isset($dadosColunas[$attribute]) && in_array($dadosColunas[$attribute]->dbType, $timestamps)) {
                $object->$attribute = FDate::getDateTime();
            } elseif (isset($dadosColunas[$attribute]) && $dadosColunas[$attribute]->dbType == 'date') {
                $object->$attribute = FDate::getDate();
            } else {
                $object->$attribute = 93;
            }
        }

        $dataProvider = $object->search();

        if (!method_exists($dataProvider, 'getCriteria')) {
            return ;
        }

        $criteria = $dataProvider->getCriteria()->toArray();

        foreach ($safeAttributes as $safeAttribute) {

            if (in_array($safeAttribute, $colunasDaTabela) && strpos($criteria['condition'], $safeAttribute) === false) {
                $attributesWithoutSearch[] = $safeAttribute;
            }
        }

        if (count($attributesWithoutSearch)) {

            $attributes = implode('", "', $attributesWithoutSearch);
            $message = 'Os atributos "' . $attributes . '" estão marcados como safe, mas não são filtrados no método search().';

            $this->fail($message);
        }
    }

    /**
     * Adivinha o nome do modelo pelo nome da classe de teste
     * @return string
     */
    protected function getModelClass()
    {
        $className = get_called_class();
        return substr($className, 0, strlen($className) - strlen('Test'));
    }

    /**
     * Adivinha o nome do modelo pelo nome da classe de teste
     * @return string
     */
    protected function getModelObject()
    {
        $className = $this->getModelClass();
        return $className::model();
    }
}
