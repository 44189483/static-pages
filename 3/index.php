<?php
require('../global.php');
require('../class.db.php');
require('libs/Smarty.class.php');
require('smarty.php');

$row = $db->get_one(PRFIX.'article','WHERE articleId=15');

$smarty->assign("title", $row['articleTitle']);

$smarty->assign("content", $row['articleContent']);

$content = $smarty->fetch("templates/index.html");
//这里的 fetch() 就是获取输出内容的函数,现在$content变量里面,就是要显示的内容了
$fp = fopen("archives/{$row['articleId']}.html", "w");
fwrite($fp, $content);
fclose($fp);

?> 