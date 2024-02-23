<?php 

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseServiceInterface 
{
    /**
     * Get a model based on id from the database.
     * 
     * @param string|int $id
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getById($id): ?Model;

    /**
     * Get all models from database.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection;

    /**
     * Create a new model in database.
     * 
     * @param array $attributes
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes): Model;

    /**
     * Update a model in database.
     * 
     * @param string|int $id
     * @param array $attributes
     * 
     * @return bool
     */
    public function update(string|int $id, array $attributes): bool;

    /**
     * Delete a model from database.
     * 
     * @param string|int $id
     * 
     * @return bool
     */
    public function destroy(string|int $id): bool;
}