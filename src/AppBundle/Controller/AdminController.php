<?php

namespace AppBundle\Controller;

use AppBundle\Service\CommentServiceInterface;
use AppBundle\Service\ImageServiceInterface;
use AppBundle\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var ImageServiceInterface
     */
    private $imageService;

    /**
     * @var CommentServiceInterface
     */
    private $commentService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param ImageServiceInterface $imageService
     */
    public function __construct(UserServiceInterface $userService, ImageServiceInterface $imageService, CommentServiceInterface $commentService)
    {
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->commentService = $commentService;
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboard()
    {
        $lastFiveUsers = $this->userService->lastFiveUsers();
        $lastFiveImages = $this->imageService->lastFiveImages();

        return $this->render('admin/admin_dashboard.html.twig',['lastFiveUsers' => $lastFiveUsers, 'lastFiveImages' => $lastFiveImages]);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/images", name="admin_all_images")
     */
    public function allImages(Request $request){

        $allImages = $this->imageService->allImages();

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($allImages,$request->query->getInt('page',1),$request->query->getInt('limit',10));

        return $this->render(':Admin:admin_all_pictures.html.twig',['allImages' => $result]);

    }

    /**
     * @Route("/usersTable", name="admin_users_table")
     */
    public function usersTable(){

        $userData = $this->userService->userData();

        return $this->render('admin/admin_users_table.html.twig',['userData' => $userData]);

    }

    /**
     * @Route("/user/images/{id}", name="admin_user_images")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userImages($id){

        $data = $this->imageService->userImages($id);

        return $this->render('admin/admin_user_images.html.twig',['userImages' => $data]);
    }

    /**
     * @Route("delete/user/{id}", name="admin_delete_user")
     * @param $id int
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser($id){

        $this->userService->deleteUser($id);

        return $this->redirectToRoute('admin_users_table');

    }

    /**
     * @Route("/delete/user/{id}/{userId}", name="admin_delete_picture_in_user_images")
     * @param $id int
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminDeleteImageInUserPictures($id,$userId){

        $this->commentService->deleteComments($id);

        $this->imageService->deleteImage($id);

        return $this->redirect($this->generateUrl('admin_user_images', array('id' => $userId)));
    }

    /**
     * @Route("/delete/{id}", name="admin_delete_picture_in_dashboard")
     * @param $id int
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminDeleteImageInDashboard($id){

        $this->commentService->deleteComments($id);

        $this->imageService->deleteImage($id);

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/delete/all/{id}", name="admin_delete_picture")
     * @param $id int
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminDeleteImageInAllPictures($id){

        $this->commentService->deleteComments($id);

        $this->imageService->deleteImage($id);

        return $this->redirectToRoute('admin_all_images');
    }
}
