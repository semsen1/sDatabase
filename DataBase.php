<?php
namespace database;
require_once("vendor/autoload.php");
error_reporting(E_ALL);
use database\db;
use database\table;
use database\TableCreate;
use database\TableOperations;
use database\Exception\tableException;
use PDO;



class DataBase
{
    private db $db;
    private $dbName;
    private array $tables;
    /**
     * @param string $dbName
     * @param string $login
     * @param string $password
     * @param array|null $params params[host = 127.0.0.1|dbms = mysql|port = 3306]
     * @return void
     */
    public function connect(string $dbName, string $login, string $password = '',Array|null $params = null): void
    {
        $this->dbName = $dbName;
        $this->db = new db($dbName,$login,$password,$params);
    }

    /**
     * @param $tableName
     * @return table
     */
    public function addTable($tableName){

        $this->tables[$tableName] = new TableOperations($this->db->getDb(),$tableName,$this->dbName);
        return $this->tables[$tableName];
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
                throw new tableException("Table {$tableName} not connected");
            }
        }
    }

    /**
     * @param $tableName
     * @return table
     */
    public function table($tableName): TableOperations
    {
        if(array_key_exists($tableName,$this->tables)){
            return $this->tables[$tableName];
        }
    }

    /**
     * @param string $name
     * @param array|string|null $arguments
     * @return table|array
     */
    public function __call(string $name, array $arguments = null) :TableOperations|array
    {
        if(array_key_exists($name,$this->tables) && $arguments == null){
            return $this->tables[$name];
        }elseif(array_key_exists($name,$this->tables) && $arguments != null){
            if(count($arguments) == 1){
                return $this->tables[$name]->query($arguments[0]);
            }
//            else{
//                return $this->getTable($name)->select(...$arguments);
//            }

        }
    }

    public function getTables(): array
    {
        return $this->tables;
    }

}

$laravel = new DataBase();


//new \PDO("pgsql:host=127.0.0.1;port=5432;dbname=postgres","postgres","newPassword");
$laravel->connect("postgres12",'postgres','newPassword',['dbms'=>"pgsql","port"=>"5432"]);
$laravel->addTable("base");
$laravel->base()->column("base int DEFAULT 10");
$laravel->base()->create();
$laravel->base()->insert(["base"=>122]);
//$laravel->base()->delete(["base"=>"asf","123"=>"2144"]);
print_r($laravel->base("SELECT * FROM base"));

$laravelM = new DataBase();
$laravelM->connect("laravel",'root','root1234',['dbms'=>"mysql","port"=>"3306"]);
$laravelM->addTable("base");
//$laravelM->base()->column("base int");
//$laravelM->base()->create();
//$laravel->newTable("users2");
//$laravel->newTable("users");
//$laravel->newTable("someBase");
//
//print_r($laravel->users("*",10,"id % 2 = 0"));
//

//print "\r\n";
//
//print_r($laravel->users()->select("COUNT(*) as count",10)['count']);


?>

