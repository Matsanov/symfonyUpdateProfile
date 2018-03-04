<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 2/20/2018
 * Time: 5:56 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use AppBundle\Repository\ImageRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ImageService implements ImageServiceInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var User
     */
    private $user;

    /**
     * UserService constructor.
     * @param EntityManager $entityManager
     * @param ImageRepository $imageRepository
     * @param UserServiceInterface $userService
     * @param UserRepository $userRepository
     * @param User $user
     */
    public function __construct(EntityManager $entityManager, ImageRepository $imageRepository, UserServiceInterface $userService ,UserRepository $userRepository ,User $user)
    {
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->user = $user;
    }

    /**
     * @param $image Image
     * @param $parameter
     * @param $id
     * @return mixed|void
     */
    public function upload($image,$parameter,$id){

        $imagesCount = $this->imageRepository->imagesCount($id);

        if ($imagesCount[0]["count(user_id)"] <= 9) {
            // $file stores the uploaded PDF file
            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile $file
             */
            $file = $image->getImage();

            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $parameter,
                $fileName
            );

            /**
             * @var $userId User
             */
            $userId = $this->userRepository->findOneBy(['id' => $id]);


            $image->setDateAdded(new \DateTime('now'));
            $image->setImage($fileName);
            $image->setName($fileName);
            $image->setUser($userId);

            $this->entityManager->persist($image);
            $this->entityManager->flush();

        }else{

            throw new Exception('Maximum number of images reached !');
        }
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    public function allImages(){

        return $this->imageRepository->allImages()->fetchAll();

    }

    public function userImages($id){

        return $this->imageRepository->userImages($id);
    }

    public function lastTenPictures(){
        $asd = 5;
        echo json_encode(['errors'=> $asd]);
        return $this->imageRepository->getLimitImages();
    }

    public function imagesCount($userId){
        return $this->imageRepository->imagesCount($userId);
    }

    public function lastFiveImages(){

        return $this->imageRepository->lastFiveImages();
    }

    public function imageName($imageId){

        return $this->imageRepository->imageName($imageId)->fetch();
    }

    public function deleteImage($imageId){

        return $this->imageRepository->deleteImage($imageId);
    }

}