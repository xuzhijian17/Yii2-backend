<?php
namespace institution\service;

use Yii;
use yii\mongodb\file\Query;

class GridFsService
{
    public function getMongoGridFs($_id)
    {
        $query = new Query();
        return $query->where(["_id"=>$_id])->from('fs')->one();
    }
}