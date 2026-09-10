<?php

declare(strict_types=1);

/**
 * Helper de banco de dados para utilização com a Phactory
 */

namespace fidelize\YiiPhactory;

class FPhactoryDbHelper
{
    /**
     * Retorna todas as sequences para o menor valor não-utilizado
     * Se a tabela já contém dados, usa o ID que vem depois desses dados
     */
    public static function resetSequences(): void
    {
        $sequences = self::getSequences();
        $pdo = \Yii::app()->db->pdoInstance;

        if ($pdo === null) {
            throw new \RuntimeException('PDO instance is not available');
        }

        // SETVAL via subquery: com "FROM tabela" vazia o SETVAL nao executava
        // (0 rows), deixando sequence dessincronizada e gerando Unique violation.
        foreach ($sequences as $sequence) {
            $sql = sprintf(
                "SELECT SETVAL('%s.%s', (SELECT COALESCE(MAX(%s), 1) FROM %s.%s))",
                $sequence->sequence_schema,
                $sequence->sequence_name,
                $sequence->table_sequence,
                $sequence->table_schema,
                $sequence->table_name,
            );
            $pdo->query($sql);
        }
    }

    /**
     * @return list<\PgSequenceRow> uma lista com o nome de todas as sequences:
     * sequence_schema, sequence_name, table_sequence, table_schema, table_name
     */
    public static function getSequences(): array
    {
        $pdo = \Yii::app()->db->pdoInstance;

        if ($pdo === null) {
            throw new \RuntimeException('PDO instance is not available');
        }

        $statement = $pdo->query("
            SELECT DISTINCT
                quote_ident(PGT.schemaname) AS sequence_schema,
                quote_ident(S.relname) AS sequence_name,
                quote_ident(C.attname) AS table_sequence,
                quote_ident(PGT.schemaname) AS table_schema,
                quote_ident(T.relname) AS table_name
            FROM pg_class AS S,
                 pg_depend AS D,
                 pg_class AS T,
                 pg_attribute AS C,
                 pg_tables AS PGT
            WHERE S.relkind = 'S'
                AND S.oid = D.objid
                AND D.refobjid = T.oid
                AND D.refobjid = C.attrelid
                AND D.refobjsubid = C.attnum
                AND T.relname = PGT.tablename
            ORDER BY
                table_schema,
                table_name
        ");

        if ($statement === false) {
            return [];
        }

        $sequences = $statement->fetchAll(\PDO::FETCH_OBJ);
        $result = [];

        foreach ($sequences as $sequence) {
            if (!$sequence instanceof \PgSequenceRow) {
                continue;
            }

            $result[] = $sequence;
        }

        return $result;
    }
}
