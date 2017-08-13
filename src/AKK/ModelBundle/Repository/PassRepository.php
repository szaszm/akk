<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:15 PM
 */

namespace AKK\ModelBundle\Repository;


use AKK\ModelBundle\Data\Pass;
use AKK\ModelBundle\Repository\Detail\RepositoryImpl;

class PassRepository
{
    /** @var  RepositoryImpl */
    private $repoImpl;

    /** @var  PassTypeRepository */
    private $passTypeRepository;

    /** @var  UserRepository */
    private $userRepository;

    private const TABLE_NAME = 'passes';

    /**
     * PassRepository constructor.
     * @param RepositoryImpl $repositoryImpl
     * @param PassTypeRepository $passTypeRepository
     * @param UserRepository $userRepository
     */
    public function __construct(RepositoryImpl $repositoryImpl, PassTypeRepository $passTypeRepository, UserRepository $userRepository)
    {
        $this->repoImpl = $repositoryImpl;
        $this->passTypeRepository = $passTypeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Pass $pass
     * @return array
     */
    private function objectToArray(Pass $pass): array
    {
        $result = [
            'id' => $pass->id
        ];
        if($pass->type !== null) $result['pass_type_id'] = $pass->type->id;
        if($pass->user !== null) $result['user_id'] = $pass->user->id;
        if($pass->obtainDate !== null) $result['obtain_date'] = date('Y-m-d H:i:s', $pass->obtainDate->getTimestamp());
        if($pass->validityStartDate !== null) $result['validity_start_date'] = date('Y-m-d H:i:s', $pass->validityStartDate->getTimestamp());

        return $result;
    }

    /**
     * @param array $row
     * @return Pass
     */
    private function arrayToObject(array $row): Pass
    {
        $result = new Pass();
        $result->id = $row['id'];
        if(array_key_exists('pass_type_id', $row)) {
            $result->type = $this->passTypeRepository->lazyGetById($row['pass_type_id']);
        }
        if(array_key_exists('user_id', $row)) {
            $result->user = $this->userRepository->lazyGetById($row['user_id']);
        }
        if(array_key_exists('obtain_date', $row)) {
            $result->obtainDate = \DateTime::createFromFormat('Y-m-d H:i:s', $row['obtain_date']);
        }
        if(array_key_exists('validity_start_date', $row)) {
            $result->validityStartDate = \DateTime::createFromFormat('Y-m-d H:i:s', $row['validity_start_date']);
        }

        return $result;
    }

    /**
     * @param int $id
     * @return Pass
     */
    public function getById(int $id): Pass
    {
        return $this->arrayToObject($this->repoImpl->findById(self::TABLE_NAME, $id));
    }

    /**
     * @param int $id
     * @return Pass
     */
    public function lazyGetById(int $id): Pass
    {
        $lazyFindResult = $this->repoImpl->tryFindById(self::TABLE_NAME, $id);
        if($lazyFindResult !== null) {
            return $this->arrayToObject($lazyFindResult);
        } else {
            return $this->arrayToObject(['id' => $id ]);
        }
    }

    /**
     * @param Pass $pass
     */
    public function persist(Pass $pass): void
    {
        $this->repoImpl->insert(self::TABLE_NAME, $this->objectToArray($pass));
    }

    public function initTable()
    {
        $this->repoImpl->createTable(self::TABLE_NAME, [
            'pass_type_id' => 'INTEGER',
            'user_id' => 'INTEGER',
            'obtain_date' => 'TEXT',
            'validity_start_date' => 'TEXT',
        ]);
    }

    public function purge()
    {
        $this->repoImpl->dropTable(self::TABLE_NAME);
    }

    public function findAll(): array
    {
        return $this->repoImpl->select(self::TABLE_NAME);
    }
}