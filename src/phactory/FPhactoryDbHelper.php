<?php
/**
 * Helper de banco de dados para utilização com a Phactory
 */
class FPhactoryDbHelper
{
    /**
     * Retorna todas as sequences para o menor valor não-utilizado
     * Se a tabela já contém dados, usa o ID que vem depois desses dados
     * @return void
     */
    public static function resetSequences()
    {
        $sequences = self::getSequences();
        $resetters = array();

        foreach ($sequences as $sequence) {

            $sql = "
                SELECT SETVAL(
                    '{$sequence->sequence_schema}.{$sequence->sequence_name}',
                    COALESCE(MAX({$sequence->table_sequence}), 1)
                )
                FROM {$sequence->table_schema}.{$sequence->table_name}
            ";

            $resetters[] = $sql;
        }

        return Yii::app()->db->pdoInstance->query(
            implode("\nUNION\n", $resetters)
        );
    }

    /**
     * @return array uma lista com o nome de todas as sequences:
     * sequence_schema, sequence_name, table_sequence, table_schema, table_name
     */
    public static function getSequences()
    {
        return Yii::app()->db->pdoInstance->query("
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
        ")->fetchAll(PDO::FETCH_OBJ);
    }
}