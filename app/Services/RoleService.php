<?php

// app/Services/RoleService.php

namespace App\Services;

use App\Models\RoleModel;

class RoleService
{
    /**
     * Get all roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return RoleModel::all();
    }

    /**
     * Generate default roles for a user.
     *
     * @param int $userid The ID of the user to generate roles for.
     * @return bool
     */
    public function generateRole($userid)
    {
        // Array of default roles for a user
        $data = [
            ['userid' => $userid, 'module' => 'stock', 'permission' => '1,2,3,4'],
            ['userid' => $userid, 'module' => 'purchase', 'permission' => ''],
            ['userid' => $userid, 'module' => 'transaction', 'permission' => '1,2,3,4'],
            ['userid' => $userid, 'module' => 'warehouse', 'permission' => '1,2,3,4'],
            ['userid' => $userid, 'module' => 'category', 'permission' => '1,2,3'],
            ['userid' => $userid, 'module' => 'shelf', 'permission' => '1,2,3'],
            ['userid' => $userid, 'module' => 'unit', 'permission' => '1,2,3'],
            ['userid' => $userid, 'module' => 'size', 'permission' => '1,2,3'],
            ['userid' => $userid, 'module' => 'customer', 'permission' => '1,2,3,4'],
            ['userid' => $userid, 'module' => 'supplier', 'permission' => '1,2,3,4'],
            ['userid' => $userid, 'module' => 'user', 'permission' => ''],
            ['userid' => $userid, 'module' => 'activity', 'permission' => ''],
            ['userid' => $userid, 'module' => 'settings', 'permission' => ''],
            ['userid' => $userid, 'module' => 'reports', 'permission' => '1'],
            ['userid' => $userid, 'module' => 'vat', 'permission' => '1'],
            // Add other arrays for additional records
        ];

        return RoleModel::insert($data);
    }

    /**
     * Create a new role.
     *
     * @param array $data The data for creating a new role.
     * @return \App\Models\RoleModel
     */
    public function create($data)
    {
        return RoleModel::create($data);
    }

    /**
     * Update permissions for various modules.
     *
     * @param array $warehouse Permissions for the warehouse module.
     * @param array $unit Permissions for the unit module.
     * @param array $stock Permissions for the stock module.
     * @param array $transaction Permissions for the transaction module.
     * @param array $category Permissions for the category module.
     * @param array $shelf Permissions for the shelf module.
     * @param array $customer Permissions for the customer module.
     * @param array $supplier Permissions for the supplier module.
     * @param array $activity Permissions for the activity module.
     * @param array $settings Permissions for the settings module.
     * @param array $reports Permissions for the reports module.
     * @param array $user Permissions for the user module.
     * @param int $userid The ID of the user for whom permissions are updated.
     * @return void
     */
    public function update($warehouse, $unit, $stock, $purchase, $transaction, $category,
                            $shelf, $customer, $supplier, $activity, $settings, $reports, $user, $userid, $size, $vat)
    {
        $modules = compact(
            'warehouse', 'unit', 'stock', 'purchase', 'transaction', 'category', 'shelf', 'customer', 'supplier', 'activity', 'settings', 'reports', 'user', 'size', 'vat'
        );
        // dd($modules);
        foreach ($modules as $module => $data) {
            // Assuming you have a method like 'updateData' in your model
            RoleModel::where('userid', $userid)->where('module', $module)->update(['permission' => $data]);

            // You might want to add some validation and error handling here
        }
    }

    /**
     * Delete a role.
     *
     * @param int $id The ID of the role to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = RoleModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Delete roles associated with a user.
     *
     * @param int $id The ID of the user for whom roles are deleted.
     * @return void
     */
    public function deleteByUserid($id)
    {
        $SetData = RoleModel::where('userid', $id)->get();

        $SetData->each->delete();
    }

    /**
     * Get a role by its ID.
     *
     * @param int $id The ID of the role to retrieve.
     * @return \App\Models\RoleModel
     */
    public function getById($id)
    {
        return RoleModel::findOrFail($id);
    }

    /**
     * Get roles associated with a user.
     *
     * @param int $userid The ID of the user.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByUser($userid)
    {
        return RoleModel::where('userid', $userid)->get();
    }
}
