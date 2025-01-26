<?php

// app/Services/WarehouseService.php

namespace App\Services;

use App\Models\WarehouseModel;

class WarehouseService
{
    /**
     * Get all warehouses.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return WarehouseModel::all();
    }

    /**
     * Get the total count of warehouses.
     *
     * @return int
     */
    public function totalitem()
    {
        return WarehouseModel::count();
    }

    /**
     * Create a new warehouse with the given data.
     *
     * @param array $data The warehouse data to create.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return WarehouseModel::create($data);
    }

    /**
     * Update the warehouse with the given ID.
     *
     * @param int $id The ID of the warehouse to update.
     * @param array $data The warehouse data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update($id, $data)
    {
        $SetData = WarehouseModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete the warehouse with the given ID.
     *
     * @param int $id The ID of the warehouse to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = WarehouseModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get the warehouse by ID.
     *
     * @param int $id The ID of the warehouse to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        return WarehouseModel::findOrFail($id);
    }
    /**
     * Get the warehouse by ID.
     *
     * @param int $id The ID of the warehouse to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrimary()
    {
        return WarehouseModel::where('is_primary', 1)->first();
    }
}
