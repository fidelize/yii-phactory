<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

abstract class FPhactoryActiveRecordTest extends FPhactoryTestCase
{
    /**
     * Se deve ou não testar se cada relação do banco de dados com essa tabela
     * de fato existe nos modelos ActiveRecord.
     */
    protected bool $relationsTest = true;

    /**
     * Testa o método ::model()
     */
    public function testModel(): void
    {
        $className = $this->getModelClass();

        if (!class_exists($className)) {
            $this->fail('Model class does not exist: ' . $className);
        }

        $this->assertInstanceOf(
            $className,
            $this->getModelObject(),
            'Esperado que o método model retorne a instância',
        );
    }

    /**
     * Garante que a tabela do modelo exista no banco de dados
     */
    public function testTableName(): void
    {
        $model = $this->getModelObject();
        $command = \Yii::app()->db->createCommand();
        $command->select = $this->resolvePrimaryKeyForSelect($model);
        $command->from = $model->tableName();

        try {
            $command->queryRow();
        } catch (\CDbException $e) {
            $this->fail('Ocorreu um erro ao tentar selecionar da tabela: ' . $e->getMessage());
        }
    }

    /**
     * Colunas da PK para SELECT (schema), evitando primaryKey() vazio do CActiveRecord.
     *
     * @return string|array<int|string, string>
     */
    protected function resolvePrimaryKeyForSelect(\CActiveRecord $model): string|array
    {
        $primaryKey = $model->getTableSchema()->primaryKey;

        if ($primaryKey === null || $primaryKey === '' || $primaryKey === []) {
            return '*';
        }

        return $primaryKey;
    }

    /**
     * Garante que todos os atributos têm pelo menos uma regra de validação
     */
    public function testRules(): void
    {
        $object = $this->getModelObject();
        $attributes = $object->attributes;

        foreach ($object->rules() as $rule) {
            if (!is_array($rule) || !isset($rule[0]) || !is_string($rule[0])) {
                continue;
            }

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
            if (!is_string($pk) && !is_int($pk)) {
                continue;
            }

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
    public function testAttributesLabels(): void
    {
        $object = $this->getModelObject();
        $labels = $object->attributeLabels();
        $attributes = array_keys($object->attributes);
        $attributesWithoutLabels = [];

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
     *
     * @TODO testar belongsTo e hasMany
     */
    public function testRelations(): void
    {
        if ($this->relationsTest == false) {
            $this->markTestSkipped(
                'Este case está configurado para não testar as relations do modelo',
            );
        }

        $object = $this->getModelObject();

        $schema = $object->metaData->tableSchema;
        $foreignKeys = $schema->foreignKeys;
        $relations = $object->relations();

        if (!empty($foreignKeys)) {
            foreach ($relations as $data) {
                if (!is_array($data) || count($data) < 3) {
                    continue;
                }

                $fkAttribute = $data[2];

                if (is_array($fkAttribute)) {
                    continue; // TODO Quando for um relacionamento "complicado"
                }

                if (!is_string($fkAttribute) && !is_int($fkAttribute)) {
                    continue;
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
    public function testSearch(): void
    {
        $class = $this->getModelClass();

        if (!class_exists($class)) {
            $this->fail('Model class does not exist: ' . $class);
        }

        $object = new $class('search');

        if (!$object instanceof \CActiveRecord) {
            $this->fail('Model class must extend CActiveRecord: ' . $class);
        }

        $attributesWithoutSearch = [];

        $object->unsetAttributes();

        $dadosColunas = $object->metaData->tableSchema->columns;
        $colunasDaTabela = array_keys($dadosColunas);
        $safeAttributes = $object->getSafeAttributeNames();

        $oReflectionClass = new \ReflectionClass($class);

        foreach ($safeAttributes as $attribute) {
            if (!is_string($attribute)) {
                continue;
            }

            if ($oReflectionClass->hasProperty($attribute)) {
                $property = $oReflectionClass->getProperty($attribute);

                if (!$property->isPublic()) {
                    continue;
                }
            }

            if (str_contains($attribute, '+')) {
                continue;
            }

            $timestamps = [
                'timestamp without time zone',
                'timestamp(0) without time zone',
            ];

            if (
                isset($dadosColunas[$attribute])
                && in_array($dadosColunas[$attribute]->dbType, $timestamps, true)
            ) {
                $object->$attribute = FDate::getDateTime();
            } elseif (isset($dadosColunas[$attribute]) && $dadosColunas[$attribute]->dbType == 'date') {
                $object->$attribute = FDate::getDate();
            } else {
                $object->$attribute = 93;
            }
        }

        $dataProvider = $object->search();
        $criteria = $dataProvider->getCriteria()->toArray();
        $condition = $criteria['condition'] ?? '';

        if (!is_string($condition)) {
            $condition = '';
        }

        foreach ($safeAttributes as $safeAttribute) {
            if (!is_string($safeAttribute)) {
                continue;
            }

            if (
                in_array($safeAttribute, $colunasDaTabela, true)
                && !str_contains($condition, $safeAttribute)
            ) {
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
     */
    protected function getModelClass(): string
    {
        $className = static::class;

        return substr($className, 0, strlen($className) - strlen('Test'));
    }

    /**
     * Adivinha o nome do modelo pelo nome da classe de teste
     */
    protected function getModelObject(): \CActiveRecord
    {
        $className = $this->getModelClass();

        if (!class_exists($className)) {
            throw new \RuntimeException('Model class does not exist: ' . $className);
        }

        $model = $className::model();

        if (!$model instanceof \CActiveRecord) {
            throw new \RuntimeException('Model method did not return CActiveRecord: ' . $className);
        }

        return $model;
    }
}
