<?php

namespace App\Respositories;


use App\Exceptions\UserNotFoundException;
use App\Model\User;

class UserRepository
{

    public function __construct()
    {
    }

    /**
     * @param $id
     * @param array $requiredFields
     * @return mixed
     * @throws UserNotFoundException
     */
    public function getUser($id, $requiredFields = [])
    {
        $user = empty($requiredFields) ? User::where('id', $id)->first() : User::where('id', $id)->first($requiredFields);
        if ($user == null) {
            throw new UserNotFoundException();
        } else {
            return $user;
        }
    }
}