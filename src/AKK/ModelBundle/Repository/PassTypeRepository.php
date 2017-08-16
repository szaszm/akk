<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:15 PM
 */

namespace AKK\ModelBundle\Repository;


use AKK\ModelBundle\Data\Pass;
use AKK\ModelBundle\Data\PassType;
use AKK\ModelBundle\Repository\Detail\RepositoryImpl;

class PassTypeRepository
{
    /** @var  RepositoryImpl */
    private $repoImpl;

    private const TABLE_NAME = 'pass_types';

    /**
     * PassTypeRepository constructor.
     * @param RepositoryImpl $repositoryImpl
     */
    public function __construct(RepositoryImpl $repositoryImpl)
    {
        $this->repoImpl = $repositoryImpl;
    }

    /**
     * @param PassType $passType
     * @return array
     */
    private function objectToArray(PassType $passType): array
    {
        $result = [];
        if($passType->id !== null) $result['id'] = $passType->id;
        if($passType->name !== null) $result['name'] = $passType->name;
        if($passType->displayName !== null) $result['display_name'] = $passType->displayName;
        if($passType->priceHuf !== null) $result['price_huf'] = $passType->priceHuf;
        if($passType->validitySeconds !== null) $result['validity_seconds'] = $passType->validitySeconds;

        return $result;
    }

    private function hidrateFromArray(PassType $passType, array $row): void
    {
        if(array_key_exists('name', $row)) $passType->name = $row['name'];
        if(array_key_exists('display_name', $row)) $passType->displayName = $row['display_name'];
        if(array_key_exists('price_huf', $row)) $passType->priceHuf = $row['price_huf'];
        if(array_key_exists('validity_seconds', $row)) $passType->validitySeconds = $row['validity_seconds'];
    }

    /**
     * @param array $row
     * @return PassType
     */
    private function arrayToObject(array $row): PassType
    {
        $passType = new PassType();
        $passType->id = $row['id'];
        $this->hidrateFromArray($passType, $row);
        return $passType;
    }

    /**
     * @param int $id
     * @return PassType
     */
    public function getById($id): PassType
    {
        return $this->arrayToObject($this->repoImpl->findById(self::TABLE_NAME, $id));
    }

    /**
     * @param int $id
     * @return PassType
     */
    public function lazyGetById($id): PassType
    {
        $lazyFindResult = $this->repoImpl->tryFindById(self::TABLE_NAME, $id);
        if($lazyFindResult !== null) {
            return $this->arrayToObject($lazyFindResult);
        } else {
            return $this->arrayToObject(['id' => $id ]);
        }
    }

    /**
     * @param PassType $passType
     */
    public function persist(PassType $passType): void
    {
        $passType->id = $this->repoImpl->insert(self::TABLE_NAME, $this->objectToArray($passType));
    }

    public function initTable()
    {
        $this->repoImpl->createTable(self::TABLE_NAME, [
            'name' => 'TEXT',
            'display_name' => 'TEXT',
            'price_huf' => 'INTEGER',
            'validity_seconds' => 'INTEGER',
        ]);

        $passTypesToInit = [
            ['name' => 'havi', 'display_name' => 'Havi bérlet', 'price_huf' => 9500, 'validity_seconds' => 2592000],
            ['name' => 'diak_havi', 'display_name' => 'Havi diákbérlet', 'price_huf' => 3450, 'validity_seconds' => 2592000],
            ['name' => 'nyugger_havi', 'display_name' => 'Havi nyugdíjas bérlet', 'price_huf' => 3330, 'validity_seconds' => 2592000],
            ['name' => 'felhavi', 'display_name' => 'Félhavi bérlet', 'price_huf' => 6300, 'validity_seconds' => 1296000],
        ];

        foreach ($passTypesToInit as $item) {
            $this->repoImpl->insert(self::TABLE_NAME, $item);
        }
    }

    public function purge(): void
    {
        $this->repoImpl->dropTable(self::TABLE_NAME);
    }

    /**
     * @return PassType[]
     */
    public function findAll(): array
    {
        return $this->find([]);
    }

    /**
     * @param array $constraints
     * @return PassType[]
     */
    public function find(array $constraints): array
    {
        return array_map(function($row) {
            return $this->arrayToObject($row);
        }, $this->repoImpl->select(self::TABLE_NAME, $constraints));
    }

    /**
     * @param array $constraints
     * @return PassType|null
     * @throws \Exception
     */
    public function findOne(array $constraints): PassType
    {
        $result = $this->find($constraints);
        if(count($result) > 1) {
            throw new \Exception('Non-unique query result');
        }
        if(count($result) == 0) return null;
        return $result[0];
    }

    public function load(PassType $passType): void
    {
        if($passType->name !== null) return;
        $dbres = $this->repoImpl->select(self::TABLE_NAME, ['id' => $passType->id ])[0];
        $this->hidrateFromArray($passType, $dbres);
    }
}