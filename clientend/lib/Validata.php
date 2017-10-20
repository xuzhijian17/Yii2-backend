<?php
namespace clientend\lib;

use Yii;
/**
 *验证方法相关类
 */
class Validata
{
    /**
     * 验证基金代码
     * @param string $fundcode 基金代码
     * @return bool true/false
     */
    public static function validata_fundcode($fundcode)
    {
        return preg_match('/^\d{6}$/', $fundcode)?true:false;
    }
}