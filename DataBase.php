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

    /**
     * @param $tableName
     * @return table
     */
    protected function getTable($tableName): table
    {
        if(array_key_exists($tableName,$this->tables)){
            return $this->tables[$tableName];
        }
    }

    /**
     * @param string $name
     * @param array|string|null $arguments
     * @return table
     */
    public function __call(string $name, array $arguments = null)
    {

        if(array_key_exists($name,$this->tables) && $arguments == null){
            return $this->getTable($name);
        }elseif(array_key_exists($name,$this->tables) && $arguments != null){
            if(count($arguments) == 1){
                return $this->getTable($name)->query($arguments[0]);
            }
//            return $this->getTable($name)->select($arguments[0]);
        }
    }

    public function getTables(): array
    {
        return $this->tables;
    }

}

$laravel = new DataBase();
$laravel->connect("laravel",'root','');
$laravel->newTable("users2");
$laravel->newTable("users");
$laravel->newTable("someBase");

print_r($laravel->someBase(["SELECT * FROM users"=>PDO::FETCH_ASSOC,'fetchAll']));
//$laravel->someBase()->columns("id smallint unsigned auto_increment PRIMARY KEY");
//$laravel->someBase()->columns("path varchar(60) not null");
//$laravel->someBase()->columns("owner_id smallint unsigned not null");
//$laravel->someBase()->columns("team_id smallint unsigned");
//$laravel->someBase()->columns("author smallint unsigned");
//$laravel->someBase()->columns("name varchar(255) not null");
//$laravel->someBase()->columns("last_update datetime not null");
//$laravel->someBase()->columns("tags text");
//$laravel->someBase()->columns("status int(1) not null");
//$laravel->someBase()->keys("id_p");
//$laravel->someBase()->keys("owner_id");
//$laravel->someBase()->keys("team_id");
//$laravel->someBase()->create();


print_r($laravel->users()->select("COUNT(*) as count",10)['count']);
