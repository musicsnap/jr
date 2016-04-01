<?php
/**
 * 这是一个基于MySQLi实现的数据库类包装，其主要目的在于封装常用的操作以及错误诊断
 * 
 * @author wps2000
 * @email	zhangsilly@gmail.com
 * @date	2010-10-28
 * @package db
 */

/**
 * 一个简单的类型包装
 *
 */
class Db_Expr
{
	private $_expr;
	public function __construct($expr)
	{
		$this->_expr	= $expr;
	}
	public function __toString()
	{
		return $this->_expr;
	}
}