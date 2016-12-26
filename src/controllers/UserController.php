<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 10/12/2016
 * Time: 16:38
 */

namespace MiW16\Results\Controllers;

use MiW16\Results\Models\User;
use MiW16\Results\Models\Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class UserController
{
    const TOKEN_REQUEST_PARAMETER = 'token';
    const USER_ID_REQUEST_PARAMETER = 'id';
    const USERNAME_REQUEST_PARAMETER = 'username';
    const EMAIL_REQUEST_PARAMETER = 'email';
    const PASSWORD_REQUEST_PARAMETER = 'password';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->entityManager = getEntityManager();
        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
    }

    /**
     * @param array $requestParameters
     * @return User
     */
    public function getByToken($requestParameters)
    {
        $userToken = $requestParameters[UserController::TOKEN_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::TOKEN_ATTRIBUTE => $userToken));

        if (is_null($user))
            return new User();

        return $user;
    }

    /**
     * @param array $requestParameters
     * @return User
     */
    public function getByID($requestParameters)
    {
        $userID = $requestParameters[UserController::USER_ID_REQUEST_PARAMETER];
        return $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));
    }

    /**
     * @param array $requestParameters
     * @return bool
     */
    public function userNameExists($requestParameters)
    {
        $userName = $requestParameters[UserController::USERNAME_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::USERNAME_ATTRIBUTE => $userName));

        if (!is_null($user))
            return true;

        return false;
    }

    /**
     * @param array $requestParameters
     * @return bool
     */
    public function userEmailExists($requestParameters)
    {
        $userEmail = $requestParameters[UserController::EMAIL_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::EMAIL_ATTRIBUTE => $userEmail));

        if (!is_null($user))
            return true;

        return false;
    }

    /**
     * @param array $requestParameters
     * @return User
     */
    public function create($requestParameters)
    {
        $user = new User();

        $user->setUsername($requestParameters['username']);
        $user->setEmail($requestParameters['email']);
        $user->setPassword($requestParameters['password']);
        $user->setToken($requestParameters['token']);
        $user->setEnabled($requestParameters['enabled']);
        $user->setLastLogin(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @return User[]
     */
    public function getAll()
    {
        return $this->entityRepository->findAll();
    }

    /**
     * @param array $requestParameters
     * @return User
     */
    public function delete($requestParameters)
    {
        $userID = $requestParameters[UserController::USER_ID_REQUEST_PARAMETER];
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
        $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));

        foreach ($results as $result)
            $this->entityManager->remove($result);

        $userID = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $user->setId($userID);

        return $user;
    }

    /**
     * @param array $requestParameters
     * @return User
     */
    public function update($requestParameters)
    {
        $userID = $requestParameters['id'];

        /**
         * @var User $user
         */
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $user->setUsername($requestParameters['username']);
        $user->setEmail($requestParameters['email']);
        $user->setPassword($requestParameters['password']);
        $user->setToken($requestParameters['token']);
        $user->setEnabled($requestParameters['enabled']);
        $user->setLastLogin(new \DateTime());

        $this->entityManager->merge($user);
        $this->entityManager->flush();

        return $user;
    }
}