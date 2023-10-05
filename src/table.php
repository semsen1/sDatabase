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
     * @param $column
     * @param $newValue
     * @param $where
     * @return void
     */
    public function update($column, $newValue, $where)
    {
        if (!empty($column) && !empty($newValue) && !empty($where)) {
            $this->db->exec("UPDATE {$this->name} SET `{$column}` = '{$newValue}' WHERE {$where}");
        }
    }
    //delete from table

    /**
     * @param $where
     * @return void
     */
    public function delete(string|array|null $where = null): void
    {

        if (isset($where) && !empty($where)) {
            if (is_string($where)) {
                $this->db->exec("DELETE FROM {$this->name} WHERE {$where}");
            } elseif (is_array($where)) {
                $where = http_build_query($where, ' AND ', '');
                $this->db->exec("DELETE FROM {$this->name} WHERE {$where}");
            }

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
                for ($i = 0; $i < count($values); $i++) {
                    if ($i != (count($values) - 1)) {
                        $val .= ":value" . $i . ",";
                    } else {
                        $val .= ":value" . $i;
                    }

                }
                $columns = implode(",", $columns);

                $Insert = $this->db->prepare("INSERT INTO {$this->name}($columns) VALUES({$val})");
                //bind vN with valueN
                for ($i2 = 0; $i2 < count($values); $i2++) {
                    $Insert->bindValue(":value" . $i2, $values[$i2]);
                }
                $Insert->execute();

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