<?php

namespace DataBase;
use PDO;

class table extends DataOperation
{

    protected PDO $db;
    protected string $name;
    protected $columns = '';
    protected $key;
    protected int $TableExists = 0;
    protected $errors = 0;
    //here you need an instance of the db class and the name of the table

    /**
     * @param PDO $db
     * @param string $name
     */
    public function __construct(PDO $db, string $name){
        if($this->errors == 0){
            $this->db = $db;
            $this->name = $name;
            $searchTable = $this->db->query("SHOW TABLES",PDO::FETCH_ASSOC)->fetchall();
            foreach ($searchTable as $tables) {
                if(array_values($tables)[0] == $this->name){
                    $this->TableExists = 1;
                }
            }
        }

    }
    //save the new column of the table

    /**
     * @param $column|
     * @return void
     */
    public function columns($column){
        if($this->errors == 0){
            if($this->TableExists  == 0){
                $this->columns .= $column.",";
            }
        }

    }
    //new index for column

    /**
     * @param $key
     * @return void
     */
    public function keys($key): void
    {
        if($this->errors == 0){
            if($this->TableExists  == 0){
                $this->key .= "KEY({$key})".",";
            }
        }
    }
    //create new table

    /**
     * @return void
     */
    public function create(): void
    {
        if($this->errors == 0){
            if($this->TableExists  == 0){
                $query = $this->columns.$this->key;
                $query = preg_replace("/,$/",'',$query);
                $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->name}({$query})");
            }
        }
    }
}