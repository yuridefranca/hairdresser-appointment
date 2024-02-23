<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Log;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a model based on id from the database.
     * 
     * @param string|int $id
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($id): ?Model
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        $response = $this->model->find($id);
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' finished', [ 'response' => json_encode($response) ]);
        return $response;
    }

    /**
     * Get all models from database.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' called');
        $response = $this->model->all();
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' finished', [ 'response' => json_encode($response) ]);
        return $response;
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
        $response = $this->model->create($attributes);
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' finished', [ 'response' => json_encode($response) ]);
        return $response;
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
        $model = $this->model->findOrFail($id);
        $response = $model->update($attributes);
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' finished', [ 'response' => json_encode($response) ]);
        return $response;
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
        $response = $this->model->destroy($id);
        Log::debug(__CLASS__ . ' ' . __FUNCTION__ . ' finished', [ 'response' => json_encode($response) ]);
        return $response;
    }
}