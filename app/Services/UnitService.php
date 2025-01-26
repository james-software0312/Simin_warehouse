<?php

// app/Services/UnitService.php

namespace App\Services;

use App\Models\UnitModel;

class UnitService
{
    /**
     * Get all units.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return UnitModel::all();
    }

    /**
     * Get the total count of units.
     *
     * @return int
     */
    public function totalitem()
    {
        return UnitModel::count();
    }

    /**
     * Create a new unit.
     *
     * @param array $data The unit data to create.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return UnitModel::create($data);
    }

    /**
     * Update a unit by ID.
     *
     * @param int $id The ID of the unit to update.
     * @param array $data The unit data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update($id, $data)
    {
        $SetData = UnitModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete a unit by ID.
     *
     * @param int $id The ID of the unit to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = UnitModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get a unit by ID.
     *
     * @param int $id The ID of the unit to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        return UnitModel::findOrFail($id);
    }

    /**
     * Check if a unit with the given code and ID exists.
     *
     * @param string $code The code to check.
     * @param int $Id The ID to exclude from the check.
     * @return bool
     */
    public function CheckCodeId($code, $Id)
    {
        return  UnitModel::where('code', $code)->where('id', '!=', $Id)->exists();
    }

    /**
     * Check if a unit with the given code exists.
     *
     * @param string $code The code to check.
     * @return bool
     */
    public function CheckCode($code)
    {
        return UnitModel::where('code', $code)->exists();
    }
}
