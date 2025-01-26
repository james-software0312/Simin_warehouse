<?php

// app/Services/vatService.php

namespace App\Services;

use App\Models\VatModel;

class VatService
{
    /**
     * Get all vats.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return VatModel::orderBy('name', 'asc')->get();
    }

    /**
     * Get the total count of vats.
     *
     * @return int
     */
    public function totalitem()
    {
        return VatModel::count();
    }

    /**
     * Create a new vat.
     *
     * @param array $data The vat data to create.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return VatModel::create($data);
    }

    /**
     * Update a vat by ID.
     *
     * @param int $id The ID of the vat to update.
     * @param array $data The vat data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update($id, $data)
    {
        $SetData = VatModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete a vat by ID.
     *
     * @param int $id The ID of the vat to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = VatModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get a vat by ID.
     *
     * @param int $id The ID of the vat to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        return VatModel::findOrFail($id);
    }
}
