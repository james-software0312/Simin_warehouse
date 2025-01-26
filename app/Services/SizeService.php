<?php

// app/Services/sizeService.php

namespace App\Services;

use App\Models\SizeModel;

class SizeService
{
    /**
     * Get all sizes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return SizeModel::all();
    }

    /**
     * Get the total count of sizes.
     *
     * @return int
     */
    public function totalitem()
    {
        return SizeModel::count();
    }

    /**
     * Create a new size.
     *
     * @param array $data The size data to create.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return SizeModel::create($data);
    }

    /**
     * Update a size by ID.
     *
     * @param int $id The ID of the size to update.
     * @param array $data The size data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update($id, $data)
    {
        $SetData = SizeModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete a size by ID.
     *
     * @param int $id The ID of the size to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = SizeModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get a size by ID.
     *
     * @param int $id The ID of the size to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        return SizeModel::findOrFail($id);
    }
}
