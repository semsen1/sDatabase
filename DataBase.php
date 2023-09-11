<?php
spl_autoload_register(function ($class){
    require_once($class.".php");
});

use DataBase\db;
use DataBase\table;
use Exception\tableException;


class DataBase
{
    private db $db;
    private array $tables;
    /**
     * @param string $dbName
     * @param string $login
     * @param string $password
     * @param array|null $params params[host = 127.0.0.1|dbms = mysql|port = 3306]
     */
    public function connect(string $dbName, string $login, string $password,Array|null $params = null){
        $this->db = new db($dbName,$login,$password,$params);
    }

    /**
     * @param $tableName
     * @return void
     */
    public function newTable($tableName){
        $this->tables[$tableName] = new table($this->db->getDb(),$tableName);
    }

    /**
     * @param $tableName
     * @return void|
     */
    public function flushTable($tableName = null){
        if($tableName == null){
            $this->tables = [];
        }else{
            if(array_key_exists($tableName,$this->tables)){
                unset($this->tables[$tableName]);
            }else{
                throw new tableException("Таблица {$tableName} не подключена",debug_backtrace()[0]['line']);
            }
        }
    }

    public function __call(string $name, array $arguments)
    {
        if(array_key_exists($name,$this->tables)){
            return $this->tables[$name];
        }
    }

    public function getTables(){
        return $this->tables();
    }

}

$laravel = new DataBase();
$laravel->connect("laravel",'root','');
$laravel->newTable("users2");
$laravel->newTable("users");
print_r($laravel->users()->select("COUNT(*) as count",10)['count']);
