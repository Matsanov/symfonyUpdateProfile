<?php
/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 3/2/2018
 * Time: 1:19 PM
 */

namespace AppBundle\Service;


interface CommentServiceInterface
{

    public function uploadComment($comment,$userId, $image_id, $commentText);

    public function commentsAndUsers($imageId);

    public function userCommentsCount($userId);

    public function deleteComment($id);

    public function deleteComments($imageId);
}