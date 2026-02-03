<?php

namespace App\Contracts\Repositories;

interface OrderRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getByStatus(string $status);
    public function getByCustomer(int $customerId);
}
