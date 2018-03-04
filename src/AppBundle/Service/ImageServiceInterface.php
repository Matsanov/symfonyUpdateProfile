<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 2/20/2018
 * Time: 5:56 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Image;

interface ImageServiceInterface
{
    /**
     * @param $image Image
     * @param $parameter
     * @param $id
     * @return mixed
     */
    public function upload($image, $parameter, $id);

    public function allImages();

    public function userImages($id);

    public function lastTenPictures();

    public function imagesCount($userId);

    public function lastFiveImages();

    public function imageName($imageId);

    public function deleteImage($imageId);

}