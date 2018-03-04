<?php 
  
// 文件作者: gyh   
// 文件描述: mysql,mssql,access通用数据库操作类   
/**使用方法 
在 
WINDOWS XP  
IIS5 
MySQL 4.0 
access2003 
SQL server2000上测试通过,其他没测试 
要用SQL server要在php.ini中打开mssql的扩展 
要用access必须在windows下吧 
不同的数据库除了连接不同其他操作都是相同的  

一：连接数据库  
1:MySQL  
$dsn = array(   
'host'     => 'localhost:3306',   
'user'     => 'root',   
'password' => '123456',   
'database' => 'test'   
);   
$db=new DB($dsn,"mysql");  
   
2:SQL server  
$dsn = array(   
'host'     => 'localhost',   
'user'     => 'user',   
'password' => 'pw',   
'database' => 'test'   
);   
$db=new DB($dsn,"mssql");  
   
3:access  
   
$db=new DB("data.mdb","access"); //写上mdb文件文件名  
   
//不同的数据库除了连接不同下面其他操作都是相同的  
   
//获取所有记录   
$all_record = $db->get_all($table,"where type='student' order by id");   
$all_record是二维数组 比如$all_record[0]["name"];  
   
//获取指定位置的多条记录  
$some_record=$db->get_some("table",$start,$length,"order by id");  
或者$some_record=$db->get_some("table",$start,$length,"order by id desc");  
或者$some_record=$db->get_some("table",$start,$length,"order by id desc","where type='student'");  
$start是起始位置,$length是获取条数  
返回二维数组同$all_record  

//自定义查询
$cusom_record = result(sql语句,开始条条数,取多少条数,排序);
   
//获取一条   
$one_row = $db->get_one($table,"where type='student' order by id");   
$one_row是一维数组 比如$one_row["name"];  
   
//统计记录数  
$count = $db->count("table",);  
或者 $count = $db->count("table","where type='students' order by id"); 

//统计自定义总记录数 sql语句
$total = total(sql);
   
//插入一条记录   
$info_array = array(   
"name"   => "gyh",   
"type"   => "student",   
"age"    => "22",   
"gender" => "boy"   
);   
$db->insert("table1", $info_array);  
  
//更新一条记录   
$info_array = array(   
"name"   => "gyh",   
"type"   => "student",   
"age"    => "22",   
"gender" => "boy"   
);   
$db->update("table1", $info_array, "where name = 'gyh'");   
   
//删除记录   
$db->delete("table1", "where name = 'gyh'");   
   
//执行一条无结果集的SQL   
$db->execute("delete FROM table1 WHERE name = 'gyh'");   
*/   
  
class DB 
{ 
    private $db_type = NULL;  
    private $dsn = NULL;  
    private $Link_ID = 0;  
    private $Query_ID = 0;
    public $character_set_connection = 'utf8';  
    public $character_set_results    = 'utf8';  
     
    //函数创建一个新对象。如果成功，则该函数返回一个对象。如果失败，则返回 false
    function __construct ($dsn1,$type) { 
		$this->dsn=$dsn1; 
		$this->db_type=$type; 
		$this->connect(); 
    }

    function __destruct() {  
		switch($this->db_type) { 
			case "mysql": 
				mysql_close(); 
				break; 
			case "mssql": 
				mssql_close();    
				break; 
			case "access": 
			$this->Link_ID->Close;    
				break; 
		} 
    }  

    function connect() { 
       switch($this->db_type) { 
			case "mysql": 
				$this->Link_ID = @mysql_connect($this->dsn['host'], $this->dsn['user'], $this->dsn['password']); 

				if(!$this->Link_ID){
					//return die('Could not connect: ' . mysql_error());
					throw new Exception('Could not connect: ' . mysql_error());
				}

				mysql_query("set names utf8");
				if ($this->version() > '4.1') {  
					@mysql_query('SET character_set_connection= ' . $this->character_set_connection . ', character_set_results= ' . $this->character_set_results . ', character_set_client = binary', $this->link);  
					if ($this->version() > '5.0.1') 
						@mysql_query("SET sql_mode=''", $this->link);  
				}  
				mysql_select_db($this->dsn['database'], $this->Link_ID); 
				break; 
			case "mssql": 
				$this->Link_ID = @mssql_connect($this->dsn['host'], $this->dsn['user'], $this->dsn['password']); 
				mssql_select_db($this->dsn['database'], $this->Link_ID); 
				break; 
			case "access": 
				$this->Link_ID = new com("ADODB.Connection"); 
				$connstr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=". realpath($this->dsn);  
				$this->Link_ID->Open($connstr); 
				break; 
		} 
    } 

