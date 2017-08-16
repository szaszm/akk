<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:16 PM
 */

namespace AKK\ModelBundle\Repository\Detail;


use Doctrine\DBAL\Connection;

class RepositoryImpl
{
    private $conn;
    private $cache;

    /**
     * RepositoryImpl constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
        $this->cache = [];
    }

    /**
     * So much SQLi
     * @param string $fromArg
     * @param array $constraints
     * @return array
     */
    public function select(string $fromArg, array $constraints = []): array
    {
        $sql = 'SELECT * FROM '.$fromArg;
        $whereargs = [];
        foreach ($constraints as $key => $value) {
            if(is_numeric($key)) {
                if(is_array($value)) {
                    $orexpr = [];
                    foreach ($value as $okey => $item) {
                        if(is_numeric($okey)) {
                            $orexpr[] = $item;
                        } else {
                            $orexpr[] = "`$okey` = '$item'";
                        }
                    }
                    $whereargs[] = '('.implode(' OR ', $orexpr).')';
                } else {
                    $whereargs[] = $value;
                }
            } else {
                $whereargs[] = "`$key` = '$value'";
            }
        }
        if(count($constraints)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereargs);
        }

        //var_dump($sql);die();
        $queryResult = $this->conn->prepare($sql);
        $queryResult->execute();
        //$queryResult = $this->conn->executeQuery($sql);

        $all = $queryResult->fetchAll(\PDO::FETCH_ASSOC);
        return $all;
    }

    /**
     * @param string $from
     * @param int $id
     * @return array
     */
    public function findById(string $from, $id): array
    {
        $result = $this->tryFindById($from, $id);
        if($result !== null) {
            return $result;
        }

        if(!isset($this->cache[$from])) {
            $this->cache[$from] = [];
        }
        return $this->cache[$from][$id] = $this->select($from, ['id' => $id]);
    }

    /**
     * If the row is in the cache, then returns it, returns null otherwise
     * @param string $from
     * @param int $id
     * @return array|null
     */
    public function tryFindById(string $from, $id)
    {
        if(isset($this->cache[$from]) && isset($this->cache[$from][$id])) {
            return $this->cache[$from][$id];
        }
        return null;
    }

    /**
     * @param string $target
     * @param array $row
     * @return int
     */
    public function insert(string $target, array $row): int
    {
        $pieces = array_map(function($val) { return '\''.$val.'\''; }, array_values($row));
        $sql = 'INSERT INTO '.$target
            .' ('.implode(',', array_keys($row)).') VALUES ('
            .implode(',', $pieces).')';

        $this->conn->exec($sql);
        return intval($this->conn->lastInsertId());
    }

    /**
     * @param string $target
     * @param array $row
     */
    public function updateById(string $target, array $row): void
    {
        $sql = 'UPDATE '.$target.' SET ';
        $updateargs = [];
        foreach ($row as $key => $item) {
            $updateargs[] = "`$key`='$item'";
        }

        $sql .= implode(',', $updateargs);

        $this->conn->exec($sql);
    }

    /**
     * @param string $name
     * @param array $columns associative array mapping columns to their types
     */
    public function createTable(string $name, array $columns): void
    {
        $sql = "CREATE TABLE `$name`(id INTEGER PRIMARY KEY";

        foreach ($columns as $name => $type) {
            $sql .= ",`$name` $type";
        }

        $sql .= ')';

        $this->conn->exec($sql);
    }

    /**
     * @param string $tableName
     * @return bool
     */
    public function tableExists(string $tableName): bool
    {
        $sql = "SELECT COUNT(name) FROM `sqlite_master` WHERE name = '$tableName'";
        $qres = $this->conn->executeQuery($sql);
        $res = $qres->fetchColumn(0);
        return count($res) == 1 && intval($res[0]) > 0;
    }

    /**
     * @param string $tableName
     */
    public function dropTable(string $tableName): void
    {
        if($this->tableExists($tableName)) {
            $this->conn->exec("DROP TABLE `$tableName`");
        }
    }
}