<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleModel; // Adjust the namespace based on your model location
use DB;
class CheckViewAccessMenu
{
    public function handle($request, Closure $next)
    {
        $userId = Auth::id();
        $modules = $this->getModulesWithPermissions($userId);

        view()->share('modules', $modules);

        return $next($request);
    }

    // Implement your logic to retrieve permissions from the database
    private function getModulesWithPermissions($userId)
    {
        if(DB::connection()->getDatabaseName()){
            // Replace this with your actual logic to fetch modules with permissions from the database
            $modules = RoleModel::where('userid', $userId)->get();

            return $modules->map(function ($module) {
                $module->hasViewPermission = $this->hasViewPermission($module->module);
                $module->hasEditPermission = $this->hasEditPermission($module->module);
                return $module;
            });
        }
    }

    private function hasViewPermission($moduleName)
    {
    $userId = Auth::id();

    // Replace this with your actual logic to check if the user has view permission for the given module
    $role = RoleModel::where('userid', $userId)
        ->where('module', $moduleName)
        ->first();

    if ($role) {
        $permissions = explode(',', $role->permission);

        // Check if 1 (view permission) is in the permissions array as an integer
        return in_array(1, $permissions);
    }
        return false; // User doesn't have a role for this module
    }   

    private function hasEditPermission($moduleName)
    {
    $userId = Auth::id();

    // Replace this with your actual logic to check if the user has view permission for the given module
    $role = RoleModel::where('userid', $userId)
        ->where('module', $moduleName)
        ->first();

    if ($role) {
        $permissions = explode(',', $role->permission);

        // Check if 3 (edit permission) is in the permissions array as an integer
        return in_array(3, $permissions);
    }
        return false; // User doesn't have a role for this module
    }


}