    public function query($Query_String) { 
       	switch($this->db_type) { 
			case "mysql": 
				$this->Query_ID = @mysql_query($Query_String, $this->Link_ID);  
				break; 
			case "mssql": 
				$this->Query_ID = @mssql_query($Query_String, $this->Link_ID);  
				break; 
			case "access": 
				$this->Query_ID = new com("ADODB.RecordSet");  
				if(!$this->Query_ID->Open($Query_String,$this->Link_ID,1,3)) return false; 
				break; 
       	} 
		if($this->Query_ID) return true; 
		else return false; 
    }

    public function execute($sql) { 
       $this->query($sql); 
		return true; 
    }

    //自定义查询
    public function result($sql,$start=null,$length=null) {  
		$result_array = array();  
		switch($this->db_type) { 
			case "mysql": 
				if($length != ''){
					$sql.=" LIMIT $start,$length";
				}
				$this->query($sql);  
				while($row = mysql_fetch_array($this->Query_ID,MYSQL_ASSOC))  
                	$result_array[]=$row;   
				break; 
		}     
        return $result_array;  
    }  

    public function insert($table,$dataArray) {  
       	while(list($key,$val) = each($dataArray)) {  
			$field .= "$key,";
			if(is_string($val)) 
				$value .= "'$val',";  
			else 
				$value .= "$val,"; 
		}  
       $field = substr($field, 0, -1);  
       $value = substr($value, 0, -1);  
       $sql = "INSERT INTO $table ($field) VALUES ($value)";  
       if(!$this->query($sql)) return false;  
       return true;  
    } 

    private function access_fetch_array($rs) { 
       	if($rs->eof){ 
			return false;
		}else{ 
			$f = $rs->Fields; 
			return $f;  
       	} 
    } 

    public function get_one($table,$where="",$col="*") {  
		$sql="SELECT $col FROM $table $where"; 
		$this->query($sql);  
		switch($this->db_type) { 
			case "mysql": 
				return mysql_fetch_array($this->Query_ID,MYSQL_ASSOC);  
			break; 
			case "mssql": 
				return mssql_fetch_array($this->Query_ID);     
			break; 
			case "access": 
				$array=array(); 
				$row=$this->access_fetch_array($this->Query_ID); 
				$n=0; 
				foreach($row as $key => $value) { 
					$array[$row[$key]->Name] = $array[$n] = ltrim($row[$key]->value); 
					$n++; 
				} 
				return $array;        
			break; 
		}        
    } 

    public function get_row($sql) {  
		$this->query($sql);  
		switch($this->db_type) { 
			case "mysql": 
				return mysql_fetch_array($this->Query_ID,MYSQL_ASSOC);  
			break; 
			case "mssql": 
				return mssql_fetch_array($this->Query_ID);     
			break; 
			case "access": 
				$array=array(); 
				$row=$this->access_fetch_array($this->Query_ID); 
				$n=0; 
				foreach($row as $key => $value) { 
					$array[$row[$key]->Name] = $array[$n] = ltrim($row[$key]->value); 
					$n++; 
				} 
				return $array;        
			break; 
		}        
    }

    public function get_all($table,$where="",$col="*") {  
        $sql="SELECT $col FROM $table $where"; 
		$result_array = array();  
        $this->query($sql);  
		switch($this->db_type) { 
			case "mysql": 
				while($row = mysql_fetch_array($this->Query_ID,MYSQL_ASSOC))  
                 $result_array[]=$row;   
				break; 
			case "mssql": 
				while($row = mssql_fetch_array($this->Query_ID))  
                $result_array[]=$row;     
				break; 
			case "access": 
				while($row=$this->access_fetch_array($this->Query_ID)) { 
					$array=array(); 
					$n=0; 
					foreach($row as $key => $value) 
					{  
						$array[$row[$key]->Name] = $array[$n] =ltrim($row[$key]->value); 
						$n++; 
					} 
					$result_array[]=$array; 
					$this->Query_ID->MoveNext(); 
				}  
				break; 
		}       
        return $result_array;  
    }  
  
