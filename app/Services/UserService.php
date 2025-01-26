<?php

// app/Services/UserService.php

namespace App\Services;

use Illuminate\Support\Facades\Password;
use App\Models\User;
use Auth;
use DB;
use Str;
use Carbon\Carbon;


class UserService
{
    /**
     * Get the total count of users.
     *
     * @return int
     */
    public function totalitem()
    {
        return User::count();
    }

    /**
     * Create a new user with the given default password and data.
     *
     * @param string $defaultpassword The default password for the new user.
     * @param array $data The user data to create.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createUser($defaultpassword, $data)
    {
        return User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => $defaultpassword]);
    }

    /**
     * Send a password reset link to the user with the given email.
     *
     * @param string $email The email address of the user.
     * @return void
     */
    public function sendResetLink($email, $token)
    {
       return DB::table('password_resets')->insert([
            'email' => $email, 
            'token' => $token,
            'created_at' => Carbon::now()
          ]);
    }

    /**
     * check email and token
     *
     * @param string $email The email address of the user.
     * @param string $token The reset token.
     * @return string|null
     */
    public function checkToken($token){
       return $updatePassword = DB::table('password_resets')
            ->where([
                    'token' => $token
                    ])
            ->first();
    }

    /**
     * Reset the user's password with the given email and token.
     *
     * @param string $email The email address of the user.
     * @param string $token The reset token.
     * @return string|null
     */
    public function resetPassword($email, $password)
    {   
        $reset = User::where('email', $email)->update(['password' => bcrypt($password)]);
        $delete = DB::table('password_resets')->where(['email'=> $email])->delete();
        if($reset){
            return $delete;
        }
        
    }

    /**
     * Update the avatar for the user with the given ID.
     *
     * @param int $id The ID of the user.
     * @param \Illuminate\Http\UploadedFile $avatar The avatar file.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function updateAvatar($id, $avatar)
    {
        $userdata = User::findOrFail($id);

        if ($avatar) {
            $randomString = Str::random(10);
            $customName = 'avatar_' . $randomString;
            $avatarName = $customName . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = $avatar->storeAs('public/avatar', $avatarName);
            $userdata->each->update(['avatar' => $avatarName]);
        }

        return $userdata;
    }

    /**
     * Update the user with the given ID.
     *
     * @param int $id The ID of the user to update.
     * @param array $data The user data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function updateUser($id, $data)
    {
        $userdata = User::findOrFail($id);
        $userdata->each->update($data);
        return $userdata;
    }

    /**
     * Update the profile for the user with the given ID, data, and password.
     *
     * @param int $id The ID of the user to update.
     * @param array $data The user data to update.
     * @param string|null $password The password to update (optional).
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function updateProfile($id, $data, $password)
    {
        $userdata = User::findOrFail($id);
        $userdata->each->update($data);

        if ($password) {
            $userdata->each->update(['password' => $password]);
        }

        return $userdata;
    }

    /**
     * Delete the user with the given ID.
     *
     * @param int $id The ID of the user to delete.
     * @return void
     */
    public function deleteUser($id)
    {
        $data = User::findOrFail($id);
        $data->each->delete();
    }

    /**
     * Get the user by ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Check if a user with the given email exists.
     *
     * @param string $email The email to check.
     * @return bool
     */
    public function getUserByEmail($email)
    {
        $data = User::where('email', $email)->exists();
        return $data;
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUser()
    {
        return User::all();
    }

    /**
     * Authenticate a user with the given credentials.
     *
     * @param array $credentials The user credentials.
     * @return bool
     */
    public function authenticateUser(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            return true;
        }
        return false;
    }
}
