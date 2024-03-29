<?php

namespace database;

use database\Exception\tableException;
use PDO;

class table
{
    protected PDO $db;
    protected string $name;
    protected string $dbName;

    public function __construct(PDO $db, string $name, string $dbName = '')
    {
        $this->db = $db;
        $this->name = $name;
        $this->dbName = $dbName;
    }

    //select from table

    /**
     * @param string $columns
     * @param int $fetch
     * @param string|false $where
     * @param string|false $order
     * @param string|false $group
     * @param string|false $having
     * @return mixed|void
     */
    public function select(string $columns, int $fetch = 0, string|false $where = false, string|false $order = false, string|false $group = false, string|false $having = false)
    {
        //if isset query
        if (!empty($where)) {
            $where = "WHERE $where";
        }
        if (!empty($order)) {
            $order = "ORDER BY $order";
        }
        if (!empty($group)) {
            $group = "GROUP BY $group";
        }
        if (!empty($having)) {
            $having .= " HAVING $having";
        }
        $terms = $where . " " . $order . " " . $group . " " . $having;
        if ($fetch != 0) {
            $data = $this->db->query("SELECT {$columns} FROM {$this->name} {$terms}", PDO::FETCH_ASSOC)->fetchall();
        } elseif ($fetch == 0) {
            $data = $this->db->query("SELECT {$columns} FROM {$this->name} {$terms}")->fetchall();
        }
        //if fetch is double-digit fetch row = the second digit of the number
        if ($fetch > 1) {
            $pos = substr($fetch, 1);
            $data = $data[$pos];
        }
        return $data;

    }

    //update from table

    /**
     * @param string|array $column
     * @param string|array|null $newValue
     * @param string|array|null $where
     * @return void
     */
    public function update(string|array $column, string|array|null $newValue = null, string|array|null $where = null)
    {
        if (!empty($column) && !empty($newValue) && !empty($where)) {
            $this->db->exec("UPDATE {$this->name} SET `{$column}` = '{$newValue}' WHERE {$where}");
        }
    }
    //delete from table

    /**
     * @param array|null $where
     * @return void
     */
    public function delete(array|null $where = null): void
    {
        if (!empty($where)) {
            $query = "DELETE from {$this->name} WHERE ";
            $values = array();
            foreach ($where as $key => $value) {
                if($value == "AND" || $value == "OR"){
                    $query .= " {$value} ";
                }else{
                    $query .= $key."=?";
                    $values[] = $value;
                }
            }
            $this->db->prepare($query)->execute($values);
        } else {
            $this->db->exec("DELETE FROM {$this->name}");
        }

    }


    /**
     * @param string $columns
     * @param ...$values
     * @return void
     */
    public function insert(string|array|null $columns = null, string|array|null $values = null)
    {
        $assoc = 0;

        if ($columns == null && $values == null) {
            $this->db->exec("INSERT INTO {$this->name} default values");
        }

        if ($columns != null && $values == null) {
            foreach ($columns as $key => $val) {
                if (is_string($key)) {
                    $assoc = 1;
                    break;
                }
            }

            if ($assoc == 1) {
                $preVal = $columns;
                $columns = array();
                foreach ($preVal as $column => $value) {
                    $columns[] = $column;
                    $values[] = $value;
                }
            }
        }

        if ($columns != null && $values != null) {
            if (is_string($columns)) {
                $columns = explode(",", $columns);
            }
            if (is_string($values)) {
                $values = explode(",", $values);
            }
            if (count($columns) == count($values)) {
                $val = '';
                $res = array();
                for ($i = 0; $i < count($values); $i++) {
                    $res[":value{$i}"] = $value;
                    if ($i != (count($values) - 1)) {
                        $val .= ":value" . $i . ",";
                    } else {
                        $val .= ":value" . $i;
                    }

                }
                $columns = implode(",", $columns);
                $this->db->prepare("INSERT INTO {$this->name}($columns) VALUES({$val})")->execute($res);
            } else {
                throw new tableException("Number of values doesn't match number of columns");
            }
        } else {
            throw new tableException("Two arguments required:columns,values");
        }

    }

    /**
     * @param array|string $query
     * @return array|false|void
     * @throws tableException
     */
    public function query(array|string $query)
    {
        if (is_array($query)) {
            if (count($query) != 3) {
                throw new tableException("Three arguments are required[query,mode,fetch method]");
            } else {
                if (is_string($query[0]) && is_integer($query[1]) && is_string($query[2])) {
                    $fetch = $query[2];
                    return ($this->db->query($query[0])->$fetch($query[1]));
                }
            }
        } elseif (is_string($query)) {
            return ($this->db->query($query, PDO::FETCH_ASSOC)->fetchAll());
        }
    }
}