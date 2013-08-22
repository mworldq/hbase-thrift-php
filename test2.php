<?php
 
/***
Thrift Test Class by xinqiyang
 
*/
 
ini_set('display_error', E_ALL);
 
$GLOBALS['THRIFT_ROOT'] = './';
 
 
/* Dependencies. In the proper order. */
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Transport/TTransport.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Protocol/TProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Transport/TBufferedTransport.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Type/TMessageType.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Factory/TStringFuncFactory.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/StringFunc/TStringFunc.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/StringFunc/Core.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Type/TType.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Exception/TException.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Exception/TTransportException.php';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift/Exception/TProtocolException.php';
 
 
 
 
 
/* Remember these two files? */
require_once $GLOBALS['THRIFT_ROOT'].'/packages/Hbase/Types.php';
require_once $GLOBALS['THRIFT_ROOT'].'/packages/Hbase/Hbase.php';
 
 
 
 
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\TSocketPool;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TBufferedTransport;
use Hbase\HbaseClient;
use Hbase\ColumnDescriptor;
 
 
//define host and port
$host = '192.168.8.103';
$port = 9090;
$socket = new Thrift\Transport\TSocket($host, $port);
 
$transport = new TBufferedTransport($socket);
$protocol = new TBinaryProtocol($transport);
// Create a calculator client
$client = new HbaseClient($protocol);
$transport->open();
 
 
 //获取表列表   
$tables = $client->getTableNames();  
sort($tables);  
foreach ($tables as $name) {  
  
    echo( "  found: {$name}\n" );  
}  
   
//创建新表student   
$columns = array(  
    new ColumnDescriptor(array(  
        'name' => 'id:',  
        'maxVersions' => 10  
    )),  
    new ColumnDescriptor(array(  
        'name' => 'name:'  
    )),  
    new ColumnDescriptor(array(  
        'name' => 'score:'  
    )),  
);  
  
$tableName = "student";  
/*
try {  
    $client->createTable($tableName, $columns);  
} catch (AlreadyExists $ae) {  
    echo( "WARN: {$ae->message}\n" );  
}  
*/
//获取表的描述   
  
$descriptors = $client->getColumnDescriptors($tableName);  
asort($descriptors);  
foreach ($descriptors as $col) {  
    echo( "  column: {$col->name}, maxVer: {$col->maxVersions}\n" );  
}  
  
//修改表列的数据   
$row = '2';  
$valid = "foobar-\xE7\x94\x9F\xE3\x83\x93";  
$mutations = array(  
    new Mutation(array(  
        'column' => 'score',  
        'value' => $valid  
    )),  
);  
$client->mutateRow($tableName, $row, $mutations);  
  
  
//获取表列的数据   
$row_name = '2';  
$fam_col_name = 'score';  
$arr = $client->get($tableName, $row_name, $fam_col_name);  
// $arr = array   
foreach ($arr as $k => $v) {  
// $k = TCell   
    echo ("value = {$v->value} , <br>  ");  
    echo ("timestamp = {$v->timestamp}  <br>");  
}  
  
$arr = $client->getRow($tableName, $row_name);  
// $client->getRow return a array   
foreach ($arr as $k => $TRowResult) {  
// $k = 0 ; non-use   
// $TRowResult = TRowResult   
    var_dump($TRowResult);  
}  
  
$transport->close();  
?>  