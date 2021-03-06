<?php

/*
 * Classe com fun��es usadas recorrentemente em testes, para evitar repeti��o
 * de c�digo
 */
namespace fidelize\YiiPhactory;

class FTestLib
{

    /**
     * Remove palavras reservadas do SQL, espa�os em branco, e condi��es esperadas
     * restando apenas texto que n�o foi tratado pelo teste
     * @param array $aCriteria
     * @param array $aCondicaoEsperada
     * @return string vazia caso nao haja nenhuma condicao al�m das esperadas.
     * sen�o retorna as condicoes extras
     */
    public static function limpaConditionCriteria($aCriteria, $aCondicaoEsperada)
    {
        $sqlKeyWords = array('SELECT', 'ILIKE', 'LIKE', '=', '>', '<', 'AND',
            'OR', 'WHERE', 'FROM', 'JOIN', 'ON', 'LEFT', 'RIGHT', 'INNER',
            'EXISTS', 'NOT', 'IN', 'COUNT', 'DISTINCT', 'ORDER', 'BY', 'GROUP',
            'HAVING', '(', ')', '*');

        $newCondition = strtolower($aCriteria['condition']);

        // Remove as condicoes esperadas
        foreach ($aCondicaoEsperada as $condicaoEsperada) {
            $newCondition = preg_replace('/[ (]' . strtolower($condicaoEsperada) . '/', "", $newCondition);
        }
        // Remove as keywords do sql
        foreach ($sqlKeyWords as $keyWord) {
            $newCondition = str_replace(strtolower($keyWord), "", $newCondition);
        }
        // Remove os parametros da criteria
        $aReversedParams = $aCriteria['params']; //array_reverse($aCriteria['params']);
        foreach ($aReversedParams as $param => $value) {
            $newCondition = preg_replace('/' . strtolower($param) . '[^0-9]?/', "", $newCondition, 1);
        }
        // Remove espacos em branco
        $newCondition = str_replace(" ", "", $newCondition);

        return $newCondition;
    }

    public static function geraCNPJ()
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = 0;
        $n10 = 0;
        $n11 = 0;
        $n12 = 1;
        $d1 = $n12 * 2 + $n11 * 3 + $n10 * 4 + $n9 * 5 + $n8 * 6 + $n7 * 7 + $n6 * 8 + $n5 * 9 + $n4 * 2 + $n3 * 3 + $n2 * 4 + $n1 * 5;
        $d1 = 11 - ( ($d1 % 11) );
        if ($d1 >= 10) {
            $d1 = 0;
        } $d2 = $d1 * 2 + $n12 * 3 + $n11 * 4 + $n10 * 5 + $n9 * 6 + $n8 * 7 + $n7 * 8 + $n6 * 9 + $n5 * 2 + $n4 * 3 + $n3 * 4 + $n2 * 5 + $n1 * 6;
        $d2 = 11 - ( ($d2 % 11) );
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $n10 . $n11 . $n12 . $d1 . $d2;
        return $retorno;
    }

    public static function geraCPF(bool $compontos = null)
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9*2+$n8*3+$n7*4+$n6*5+$n5*6+$n4*7+$n3*8+$n2*9+$n1*10;
        $d1 = 11 - ( round($d1 - (floor($d1/11)*11)) );
        if ($d1 >= 10) {
            $d1 = 0;
        }

        $d2 = $d1*2+$n9*3+$n8*4+$n7*5+$n6*6+$n5*7+$n4*8+$n3*9+$n2*10+$n1*11;
        $d2 = 11 - ( round($d2 - (floor($d2/11)*11)) );
        if ($d2>=10) {
            $d2 = 0 ;
        }

        $retorno = '';
        if ($compontos) {
            $retorno = ''.$n1.$n2.$n3.".".$n4.$n5.$n6.".".$n7.$n8.$n9."-".$d1.$d2;
        } else {
            $retorno = ''.$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$d1.$d2;
        }

        return $retorno;
    }
}
