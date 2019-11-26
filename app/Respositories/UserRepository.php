<?php

namespace App\Respositories;


use App\Exceptions\UserNotFoundException;
use App\Model\User;


class UserRepository extends BaseRepository
{

    // Constructor to bind model to repo
    public function __construct()
    {
        parent::__construct(new User());
    }

    /**
     * @param $id
     * @param array $requiredFields
     * @return mixed
     * @throws UserNotFoundException
     */
    public function getUser($id, $requiredFields = [])
    {
        $user = empty($requiredFields) ? $this->model->where('id', $id)->first() : $this->model->where('id', $id)->first($requiredFields);
        if ($user == null) {
            throw new UserNotFoundException();
        } else {
            return $user;
        }
    }

    /**
     * This function returns user profile image data
     *
     * @param $id : User ID
     * @return mixed
     */
    public function getUserProfileImageInformation($id)
    {
        return $this->model->where('id', $id)->first(['profile_image_filename', 'profile_image_data']);
    }
}