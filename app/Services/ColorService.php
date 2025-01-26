<?php

// app/Services/sizeService.php

namespace App\Services;

use App\Models\ColorModel;

class ColorService
{
    /**
     * Get all sizes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ColorModel::all();
    }

    /**
     * Get the total count of sizes.
     *
     * @return int
     */
    public function totalitem()
    {
        return ColorModel::count();
    }

    /**
     * Create a new size.
     *
     * @param array $data The size data to create.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return ColorModel::create($data);
    }

    /**
     * Update a size by ID.
     *
     * @param int $id The ID of the color to update.
     * @param array $data The color data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update($id, $data)
    {
        $SetData = ColorModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete a color by ID.
     *
     * @param int $id The ID of the color to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = ColorModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get a color by ID.
     *
     * @param int $id The ID of the color to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        return ColorModel::findOrFail($id);
    }
}
