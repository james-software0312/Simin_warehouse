<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\RoleService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Password;
use Mail;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//use Illuminate\Auth\Notifications\ResetPassword;

class UserController extends Controller
{
    //
    //use ResetPassword;
    protected $userService;
    protected $settingsService;
    protected $roleService;
    protected $user;
    public function __construct(UserService $userService, SettingsService $settingsService, RoleService $roleService)
    {

      // Dependency injection for services
      $this->userService        = $userService;
      $this->settingsService    = $settingsService;
      $this->roleService        = $roleService;
      $this->user = auth()->user();

    }


    /**
     * Display the index view for users.
     *
     * @return \Illuminate\View\View
     */
    public function index(){

        return view('user.index');
    }


    /**
     * Display the forgot password view.
     *
     * @return \Illuminate\View\View
     */
    public function forgotpassword(){
        return view('login.index');
    }


    /**
     * Display the profile view.
     *
     * @return \Illuminate\View\View
     */
    public function profile(){
        return view('user.profile');
    }


    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function login(){
//         session(['test' => 'Session is working']);
// dd(session('test'));
        $data = $this->settingsService->getdataById(1);

        return view('user.login', ['data' => $data]);
    }

    /**
     * Display the reset password view.
     *
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null){
        return view('user.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }


    /**
     * Process user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dologin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($this->userService->authenticateUser($credentials)) {

            return redirect('/transaction/checkoutlist');

        } else {
            return redirect('/login')->with('statuserror', __('text.msg_failed_email_password'));
        }
    }


    /**
     * Delete a user and associated role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->only('deleteid');
        $deleteuser = $this->userService->deleteUser($id);
        $this->roleService->deleteByUserid($id);

        return redirect()->route('user.index')->with('success', __('text.msg_user_deleted'));
    }


    /**
     * Get user data by ID for JSON response.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->userService->getUserById($id);
        return response()->json($data);
    }


    /**
     * Update user profile or user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required',

        ]);

        $id         = $request->only(['editid']);
        $data       = $request->only(['email','name']);
        $source     = $request->input('source');
        $password   = $request->input('password');

        $hashedPassword = bcrypt($password);



        //source from profile
        if($source === '1'){

            if($password !=''){
                $dataUser = $this->userService->updateProfile($id, $data, $hashedPassword);
            }else{
                $dataUser = $this->userService->updateuser($id, $data);
            }
            return redirect()->route('profile')->with('success', __('text.msg_profile_updated'));
        }
        else{
            if($password !=''){
                $dataUser = $this->userService->updateProfile($id, $data, $hashedPassword);
            }else{
                $dataUser = $this->userService->updateuser($id, $data);
            }
            return redirect()->route('user.index')->with('success', __('text.msg_user_updated'));
        }
    }


    /**
     * Get user data for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request){
    // Retrieve permissions from the middleware
    $hasCreatePermission = $request->hasCreatePermission;
    $hasEditPermission   = $request->hasEditPermission;
    $hasDeletePermission = $request->hasDeletePermission;
    $hasAssignPermission = $request->hasAssignPermission;

    if ($request->ajax()) {
        $data = $this->userService->getAllUser(); // Assuming this method exists in UserService

        return DataTables::of($data)
            ->addColumn('action', function ($data) use ($hasCreatePermission, $hasEditPermission, $hasDeletePermission, $hasAssignPermission) {

                $actionHtml = '<div class="d-flex">';
                // Edit Button
                if ($hasEditPermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#EditModal" id="btnedit" data-editid="' . $data->id . '" class="btn btn-sm btn-success d-flex align-items-center" data-toggle="modal" data-target="#edit">
                        <span class="material-symbols-rounded">edit</span> '.__('text.edit').'
                    </a>&nbsp;';
                }

                // Delete Button
                if ($hasDeletePermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->id . '" class="btn btn-sm btn-danger d-flex align-items-center">
                        <span class="material-symbols-rounded">delete</span> '.__('text.delete').'
                    </a>&nbsp;';
                }

                // Assign Role Button
                if ($hasAssignPermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#AssignModal" id="btnassign" data-asignid="' . $data->id . '" class="btn btn-sm btn-warning d-flex align-items-center">
                        <span class="material-symbols-rounded">settings_account_box</span> '.__('text.assign_role').'
                    </a>';
                }

                $actionHtml .= '</div>';
                return $actionHtml;
            })
            ->toJson();

        }
    return abort(403, 'Unauthorized access.');
}

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {

        // If the code reaches this point, it means validation has passed.

        // Continue with your logic to create the user.
        $data   = $request->only(['name', 'email']);


        // Define your default password (e.g., 'default_password')
        $defaultPassword = '123456';
        if(!empty($request->input('password'))) $defaultPassword = $request->input('password');
        // Hash the default password
        $hashedPassword = bcrypt($defaultPassword);

        if (!$this->userService->getUserByEmail($request->input('email'))) {
            // User with the same email doesn't exist, proceed with user creation.
            $dataUser = $this->userService->createUser($hashedPassword, $data);

            $lastCreatedId = $dataUser->id;


            //create default user role also to table role
            $this->roleService->generateRole($lastCreatedId);
            return redirect()->route('user.index')->with('success', __('text.msg_user_created'));
        } else {
            return redirect()->route('user.index')->with('success', __('text.msg_user_already_registered'));
        }
    }


    /**
     * Reset user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {

        $CheckToken = $this->userService->checkToken($request->input('token'));
        if(!$CheckToken){
            return back()->withInput()->with('statuserror', __('text.msg_invalid_token'));
        }
            $reset = $this->userService->resetPassword(
                $CheckToken->email,
                $request->input('password')
            );
            if($reset){
                return redirect('/login')->with('status', __('text.msg_you_can_login_with_new_password'));
             }

    }

    /**
     * Logout the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // You can specify a different URL to redirect the user after logout.
    }

    /**
     * Assign roles to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function role(Request $request) {

        // If the code reaches this point, it means validation has passed.

        // Continue with your logic to create the user.
        $permissions    = $request->input('permissions');
        $userid         = $request->input('userid');
        $warehouse      = is_array($request->input('warehouse')) ? implode(',', $request->input('warehouse')): null;
        $unit           = is_array($request->input('unit')) ? implode(',', $request->input('unit')): null;
        $transaction    = is_array($request->input('transaction')) ? implode(',', $request->input('transaction')): null;
        $purchase       = is_array($request->input('purchase')) ? implode(',', $request->input('purchase')): null;
        $category       = is_array($request->input('category')) ? implode(',', $request->input('category')): null;
        $shelf          = is_array($request->input('shelf')) ? implode(',', $request->input('shelf')): null;
        $customer       = is_array($request->input('customer')) ? implode(',', $request->input('customer')): null;
        $supplier       = is_array($request->input('supplier')) ? implode(',', $request->input('supplier')): null;
        $stock          = is_array($request->input('stock')) ? implode(',', $request->input('stock')): null;
        $activity       = is_array($request->input('activity')) ? implode(',', $request->input('activity')): null;
        $settings       = is_array($request->input('settings')) ? implode(',', $request->input('settings')): null;
        $reports        = is_array($request->input('reports')) ? implode(',', $request->input('reports')): null;
        $user           = is_array($request->input('user')) ? implode(',', $request->input('user')): null;
        $size           = is_array($request->input('size')) ? implode(',', $request->input('size')): null;
        $vat           = is_array($request->input('vat')) ? implode(',', $request->input('vat')): null;

        $this->roleService->update($warehouse, $unit, $stock, $purchase, $transaction, $category,
                                $shelf, $customer, $supplier, $activity, $settings, $reports,$user, $userid, $size, $vat);


        return redirect('/user')->with('success', __('text.msg_assign_updated'));

    }

    /**
     * Get all models and their corresponding tables.
     *
     * @return array
     */
    public function getAllModelsAndTables()
    {
        // Get all models in the "app/Models" directory
        $modelFiles = glob(app_path('Models/*.php'));

        // Initialize an array to store model names and their corresponding table names
        $modelTableMapping = [];

        foreach ($modelFiles as $modelFile) {
            // Extract the model class name from the file path
            $modelClass = 'App\\Models\\' . pathinfo($modelFile, PATHINFO_FILENAME);

            // Get the model instance
            $modelInstance = new $modelClass;

            // Get the table name associated with the model
            $tableName = $modelInstance->getTable();

            // Add the model class and table name to the mapping array
            $modelTableMapping[$modelClass] = $tableName;
        }

        return $modelTableMapping;
    }


    /**
     * Get roles by user ID.
     *
     * @param  int  $userid
     * @return mixed
     */
    public function getrolebyid($userid){
        $data = $this->roleService->getByUser($userid);
        return $data;
    }


    /**
     * Reset email to email
     *
     * @param  int  $userid
     * @return mixed
     */
    public function sendResetLinkEmail(Request $request)
    {
        $email = $request->input('forgotemail');
        $checkemail = $this->userService->getUserByEmail($email);
        $token = Str::random(64);

        if($checkemail){

            $sendreset = $this->userService->sendResetLink($email, $token);
            //$reset = $this->userService->resetPassword($request->input('forgotemail'), $token);

            Mail::send('user.forgotpassword', ['token' => $token,'email'=>$email], function($message) use($request, $email){
                $message->to($email);
                $message->subject('Reset Password');
            });
            return back()->with('status', __('text.msg_password_reset_link_sent'));
        }else{
            return back()->with('statuserror', __('text.msg_email_does_not_exist'));
        }

    }



}
