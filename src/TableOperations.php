<?php

namespace database;

use database\table;
use database\TableCreate;
use PDO;

class TableOperations
{
    protected $TableCreate;
    protected $TableQuery;
    protected $CreateMethods;
    protected $QueryMethods;

    public function __construct(PDO $db, string $name, string $dbName = '')
    {
        $this->TableCreate = new TableCreate($db, $name, $dbName);
        $this->TableQuery = new Table($db, $name, $dbName);
        $this->CreateMethods = get_class_methods($this->TableCreate);
        $this->QueryMethods = get_class_methods($this->TableQuery);
    }

    public function __call(string $name, array $arguments)
    {

        if(in_array($name,$this->CreateMethods)){
            return call_user_func([$this->TableCreate,$name],...$arguments);
        }else{
            return call_user_func([$this->TableQuery,$name],...$arguments);
        }

    }
}