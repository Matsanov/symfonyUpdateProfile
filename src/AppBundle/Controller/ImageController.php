<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Image;
use AppBundle\Form\CommentType;
use AppBundle\Form\ImageType;
use AppBundle\Service\CommentServiceInterface;
use AppBundle\Service\ImageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends Controller
{
    /**
     * @var ImageServiceInterface
     */
    private $imageService;

    /**
     * @var CommentServiceInterface
     */
    private $commentService;

    /**
     * ImageController constructor.
     * @param ImageServiceInterface $imageService
     */
    public function __construct(ImageServiceInterface $imageService, CommentServiceInterface $commentService)
    {
        $this->imageService = $imageService;
        $this->commentService = $commentService;
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/upload" , name="image_upload")
     */
    public function uploadAction(Request $request)
    {

        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        $id = $this->getUser()->getId();

        if ($form->isSubmitted() && $form->isValid()) {

                $parameter = $this->getParameter('images_directory');

                $this->imageService->upload($image, $parameter, $id);

                return $this->redirectToRoute('image_upload');
        }

        return $this->render('upload_image.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/images", name="all_images")
     */
    public function allImages(Request $request){

        $allImages = $this->imageService->allImages();

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($allImages,$request->query->getInt('page',1),$request->query->getInt('limit',10));

        return $this->render('images.html.twig',['allImages' => $result]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/userImages", name="users_images")
     */
    public function userImages(){

        $id = $this->getUser()->getId();

        $userImages = $this->imageService->userImages($id);

        return $this->render('userImages.html.twig',['allImages' => $userImages]);
    }

    /**
     * @Route("/home", name="homepage")
     */
    public function lastTenPictures(){

        $lastTenImages = $this->imageService->lastTenPictures();

        return $this->render('home.html.twig',['lastTenImages' => $lastTenImages]);

    }

    /**
     * @Route("/delete/{id}", name="user_delete_picture")
     * @param $id int
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function userDeletePicture($id){

        $this->commentService->deleteComments($id);

        $this->imageService->deleteImage($id);

        return $this->redirectToRoute('users_images');
    }

    /**
     * @Route("/comment/{id}", name="comment_upload")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadComment($id,Request $request){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        $user_id = $this->getUser()->getId();

        if ($form->isSubmitted() && $form->isValid()) {

            $commentText = $comment->getCommentText();

            $this->commentService->uploadComment($comment,$user_id,$id, $commentText);

            //return $this->redirect($request->getUri());
            return $this->redirect($this->generateUrl('comments', array('id' => $id)));
        }

        return $this->render('upload_comment.html.twig', ['form' => $form->createView(), 'imageId' => $id]);

    }

    //Getting all comments for pictures

    /**
     * @Route("/comments/{id}", name="comments")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function commentsForImage($id)
    {
            //Getting Users and Comments from DB for the picture
            $commentsAndUsers = $this->commentService->commentsAndUsers($id);

            $imageName = $this->imageService->imageName($id);

            $data = [
                'comments' => $commentsAndUsers,
                'image_id' => $id,
                'image_name' => $imageName
            ];

            return $this->render('comments.html.twig', $data);
    }

    /**
     * @Route("delete/comment/{image_id}/{id}", name="delete_comment")
     * @param $id int
     * @param $image_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteComment($id,$image_id){

        $this->commentService->deleteComment($id);

        return $this->redirect($this->generateUrl('comments', array('id' => $image_id)));
    }

}
