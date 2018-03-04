<?php
	/**
	 * SMARTY配置文件
	 * @authors Sunguoliang
	 * @date    2016-11-09 09:10:00
	 * @version 1.0
	*/

	$smarty = new Smarty;
	$smarty->template_dir = 'templates/';
	$smarty->compile_dir = 'templates_c/';
	$smarty->caching = false;
	$smarty->left_delimiter = '{{';
	$smarty->right_delimiter = '}}';
?>