<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <title>新闻中心</title> 
</head>  
<body>  
    <div class="container">  
        <h3>新闻列表</h3>  
        <ul class="list-group">  
            <?php foreach ($news as $key => $value) { ?>  
            <li class="list-group-item"><a href="#"><?php echo $value['articleTitle'];?></a></li>  
            <?php } ?>  
        </ul>  
    </div>  
</body>  
</html>  