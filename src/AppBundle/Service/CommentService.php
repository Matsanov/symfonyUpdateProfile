<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 3/2/2018
 * Time: 1:18 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use AppBundle\Repository\CommentRepository;
use AppBundle\Repository\ImageRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentService implements CommentServiceInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ImageServiceInterface
     */
    private $imageService;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * UserService constructor.
     * @param EntityManager $entityManager
     * @param ImageRepository $imageRepository
     * @param ImageServiceInterface $imageService
     * @param CommentRepository $commentRepository
     * @param UserServiceInterface $userService
     * @param UserRepository $userRepository
     * @param Comment $comment
     */
    public function __construct(EntityManager $entityManager,ImageRepository $imageRepository,ImageServiceInterface $imageService, CommentRepository $commentRepository, UserServiceInterface $userService ,UserRepository $userRepository ,Comment $comment)
    {
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->imageRepository = $imageRepository;
        $this->imageService = $imageService;
        $this->comment = $comment;
    }

    /**
     * @param Comment $comment
     * @param $userId
     * @param $image_id
     * @param $commentText
     * @internal param $id
     */
    public function uploadComment($comment,$userId, $image_id,$commentText){

        /**
         * @var $user_id User
         */
        $user_id = $this->userRepository->findOneBy(['id' => $userId]);

        /**
         * @var $imageId Image
         */
        $imageId = $this->imageRepository->findOneBy(['id' => $image_id]);

        $comment->setUser($user_id);
        $comment->setImage($imageId);
        $comment->setCommentText($commentText);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();


    }

    public function commentsAndUsers($imageId){

        return $this->commentRepository->commentAndUsers($imageId);
    }

    public function userCommentsCount($userId){

        return $this->commentRepository->commentAndUsers($userId);
    }

    public function deleteComment($commentId){
        $this->commentRepository->deleteComment($commentId);
    }

    public function deleteComments($imageId){
        $this->commentRepository->deleteComments($imageId);
    }

}