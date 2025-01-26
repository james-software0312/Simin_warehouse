<?php

// app/Services/ShelfService.php

namespace App\Services;

use App\Models\ShelfModel;

class ShelfService
{
    /**
     * Get all shelf data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ShelfModel::leftJoin('warehouse', 'shelf.warehouseid', '=', 'warehouse.id')
            ->select('shelf.*', 'warehouse.name as warehouse')
            ->get();
    }

    /**
     * Create a new shelf data.
     *
     * @param array $data The data for creating a new shelf.
     * @return \App\Models\ShelfModel
     */
    public function create($data)
    {
        return ShelfModel::create($data);
    }

    /**
     * Get the total count of shelf items.
     *
     * @return int
     */
    public function totalitem()
    {
        return ShelfModel::count();
    }

    /**
     * Update shelf data by ID.
     *
     * @param int $id The ID of the shelf data to update.
     * @param array $data The data for updating the shelf.
     * @return \App\Models\ShelfModel
     */
    public function update($id, $data)
    {
        $SetData = ShelfModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete shelf data by ID.
     *
     * @param int $id The ID of the shelf data to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = ShelfModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get shelf data by ID.
     *
     * @param int $id The ID of the shelf data to retrieve.
     * @return \App\Models\ShelfModel
     */
    public function getById($id)
    {
        return ShelfModel::findOrFail($id);
    }

    /**
     * Check if a shelf code exists, excluding a specific ID.
     *
     * @param string $code The shelf code to check.
     * @param int $Id The ID to exclude from the check.
     * @return bool
     */
    public function CheckCodeId($code, $Id)
    {
        return  ShelfModel::where('code', $code)->where('id', '!=', $Id)->exists();
    }

    /**
     * Check if a shelf code exists.
     *
     * @param string $code The shelf code to check.
     * @return bool
     */
    public function CheckCode($code)
    {
        return ShelfModel::where('code', $code)->exists();
    }


    /**
     * Get shelf items by warehouse.
     *
     * @param string $warehouse The reference to search for.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function GetByWarehouse($warehouse){
        return ShelfModel::where('warehouseid', $warehouse)->get();
    }
}
