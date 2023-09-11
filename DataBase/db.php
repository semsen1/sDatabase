<?php

namespace DataBase;
use PDO;
use PDOException;

class db{
    private PDO $db;
    protected string $host = "127.0.0.1";
    protected string $dbms = "mysql";
    protected string $port = '3306';
    protected int $errorReporting = 0;

    protected array $errors = [];


    /**
     * @param string $dbName
     * @param string $login
     * @param string $password
     * @param array|null $params params[host = 127.0.0.1|dbms = mysql|port = 3306]
     */

    public function __construct(string $dbName, string $login, string $password,Array|null $params = null){
        $host = $this->host;
        $dbms = $this->dbms;
        $port = $this->port;
        if($params != null){
            if(array_key_exists("host",$params)){
                $host = $params['host'];
            }

            if(array_key_exists("dbms",$params)){
                $dbms = $params['dbms'];
            }

            if(array_key_exists("port",$params)){
                $port = $params['port'];
            }
        }

        if(!empty($dbName) && !empty($login) && !empty($this->host)){
            //if exists
            try{
                $this->db = new PDO("{$dbms}:host={$this->host};port={$port};dbname={$dbName}",$login,$password);
                $this->db->query("SET NAMES 'utf8'");
                $this->db->query("SET CHARACTER SET 'utf8'");
                $this->db->query("SET SESSION collation_connection = 'utf8_general_ci'");
            }catch(PDOexception $e){
                //if not, create
                if($dbms == "mysql"){
                    try {
                        $db = new PDO("{$dbms}:host={$host};port={$port}",$login,$password);
                        $db->exec("CREATE DATABASE {$dbName}");
                        $this->db = new PDO("mysql:host={$host};dbname={$dbName}",$login,$password);
                        $this->db->query("SET NAMES 'utf8'");
                        $this->db->query("SET CHARACTER SET 'utf8'");
                        $this->db->query("SET SESSION collation_connection = 'utf8_general_ci'");
                    }catch (PDOException $error){
                        $this->errors[] = ["db"=>"create"];
                    }

                }else{
                    $this->errors[] = ["db"=>"connection"];
                }


            }
        }else{
            if(empty($dbName)){
                $this->errors[] = ["password"=>"required"];
            }
            if(empty($login)){
                $this->errors[] = ["login"=>"required"];
            }
            if(empty($this->host)){
                $this->errors[] = ["host"=>"required"];
            }
        }

        if($this->errorReporting == 1){
            $this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }

    }

    /**
     * @param integer $accurate 0|1
     * @return void
     */
    public function error_reporting(int $accurate): void
    {
        $this->errorReporting = 1;
    }
    // get db from outside;


    public function getErrors($error = null){
        if(count($this->errors) != 0){
            if($error == null){
                return $this->errors;
            }else{
                if(array_key_exists($error,$this->errors)){
                    return $this->errors[$error];
                }
            }
        }
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }



}

$asf = new db("laravel",'root','');
$asf->error_reporting(1);
print_r($asf->getErrors());

