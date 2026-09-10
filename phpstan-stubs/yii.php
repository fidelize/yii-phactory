<?php

/**
 * Minimal Yii 1 stubs for static analysis of yii-phactory.
 */

class CApplicationComponent
{
    public function init(): void
    {
    }
}

class CDbTransaction
{
    public function rollBack(): void
    {
    }

    public function commit(): void
    {
    }
}

class CDbCommand
{
    public mixed $select = '*';
    public mixed $from = '';

    public function queryRow(): mixed
    {
        return false;
    }
}

class PgSequenceRow
{
    public string $sequence_schema = '';

    public string $sequence_name = '';

    public string $table_sequence = '';

    public string $table_schema = '';

    public string $table_name = '';
}

class CDbTableSchema
{
    /** @var string|list<string>|array<int|string, string>|null */
    public string|array|null $primaryKey = null;
    /** @var array<string, CDbColumnSchema> */
    public array $columns = [];
    /** @var array<string, mixed> */
    public array $foreignKeys = [];
    public ?string $sequenceName = null;
}

class CDbColumnSchema
{
    public string $dbType = '';
}

class CDbMetaData
{
    public CDbTableSchema $tableSchema;

    public function __construct()
    {
        $this->tableSchema = new CDbTableSchema();
    }
}

class CActiveRelation
{
    public mixed $foreignKey = null;
}

class CDbCriteria
{
    public string $condition = '';

    /** @return array{condition: string, params?: array<string, mixed>} */
    public function toArray(): array
    {
        return ['condition' => $this->condition];
    }
}

class CActiveDataProvider
{
    public function getCriteria(): CDbCriteria
    {
        return new CDbCriteria();
    }
}

class CActiveRecord
{
    /** @var array<string, mixed> */
    public array $attributes = [];
    /** @var array<string, mixed> */
    public array $errors = [];
    public CDbMetaData $metaData;
    public CDbMetaData $metadata;
    public mixed $id = null;

    public function __construct(mixed $scenario = 'insert')
    {
        $this->metaData = new CDbMetaData();
        $this->metadata = $this->metaData;
    }

    public function save(): bool
    {
        return true;
    }

    public function getActiveRelation(string $name): ?CActiveRelation
    {
        return null;
    }

    public function hasAttribute(string $name): bool
    {
        return false;
    }

    public function tableName(): string
    {
        return '';
    }

    public function getTableSchema(): CDbTableSchema
    {
        return $this->metaData->tableSchema;
    }

    /** @return list<array{0: string, 1?: mixed}> */
    public function rules(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function attributeLabels(): array
    {
        return [];
    }

    /** @return array<string, array{0: string, 1: string, 2: mixed}> */
    public function relations(): array
    {
        return [];
    }

    public function primaryKey(): mixed
    {
        return null;
    }

    /** @return list<string> */
    public function getSafeAttributeNames(): array
    {
        return [];
    }

    public function unsetAttributes(): void
    {
    }

    public function search(): CActiveDataProvider
    {
        return new CActiveDataProvider();
    }

    /**
     * @template T of CActiveRecord
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    public static function model(string $className = self::class): static
    {
        return new static();
    }
}

class CDbException extends Exception
{
}

class CFilterChain
{
    public function __construct(mixed $controller = null, mixed $action = null)
    {
    }
}

class CAccessControlFilter
{
    /** @param list<mixed> $rules */
    public function setRules(array $rules): void
    {
    }

    public function filter(mixed $chain): void
    {
    }

    public function accessDenied(mixed $user = null, mixed $message = null): void
    {
    }
}

class CController
{
    /** @return list<mixed> */
    public function accessRules(): array
    {
        return [];
    }

    public function createAction(string $action): mixed
    {
        return null;
    }

    public function run(string $action): void
    {
    }

    public function invalidActionParams(mixed $action = null): void
    {
    }
}

class CConsoleCommand
{
}

class CConsoleCommandRunner
{
}

class CHttpSession extends CApplicationComponent implements ArrayAccess, Countable, IteratorAggregate
{
    public bool $autoStart = true;

    public function open(): void
    {
    }

    public function close(): void
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return false;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public function count(): int
    {
        return 0;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator([]);
    }
}

class CWebUser
{
    public function logout(): void
    {
    }

    public function login(mixed $identity): bool
    {
        return true;
    }

    public function getFlash(string $key, mixed $defaultValue = null, bool $delete = true): mixed
    {
        return null;
    }
}

class CDateFormatter
{
    public function formatDateTime(mixed $timestamp, ?string $dateWidth = null, ?string $timeWidth = null): string
    {
        return '';
    }
}

class CLocale
{
    public function getDateFormatter(): CDateFormatter
    {
        return new CDateFormatter();
    }
}

class CDbConnection
{
    public ?\PDO $pdoInstance = null;

    public function getCurrentTransaction(): ?CDbTransaction
    {
        return null;
    }

    public function beginTransaction(): CDbTransaction
    {
        return new CDbTransaction();
    }

    public function createCommand(mixed $query = null): CDbCommand
    {
        return new CDbCommand();
    }

    public function getLastInsertID(mixed $sequenceName = ''): string
    {
        return '0';
    }
}

class CApplication
{
    public CDbConnection $db;
    public CWebUser $user;
    public CLocale $locale;
    /** @var array<string, mixed> */
    public array $params = [];

    public function __construct()
    {
        $this->db = new CDbConnection();
        $this->user = new CWebUser();
        $this->locale = new CLocale();
    }

    public function setComponent(string $id, mixed $component): void
    {
    }

    public function setController(mixed $controller): void
    {
    }
}

class Yii
{
    public static function app(): CApplication
    {
        return new CApplication();
    }

    public static function import(string $alias, bool $forceInclude = false): string
    {
        return $alias;
    }

    public static function createComponent(mixed $config): mixed
    {
        return null;
    }
}

class UserIdentity
{
    public function __construct(string $username = '', string $password = '')
    {
    }

    public function getId(): mixed
    {
        return null;
    }

    public function getName(): string
    {
        return '';
    }
}

class Phactory
{
    public static string $dependencyClass = '';

    public static function reset(): void
    {
    }

    public static function loader(mixed $loader): void
    {
    }

    public static function builder(mixed $builder): void
    {
    }
}
