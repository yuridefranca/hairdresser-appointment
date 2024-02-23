<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Interfaces\BaseServiceInterface;
use App\Repositories\Interfaces\BaseRepositoryInterface;

class BaseService implements BaseServiceInterface
{
    /**
     * @var \App\Repositories\Interface\BaseRepositoryInterface 
     */
    protected $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get a model based on id from the database.
     * 
     * @param string|int $id
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getById($id): ?Model
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        return $this->repository->find($id);
    }

    /**
     * Get all models from database.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        return $this->repository->all();
    }

    /**
     * Create a new model in database.
     * 
     * @param array $attributes
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create($attributes): Model
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        return $this->repository->create($attributes);
    }

    /**
     * Update a model in database.
     * 
     * @param string|int $id
     * @param array $attributes
     * 
     * @return bool
     */
    public function update($id, $attributes): bool
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        $model = $this->repository->findOrFail($id);
        return $model->update($attributes);
    }

    /**
     * Delete a model from database.
     * 
     * @param string|int $id
     * 
     * @return bool
     */
    public function destroy($id): bool
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        return $this->repository->destroy($id);
    }
}