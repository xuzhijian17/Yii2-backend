<?php
namespace frontend\models;
use Yii;
class Model
{
	public static $tableName = '';
	public static $lastSql = ''; 
	public static $cache = false;
	public static $class = '';
	public static $db = '';
	#组合查询语句
	public static function _joinSql($param)
	{
		$class = get_called_class();
		$class::$tableName = isset($param['table']) && !empty($param['table']) ? $param['table'] : $class::$tableName;
		$sql = "select ";
		$sql .= isset($param['field']) && !empty($param['field']) ? $param['field'] : '*';
		$sql .= " from {$class::$tableName} ";
		$sql .= isset($param['alias']) && !empty($param['alias']) ? $param['alias']." " : '';
		$sql .= isset($param['join']) && !empty($param['join']) ? $param['join'] : '';
		$sql .= isset($param['where']) && !empty($param['where']) ? ' where '.$param['where'] : '';
		$sql .= isset($param['group']) && !empty($param['group']) ? ' group by '.$param['group'] : '';
		$sql .= isset($param['having']) && !empty($param['having']) ? ' having '.$param['having'] : '';
		$sql .= isset($param['order']) && !empty($param['order']) ? ' order by '.$param['order'] : '';
		$sql .= isset($param['limit']) && !empty($param['limit']) ? ' limit '.$param['limit'] : '';
		self::$cache = isset($param['cache']) && !empty($param['cache']) ? $param['cache'] : false;
		self::$lastSql = $sql;
		return $sql;
	}
	//批量查询
	public static function _query($param)
	{
		$data = [];
		$sql = is_array($param) ? self::_joinSql($param) : $param;
		$db = isset($param['db']) && !empty($param['db']) ? $param['db'] : 'db_juyuan';
		$command = Yii::$app->$db->createCommand($sql);
		return $command->queryAll();
	}
	//返回第一条
	public static function _find($param)
	{
		if(!$param)	return false;
		$data = []; $cache = Yii::$app->redis;
		$class = get_called_class(); $key = $class::$tableName;
		$field = isset($param['cache']) && !empty($param['cache']) ? $param['cache'] : false;
		$param['limit'] = 1;
		$sql = is_array($param) ? self::_joinSql($param) : $param;
		$db = isset($param['db']) && !empty($param['db']) ? $param['db'] : 'db_juyuan';
		// if($cache->hexists($key, $field)){
			// return json_decode($cache->hget($key, $field), true);
		// }
		$command = Yii::$app->$db->createCommand($sql);
		$data = $command->queryOne();
		if($field && $data){
			$cache->hset($key, $field, json_encode($data));
		}
		return $data;
	}
	#__call 非静态类 __callStatic 静态类 魔术方法 如果请求的方法不存在 就走这个魔术方法
	public static function __callStatic($method, $param)
	{
		$param = $param[0]; $data = false;
		if($method == 'query'){
			$data = self::_query($param);
		}
		if($method == 'find'){
			$data = self::_find($param);
		}
		// $msg = "sql : ".self::$lastSql."\n";
		// $msg .= "runtime : ".(microtime(true)-$stime)."\n";
		// error_log($msg, 3, Yii::$app->getRuntimePath()."/logs/sql.log");
		return $data;
	}
}