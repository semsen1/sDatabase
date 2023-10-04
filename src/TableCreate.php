<?php
namespace database;
use database\Exception\tableException;
use PDO;
class TableCreate
{

    protected PDO $db;
    protected string $name;
    private $columns = '';
    private $key = '';
    private  $fkey = '';
    private int $TableExists = 0;
    protected $errors = 0;
    //here you need an instance of the db class and the name of the table

    /**
     * @param PDO $db
     * @param string $name
     * @param string $dbName
     */
    public function __construct(PDO $db, string $name,string $dbName = ''){
        if($this->errors == 0){
            $this->db = $db;
            $this->name = $name;
            $query = "SELECT COUNT(*) as exist FROM information_schema.tables WHERE table_name ='{$name}' AND (table_catalog = '{$dbName}' OR TABLE_SCHEMA = '{$dbName}')";
            $searchTable = $this->db->query($query, PDO::FETCH_ASSOC)->fetchall()[0];
            if($searchTable['exist'] != 0){
                $this->TableExists = 1;
            }
        }

    }
    //save the new column of the table

    /**
     * @param $column|
     * @return void
     */
    public function column($column){
        if($this->errors == 0){
            if($this->TableExists  == 0){
                $this->columns .= $column.",";
            }
        }
    }

    public function varchar($name,$count=255){
        if($this->errors == 0){
            if($this->TableExists  == 0){
                $this->columns .= "{$name} varchar($count)";
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
                $this->key .= "KEY({$key}),";
            }
        }
    }
    //create new table

    /**
     * @param string|array $columns
     * @param string $fTableName
     * @param string|array $fColumns
     * @param array|null $options:CONSTRAINT|c:DELETE|d:UPDATE|u
     * @return void
     * @throws tableException
     */
    public function fKeys(string|array $columns, string $fTableName, string|array $fColumns, array|null $options = null){
        if($this->errors == 0) {

            if ($this->TableExists == 0) {
                if( ( gettype($columns) != gettype($fColumns) ) ||
                    ( is_array($columns) && is_array($fColumns) && count($columns) != count($fColumns) ) ){
                    throw new tableException("The number of columns does not match ");
                }

                if(is_array($columns) && is_array($fColumns)){
                    $columns = implode(',',$columns);
                    $columns =  preg_replace("/,$/",'',$columns);

                    $fColumns = implode(',',$fColumns);
                    $fColumns =  preg_replace("/,$/",'',$fColumns);
                }

                $constraint = '';
                $onDelete = '';
                $onUpdata = '';
                $changeValues = ['CASCADE',"NULL","SET NULL","RESTRICT","ACTION","NO ACTION","DEFAULT","SET DEFAULT"];


                if($options != null){
                    if(array_key_exists("constraint",$options)){
                        $constraint = "CONSTRAINT {$options['constraint']} ";
                    }elseif(array_key_exists("c",$options)){
                        $constraint = "CONSTRAINT {$options['c']} ";
                    }

                    if(array_key_exists("DELETE",$options)){
                        if(!strPosArray::strPos($changeValues,$options['delete'])){
                            throw new tableException("invalid value for delete");
                        }

                        $onDelete = "ON DELETE {$options['delete']} ";
                    }elseif(array_key_exists("d",$options)){
                        if(!strPosArray::strPos($changeValues,$options['d'])){
                            throw new tableException("invalid value for delete");
                        }
                        $onDelete = " ON DELETE {$options['d']} ";
                    }

                    if(array_key_exists("UPDATE",$options)){
                        if(!strPosArray::strPos($changeValues,$options['update'])){
                            throw new tableException("invalid value for update");
                        }
                        $onUpdata = "ON UPDATE {$options['update']} ";
                    }elseif(array_key_exists("u",$options)){
                        if(!strPosArray::strPos($changeValues,$options['u'])){
                            throw new tableException("invalid value for update");
                        }
                        $onUpdata = " ON UPDATE {$options['u']} ";
                    }
                }
                $this->fkey .= "{$constraint}FOREIGN KEY ($columns) REFERENCES $fTableName($fColumns){$onDelete}{$onUpdata},";
            }
        }
    }

    /**
     * @return void
     */
    public function create(): void
    {
        if($this->errors == 0){
            if($this->TableExists  == 0){
                $query = $this->columns.$this->key.$this->fkey;
                $query = preg_replace("/,$/",'',$query);
                $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->name}({$query})");
            }
        }
    }
}