<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleModel; // Adjust the namespace based on your model location

class CheckViewAccess
{
    public function handle($request, Closure $next)
    {
        $userId = Auth::id();
        $moduleName = $request->segment(1); // Assuming the module is the first segment in the URL
        $requiredPermissions = $this->getRequiredPermissions($userId, $moduleName);
        // dd($requiredPermissions);
        // Check if the user has the required permissions
        $hasViewPermission = in_array(1, $requiredPermissions);
        $hasEditPermission = in_array(3, $requiredPermissions);
        // dd($requiredPermissions);
        if (!$hasViewPermission && $moduleName !=='setting') {
            // Redirect or return an error response for unauthorized access
            //return abort(403, 'Unauthorized access.'); // You can customize this based on your requirements
            redirect('login');
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


}
