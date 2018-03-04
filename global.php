<?php 
	/**
	 * 基本配置文件
	 * @authors Sunguoliang
	 * @date    2016-11-09 09:10:00
	 * @version 1.0
	*/

	/***数据库设置START***/
	
	$DB_HOST = 'localhost';//主机名
	
	$DB_USER = 'root';//用户
	
	$DB_PWD = 'root';//密码
	
	$DB_NAME = 'axdrice';//数据库名称
	
	$dsn = array(
		'host'     => $DB_HOST, 
		'user'     => $DB_USER,
		'password' => $DB_PWD, 
		'database' => $DB_NAME   
	);

	/*获取当前正确时区时间*/
	$DB_TIME = 'PRC';

	//设置当前时区
	date_default_timezone_set('PRC');

	define('PRFIX','axdrice_');//数据表前缀

	/***数据库设置END***/
	
?>