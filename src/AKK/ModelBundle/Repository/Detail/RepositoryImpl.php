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

    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
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
                $whereargs[] = $value;
            } else {
                $whereargs[] = "`$key` = '$value'";
            }
        }
        if(count($constraints)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereargs);
        }

        $queryResult = $this->conn->query($sql);

        $all = $queryResult->fetchAll();
        return $all;
    }

    /**
     * @param string $from
     * @param int $id
     * @return array
     */
    public function findById(string $from, int $id): array
    {
        return $this->select($from, ['id' => $id]);
    }

    /**
     * @param string $target
     * @param array $row
     */
    public function insert(string $target, array $row): void
    {
        $sql = 'INSERT INTO '.$target
            .' ('.implode(',', array_keys($row)).') VALUES ('
            .implode(',', array_values($row)).')';

        $this->conn->exec($sql);
    }

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
}