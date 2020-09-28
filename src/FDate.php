<?php

namespace fidelize\YiiPhactory;

class FDate
{
    /**
     * @param integer $timestamp Timestamp (se for nulo, pega a hora atual)
     * @return string A data/hora no formato do idioma atual do Yii
     */
    public static function getDateTime($timestamp = null)
    {
        if (null === $timestamp) {
            $timestamp = time();
        }

        return \Yii::app()->locale->getDateFormatter()->formatDateTime(
            $timestamp, self::getDateFormatLength(), self::getTimeFormatLength()
        );
    }

    /**
     * Recebe uma data em Timestamp e retorna uma string com a data formatada para
     * a language do usu?rio
     *
     * @param integer $timestamp Timestamp (se for nulo, pega a hora atual)
     * @return string A data no formato do idioma atual do Yii
     */
    public static function getDate($timestamp = null)
    {
        if (null === $timestamp) {
            $timestamp = time();
        }

        return \Yii::app()->locale->getDateFormatter()->formatDateTime(
            $timestamp, self::getDateFormatLength(), null
        );
    }

    /**
     * @return string Formato do Yii de data
     */
    protected static function getDateFormatLength()
    {
        // N?o cacheia para ser alterado em tempo de execu??o
        if (empty(\Yii::app()->params['dateFormatLength'])) {
            return 'short';
        } else {
            return \Yii::app()->params['dateFormatLength'];
        }
    }

    /**
     * @return string Formato do Yii de hora
     */
    protected static function getTimeFormatLength()
    {
        // N?o cacheia para ser alterado em tempo de execu??o
        if (empty(\Yii::app()->params['timeFormatLength'])) {
            return 'medium';
        } else {
            return \Yii::app()->params['timeFormatLength'];
        }
    }
}
