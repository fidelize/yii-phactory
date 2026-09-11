<?php

declare(strict_types=1);

namespace fidelize\YiiPhactory;

class FDate
{
    /**
     * @param integer $timestamp Timestamp (se for nulo, pega a hora atual)
     * @return string A data/hora no formato do idioma atual do Yii
     */
    public static function getDateTime(?int $timestamp = null): string
    {
        $timestamp ??= time();

        return \Yii::app()->locale->getDateFormatter()->formatDateTime(
            $timestamp,
            self::getDateFormatLength(),
            self::getTimeFormatLength(),
        );
    }

    /**
     * Recebe uma data em Timestamp e retorna uma string com a data formatada para
     * a language do usu?rio
     *
     * @param integer $timestamp Timestamp (se for nulo, pega a hora atual)
     * @return string A data no formato do idioma atual do Yii
     */
    public static function getDate(?int $timestamp = null): string
    {
        $timestamp ??= time();

        return \Yii::app()->locale->getDateFormatter()->formatDateTime(
            $timestamp,
            self::getDateFormatLength(),
            null,
        );
    }

    /**
     * @return string Formato do Yii de data
     */
    protected static function getDateFormatLength(): string
    {
        // N?o cacheia para ser alterado em tempo de execu??o
        if (empty(\Yii::app()->params['dateFormatLength'])) {
            return 'short';
        }

        $length = \Yii::app()->params['dateFormatLength'];

        return is_string($length) ? $length : 'short';
    }

    /**
     * @return string Formato do Yii de hora
     */
    protected static function getTimeFormatLength(): string
    {
        // N?o cacheia para ser alterado em tempo de execu??o
        if (empty(\Yii::app()->params['timeFormatLength'])) {
            return 'medium';
        }

        $length = \Yii::app()->params['timeFormatLength'];

        return is_string($length) ? $length : 'medium';
    }
}
