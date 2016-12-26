<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 10/12/2016
 * Time: 16:39
 */

namespace MiW16\Results\Controllers;

use MiW16\Results\Models\User;
use MiW16\Results\Models\Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ResultController
{
    const ID_REQUEST_PARAMETER = 'id';
    const USER_ID_REQUEST_PARAMETER = 'user_id';
    const RESULT_REQUEST_PARAMETER = 'result';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * ResultController constructor.
     */
    public function __construct()
    {
        $this->entityManager = getEntityManager();
        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
    }

    /**
     * @param array $requestParameters
     * @return Result
     */
    public function getByID($requestParameters)
    {
        $resultID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        return $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));
    }

    /**
     * @param array $requestParameters
     * @return Result[]
     */
    public function getByUserID($requestParameters)
    {
        $userID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
        return $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));
    }

    /**
     * @param array $requestParameters
     * @return Result
     */
    public function create($requestParameters)
    {
        $userID = $requestParameters['user_id'];
        $result = $requestParameters['result'];

        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));
        $result = new Result($result, $user, new \DateTime());

        $this->entityManager->persist($result);
        $this->entityManager->flush();

        return $result;
    }

    /**
     * @return Result[]
     */
    public function getAll()
    {
        return $this->entityRepository->findAll();
    }

    /**
     * @return Result[]
     */
    public function delete()
    {
        $results = $this->entityRepository->findAll();
        $resultIDs = array();

        /**
         * @var Result $result
         */
        foreach ($results as $result) {
            $resultIDs[] = $result->getId();
            $this->entityManager->remove($result);
        }

        $this->entityManager->flush();

        foreach ($results as $result)
            $result->setId(array_shift($resultIDs));

        return $results;
    }

    /**
     * @param array $requestParameters
     * @return Result
     */
    public function deleteByID($requestParameters)
    {
        $resultID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));
        $resultID = $result->getId();

        $this->entityManager->remove($result);
        $this->entityManager->flush();
        $result->setId($resultID);

        return $result;
    }

    /**
     * @param array $requestParameters
     * @return Result[]
     */
    public function deleteByUserID($requestParameters)
    {
        $userID = $requestParameters[ResultController::ID_REQUEST_PARAMETER];
        $this->entityRepository = $this->entityManager->getRepository(User::CLASS_NAME);
        $user = $this->entityRepository->findOneBy(array(User::ID_ATTRIBUTE => $userID));

        $this->entityRepository = $this->entityManager->getRepository(Result::CLASS_NAME);
        $results = $this->entityRepository->findBy(array(Result::USER_ATTRIBUTE => $user));
        $resultIDs = array();

        /**
         * @var Result $result
         */
        foreach ($results as $result) {
            $resultIDs[] = $result->getId();
            $this->entityManager->remove($result);
        }

        $this->entityManager->flush();

        foreach ($results as $result)
            $result->setId(array_shift($resultIDs));

        return $results;
    }

    /**
     * @param array $requestParameters
     * @return Result
     */
    public function update($requestParameters)
    {
        $resultID = $requestParameters['id'];

        /**
         * @var Result $result
         */
        $result = $this->entityRepository->findOneBy(array(Result::ID_ATTRIBUTE => $resultID));

        $result->setResult($requestParameters['result']);

        $this->entityManager->merge($result);
        $this->entityManager->flush();

        return $result;
    }
}