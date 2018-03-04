<?php
require('../global.php');
require('../class.db.php');

$news = array();
$rows = $db->get_all(PRFIX.'article','WHERE articleType=1');
if($rows){
	foreach($rows as $k => $v){
		$news[] = $v;
	}
}

//print_r($news);

ob_start(); //开启缓存区  
  
//引入模板文件  
require_once('singwa.php'); //动态文件singwa.php界面同样进过缓冲区  
file_put_contents('index.html', ob_get_contents()); 

?> 