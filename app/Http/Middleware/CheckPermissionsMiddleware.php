<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleModel; // Adjust the namespace based on your model location
use DB;
class CheckPermissionsMiddleware
{
    public function handle($request, Closure $next)
    {
        $userId = Auth::id();
        $moduleName = $request->segment(1); // Assuming the module is the first segment in the URL
        if(DB::connection()->getDatabaseName()){
            $requiredHasSeePermissions = $this->getRequiredPermissions($userId, 'user');
            $requiredPermissions = $this->getRequiredPermissions($userId, $moduleName);
            // Check if the user has the required permissions
            $hasViewPermission = in_array(1, $requiredPermissions);
            $hasCreatePermission = in_array(2, $requiredPermissions);
            $hasEditPermission = in_array(3, $requiredPermissions);
            $hasDeletePermission = in_array(4, $requiredPermissions);
            $hasAssignPermission = in_array(5, $requiredPermissions);
            $hasSeeHiddenPermission = in_array(6, $requiredHasSeePermissions);
            $hasEditStockQtyPermission = in_array(7, $requiredPermissions);
            // Pass the permission variables to the view
            view()->share([
                'hasCreatePermission' => $hasCreatePermission,
                'hasEditPermission' => $hasEditPermission,
                'hasDeletePermission' => $hasDeletePermission,
                'hasViewPermission' => $hasViewPermission,
                'hasAssignPermission' => $hasAssignPermission,
                'hasSeeHiddenPermission' => $hasSeeHiddenPermission,
                'hasEditStockQtyPermission' => $hasEditStockQtyPermission,
            ]);
            // Pass the permission variables to the request
            $request->merge([

                'hasViewPermission' => in_array(1, $requiredPermissions),
                'hasCreatePermission' => in_array(2, $requiredPermissions),
                'hasEditPermission' => in_array(3, $requiredPermissions),
                'hasDeletePermission' => in_array(4, $requiredPermissions),
                'hasAssignPermission' => in_array(5, $requiredPermissions),
                'hasSeeHiddenPermission' => $hasSeeHiddenPermission,
                'hasEditStockQtyPermission' => in_array(7, $requiredPermissions),
            ]);
        }
        return $next($request);
    }

    // Implement your logic to retrieve permissions from the database
    private function getRequiredPermissions($userId, $moduleName)
    {
        // Replace this with your actual logic to fetch permissions from the database
        // Example: return [1, 2, 3, 4] based on $userId and $moduleName
        $permissions = RoleModel::where('userid', $userId)
            ->where('module', $moduleName)
            ->first();

        return $permissions ? explode(',', $permissions->permission) : [];
    }

    public function getCreatePermission()
    {
        return $this->hasCreatePermission; // Replace with your actual variable
    }

    public function getEditPermission()
    {
        return $this->hasEditPermission; // Replace with your actual variable
    }

    public function getDeletePermission()
    {
        return $this->hasDeletePermission; // Replace with your actual variable
    }
}