    public function get_some($table,$start,$length,$order="",$where="",$col="*") {  
		$result_array = array();  
		switch($this->db_type) { 
			case "mysql": 
				$sql="SELECT $col FROM $table $where $order LIMIT $start,$length"; 
				$this->query($sql);  
				while($row = mysql_fetch_array($this->Query_ID,MYSQL_ASSOC))  
                	$result_array[]=$row;   
				break; 
			case "mssql": 
				$sum=$this->Count($table); 
				$cha=$sum-$start; 
				$o=strtolower($order); 
				$o=str_replace("order","",$o); 
				$o=str_replace("by","",$o); 
				$o=str_replace("desc","",$o); 
				$o=str_replace(" ","",$o); 
				if($where) 
					$where="AND ".str_replace("WHERE","",strtolower($where)); 
				if(strpos(strtolower($order),"DESC")) { 
					$order = str_replace("DESC","",strtolower($order)); 
					$sql="SELECT TOP $length $col FROM $table WHERE $o IN (SELECT TOP $cha $o FROM $table $order) $where $order DESC"; 
				}else{ 
					$sql="SELECT TOP $length $col FROM $table WHERE $o IN (SELECT TOP $cha $o FROM $table $order DESC) $where $order";
				} 
				$this->query($sql);  
				while($row = mssql_fetch_array($this->Query_ID))  
                	$result_array[]=$row;     
				break; 
			case "access": 
				$sum=$this->Count($table); 
				$cha=$sum-$start; 
				if(strpos(strtolower($order),"desc")) { 
					$order = str_replace("DESC","",strtolower($order)); 
					$sql="SELECT TOP $length $col FROM (SELECT TOP $cha * FROM $table $order) $where $order DESC"; 
				}else{ 
					$sql="SELECT TOP $length $col FROM (SELECT TOP $cha * FROM $table $order DESC) $where $order"; 
					$this->query($sql);  
					while($row=$this->access_fetch_array($this->Query_ID)) { 
						$array=array(); 
						$n=0; 
						foreach($row as $key => $value) {  
							$array[$row[$key]->Name] = $array[$n] =ltrim($row[$key]->value); 
							$n++; 
						} 
						$result_array[]=$array; 
						$this->Query_ID->MoveNext(); 
					}  
					break;
				} 
		}     
        return $result_array;  
    } 
    
    public function update($talbe, $dataArray, $where) {  
       	while(list($key,$val) = each($dataArray)) { 
			if(is_string($val)) 
				$value .= "$key = '$val',";  
			else 
				$value .= "$key = $val,"; 
		} 
       $value = substr($value, 0, -1);  
       $sql = "UPDATE $talbe SET $value $where";  
       if (!$this->query($sql)) return false;  
       return true;  
    }  
      
    public function delete($table, $where) {  
       $sql = "DELETE FROM $table $where";  
       if(!$this->query($sql)) return false;  
		return true;  
    } 

    public function Count($table, $where="") {  
       $row = $this->get_one($table,$where,"count(*) AS num"); 
       return $row['num'];  
    }

    //统计总数
    public function total($Query_String){
       	switch($this->db_type) { 
			case "mysql": 
				$this->Query_ID = @mysql_query($Query_String, $this->Link_ID);  
				break; 
			case "mssql": 
				$this->Query_ID = @mssql_query($Query_String, $this->Link_ID);  
				break; 
			case "access": 
				$this->Query_ID = new com("ADODB.RecordSet");  
				if(!$this->Query_ID->Open($Query_String,$this->Link_ID,1,3)) return false; 
				break; 
       	} 
		return mysql_num_rows($this->Query_ID);
    } 

    public function get_lastId(){
    	return mysql_insert_id();
    }

    private function version() {  
       switch($this->db_type) { 
			case "mysql": 
				return mysql_get_server_info($this->Link_ID);  
				break; 
			case "mssql": 
				break; 
			case "access":   
			break; 
		}   
    }  
} 

//类型MYSQL
$db = new DB($dsn,'mysql');

?>