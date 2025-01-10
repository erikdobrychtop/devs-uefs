<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }
    public function getUserById($id)
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function updateUser($id, array $data)
    {
        $user = $this->userRepository->find($id);
        return $this->userRepository->update($user, $data);
    }

    public function deleteUser($id)
    {
        $user = $this->userRepository->find($id);
        return $this->userRepository->delete($user);
    }
}
