<?php

declare(strict_types=1);

/*
 * Classe com fun??es usadas recorrentemente em testes, para evitar repeti??o
 * de c?digo
 */

namespace fidelize\YiiPhactory;

class FTestLib
{
    /**
     * Remove palavras reservadas do SQL, espa?os em branco, e condi??es esperadas
     * restando apenas texto que n?o foi tratado pelo teste
     *
     * @param array{condition?: string, params?: array<string, mixed>} $aCriteria
     * @param array<int, string> $aCondicaoEsperada
     * @return string vazia caso nao haja nenhuma condicao al?m das esperadas.
     * sen?o retorna as condicoes extras
     */
    public static function limpaConditionCriteria(array $aCriteria, array $aCondicaoEsperada): string
    {
        $sqlKeyWords = [
            'SELECT', 'ILIKE', 'LIKE', '=', '>', '<', 'AND',
            'OR', 'WHERE', 'FROM', 'JOIN', 'ON', 'LEFT', 'RIGHT', 'INNER',
            'EXISTS', 'NOT', 'IN', 'COUNT', 'DISTINCT', 'ORDER', 'BY', 'GROUP',
            'HAVING', '(', ')', '*',
        ];

        $newCondition = strtolower($aCriteria['condition'] ?? '');

        // Remove as condicoes esperadas
        foreach ($aCondicaoEsperada as $condicaoEsperada) {
            $newCondition = (string) preg_replace(
                '/[ (]' . strtolower($condicaoEsperada) . '/',
                '',
                $newCondition,
            );
        }
        // Remove as keywords do sql
        foreach ($sqlKeyWords as $keyWord) {
            $newCondition = str_replace(strtolower($keyWord), '', $newCondition);
        }
        // Remove os parametros da criteria
        $params = $aCriteria['params'] ?? [];
        foreach ($params as $param => $value) {
            if (!is_string($param) && !is_int($param)) {
                continue;
            }
            $newCondition = (string) preg_replace(
                '/' . strtolower((string) $param) . '[^0-9]?/',
                '',
                $newCondition,
                1,
            );
        }
        // Remove espacos em branco
        $newCondition = str_replace(' ', '', $newCondition);

        return $newCondition;
    }

    public static function geraCNPJ(): string
    {
        $n1 = random_int(0, 9);
        $n2 = random_int(0, 9);
        $n3 = random_int(0, 9);
        $n4 = random_int(0, 9);
        $n5 = random_int(0, 9);
        $n6 = random_int(0, 9);
        $n7 = random_int(0, 9);
        $n8 = random_int(0, 9);
        $n9 = 0;
        $n10 = 0;
        $n11 = 0;
        $n12 = 1;
        $d1 = $n12 * 2 + $n11 * 3 + $n10 * 4 + $n9 * 5 + $n8 * 6 + $n7 * 7 + $n6 * 8 + $n5 * 9 + $n4 * 2 + $n3 * 3 + $n2 * 4 + $n1 * 5;
        $d1 = 11 - ($d1 % 11);
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n12 * 3 + $n11 * 4 + $n10 * 5 + $n9 * 6 + $n8 * 7 + $n7 * 8 + $n6 * 9 + $n5 * 2 + $n4 * 3 + $n3 * 4 + $n2 * 5 + $n1 * 6;
        $d2 = 11 - ($d2 % 11);
        if ($d2 >= 10) {
            $d2 = 0;
        }

        return '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $n10 . $n11 . $n12 . $d1 . $d2;
    }

    public static function geraCPF(?bool $compontos = null): string
    {
        $n1 = random_int(0, 9);
        $n2 = random_int(0, 9);
        $n3 = random_int(0, 9);
        $n4 = random_int(0, 9);
        $n5 = random_int(0, 9);
        $n6 = random_int(0, 9);
        $n7 = random_int(0, 9);
        $n8 = random_int(0, 9);
        $n9 = random_int(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - (round($d1 - (floor($d1 / 11) * 11)));
        if ($d1 >= 10) {
            $d1 = 0;
        }

        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - (round($d2 - (floor($d2 / 11) * 11)));
        if ($d2 >= 10) {
            $d2 = 0;
        }

        if ($compontos) {
            return '' . $n1 . $n2 . $n3 . '.' . $n4 . $n5 . $n6 . '.' . $n7 . $n8 . $n9 . '-' . $d1 . $d2;
        }

        return '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $d1 . $d2;
    }
}
