<?php
//将数据存入二维数组
$con = array(
	array('文章标题1','文章内容1'),
	array('文章标题2','文章内容2'),
	array('文章标题3','文章内容3'),
	array('文章标题4','文章内容4'),
	array('文章标题5','文章内容5')
);
 
foreach($con as $id => $val){ //循环生成

	$title = $val[0];
	$content = $val[1];
	$path = "article-".($id+1).".html";
	 
	//替换example内容，并获取内容赋值给$str
	$fp = fopen("example.html","r");
	$str = fread($fp,filesize("example.html"));
	$str = str_replace("{title}",$title,$str);
	$str = str_replace("{content}",$content,$str);
	fclose($fp);
 
	//新建空白文件，将$str写入
	$handle = fopen($path,"w");
	fwrite($handle,$str);
	fclose($handle);
	 
	echo "生成".$path."<br/>";
}
?>