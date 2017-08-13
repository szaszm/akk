<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/11/17
 * Time: 7:15 PM
 */

namespace AKK\ModelBundle\Repository;


use AKK\ModelBundle\Data\User;
use AKK\ModelBundle\Repository\Detail\RepositoryImpl;

class UserRepository
{
    /** @var  RepositoryImpl */
    private $repoImpl;

    private const TABLE_NAME = 'users';

    /**
     * UserRepository constructor.
     * @param RepositoryImpl $repositoryImpl
     */
    public function __construct(RepositoryImpl $repositoryImpl)
    {
        $this->repoImpl = $repositoryImpl;
    }

    /**
     * @param User $user
     * @return array
     */
    private function objectToArray(User $user): array
    {
        $result = [
            'id' => $user->id
        ];
        if($user->username !== null) $result['username'] = $user->username;
        if($user->password !== null) $result['password'] = $user->password;
        if($user->email !== null) $result['email'] = $user->email;
        if($user->firstName !== null) $result['firstName'] = $user->firstName;
        if($user->lastName !== null) $result['lastName'] = $user->lastName;

        return $result;
    }

    /**
     * @param array $row
     * @return User
     */
    private function arrayToObject(array $row): User
    {
        $user = new User();
        $user->id = $row['id'];
        if(array_key_exists('username', $row)) $user->username = $row['username'];
        if(array_key_exists('password', $row)) $user->password = $row['password'];
        if(array_key_exists('email', $row)) $user->email = $row['email'];
        if(array_key_exists('firstName', $row)) $user->firstName = $row['firstName'];
        if(array_key_exists('lastName', $row)) $user->lastName = $row['lastName'];

        return $user;
    }

    /**
     * @param int $id
     * @return User
     */
    public function getById(int $id): User
    {
        return $this->arrayToObject($this->repoImpl->findById(self::TABLE_NAME, $id));
    }

    /**
     * @param int $id
     * @return User
     */
    public function lazyGetById(int $id): User
    {
        $lazyFindResult = $this->repoImpl->tryFindById(self::TABLE_NAME, $id);
        if($lazyFindResult !== null) {
            return $this->arrayToObject($lazyFindResult);
        } else {
            return $this->arrayToObject(['id' => $id ]);
        }
    }

    /**
     * @param User $user
     */
    public function persist(User $user): void
    {
        $id = $this->repoImpl->insert(self::TABLE_NAME, $this->objectToArray($user));
        $user->id = $id;
    }

    public function initTable(): void
    {
        $this->repoImpl->createTable(self::TABLE_NAME, [
            'username' => 'TEXT',
            'password' => 'TEXT',
            'email' => 'TEXT',
            'firstName' => 'TEXT',
            'lastName' => 'TEXT',
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

    public function find(array $constraints): array
    {
        return $this->repoImpl->select(self::TABLE_NAME, $constraints);
    }

    public function findOne(array $constraints): User
    {
        $result = $this->find($constraints);
        if(count($result) > 1) {
            throw new \Exception('Non-unique query result');
        }
        if(count($result) == 0) return null;
        return $result[0];
    }
}