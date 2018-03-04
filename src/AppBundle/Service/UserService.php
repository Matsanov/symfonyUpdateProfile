<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 2/20/2018
 * Time: 4:47 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Repository\RoleRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserService implements UserServiceInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var User
     */
    private $user;

    /**
     * UserService constructor.
     * @param EntityManager $entityManager
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param User $user
     */
    public function __construct(EntityManager $entityManager, UserRepository $userRepository, RoleRepository $roleRepository,User $user)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->user = $user;
    }

    /**
     * @param $user User
     * @param $passwordEncoder
     */
    public function register($user,$passwordEncoder){

        $userRole = $this->roleRepository->findOneBy(['name' => 'ROLE_USER']);

        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

        $user->setDateAdded(new \DateTime('now'));
        $user->setPassword($password);
        $user->setRoles($userRole);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function viewAll(){

        return $this->userRepository->getUsers()->fetchAll();
    }

    public function getUserId(){

        return $this->user->getId();
    }

    public function userData(){

        return $this->userRepository->userData()->fetchAll();
    }

    public function lastFiveUsers(){

        return $this->userRepository->getLastFiveUsers();
    }

    public function deleteUser($userId){

        return $this->userRepository->deleteUser($userId);
    }

}