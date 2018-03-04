<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 2/20/2018
 * Time: 4:51 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;


interface UserServiceInterface
{
    /**
     * @param $user
     * @param $passwordEncoder
     * @return
     */
    public function register($user,$passwordEncoder);

    public function viewAll();

    public function getUserId();

    public function lastFiveUsers();

    public function userData();

    public function deleteUser($userId);
}