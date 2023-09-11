<?php

namespace DataBase;
use PDO;

class table extends DataOperation
{

    protected $db;
    protected $name;
    protected $columns;
    protected $key;
    protected $Texists = 0;
    protected $errors = 0;
    //here you need an instance of the db class and the name of the table
    public function __construct(PDO $db,string $name){
        if($this->errors == 0){
            $this->db = $db;
            $this->name = $name;
            $searchTable = $this->db->query("SHOW TABLES",PDO::FETCH_ASSOC)->fetchall();
            foreach ($searchTable as $tables) {
                if(array_values($tables)[0] == $this->name){
                    $this->Texists = 1;
                }
            }
        }

    }
    //save the new column of the table
    public function columns($column){
        if($this->errors == 0){
            if($this->Texists  == 0){
                $this->columns .= $column.",";
            }
        }

    }
    //new index for column
    public function keys($key){
        if($this->errors == 0){
            if($this->Texists  == 0){
                $this->key .= "KEY({$key})".",";
            }
        }
    }
    //create new table
    public function create(){
        if($this->errors == 0){
            if($this->Texists  == 0){
                $query = $this->columns.$this->key;
                $query = preg_replace("/,$/",'',$query);
                $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->name}({$query})");
            }
        }
    }
}