<?php

namespace App\Http\Controllers;

use Artisan;
use Config;
use Exception;
use File;
use Illuminate\Http\Request;
use App\Models\ConfigurationModel;
use App\Models\User;
use App\Services\UserService;
use App\Services\RoleService;
use App\Services\SettingsService;

class InstallController extends Controller
{
    protected array $dbConfig;
    protected $userService;
    protected $roleService;
    protected $settingsService;

    public function __construct(UserService $userService, RoleService $roleService, SettingsService $settingsService)
    {
        set_time_limit(8000000);
        // Dependency injection for services
        $this->userService        = $userService;
        $this->roleService        = $roleService;
        $this->settingsService    = $settingsService;
    }

    /**
     * This function is used to display the index of setup
     * @method GET /setup/start/
     * @return Renderable
     */

    public function index()
    {
        return view('setup.welcome');
    }

    /**
     * This function is used to return View of Account Setup
     * @method GET /setup/account/
     * @return Renderable
     */

     public function account()
     {  
         return view('setup.account');
     }

    /**
     * This function is used to check for the minimum requirements
     * @method GET /setup/requirements/
     * @return Renderable
     */

    public function requirements()
    {
        [$checks, $success] = $this->checkMinimumRequirements();
        return view('setup.requirements', compact('checks', 'success'));
    }

    /**
     * This function is used to check for the minimum requirements
     * @return Array
     */

    public function checkMinimumRequirements()
    {
        $checks = [
            'php_version' => PHP_VERSION_ID >= 70400,
            'extension_bcmath' => extension_loaded('bcmath'),
            'extension_ctype' => extension_loaded('ctype'),
            'extension_json' => extension_loaded('json'),
            'extension_mbstring' => extension_loaded('mbstring'),
            'extension_openssl' => extension_loaded('openssl'),
            'extension_pdo_mysql' => extension_loaded('pdo_mysql'),
            'extension_tokenizer' => extension_loaded('tokenizer'),
            'extension_xml' => extension_loaded('xml'),
            'env_writable' => File::isWritable(base_path('.env')),
            'storage_writable' => File::isWritable(storage_path()) && File::isWritable(storage_path('logs')),
        ];
        $success = (!in_array(false, $checks, true));
        return [$checks, $success];
    }

    /**
     * This function is used to return the view of database setup
     * @method GET /setup/database/
     * @return Renderable
     */

    public function database()
    {
        return view('setup.database');
    }

    /**
     * This function is used to return the view of database setup
     * @method GET /setup/license/
     * @return Renderable
     */

     public function license()
     {
         return view('setup.license');
     }

    /**
     * This function is used to accept the database submitted values and use them accordingly
     * @method POST /setup/database-submit/
     * @param Request
     * @return Renderable
     */
    public function databaseSubmit(Request $request)
    {
        try {
            $request->validate([
                'host' => 'required|ip',
                'port' => 'required|integer',
                'database' => 'required',
                'user' => 'required',
            ]);
            $this->createDatabaseConnection($request->all());
            $migration = $this->runDatabaseMigration();
            if ($migration !== true) {
                return redirect()->back()->withInput()->withErrors([$migration]);
            }
            $this->changeEnvDatabaseConfig($request->all());
            return view('setup.account');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * This function is used to create a database connection
     * @param Array of User Submitted Details Of Database
     * @return Response
     */
    public function createDatabaseConnection($details)
    {
        Artisan::call('config:clear');
        $this->dbConfig = config('database.connections.mysql');
        $this->dbConfig['host'] = $details['host'];
        $this->dbConfig['port'] = $details['port'];
        $this->dbConfig['database'] = $details['database'];
        $this->dbConfig['username'] = $details['user'];
        $this->dbConfig['password'] = $details['password'];
        Config::set('database.connections.setup', $this->dbConfig);
    }

    /**
     * This function is used to run the database migration
     */

    public function runDatabaseMigration()
    {
        try {
            Artisan::call('migrate:fresh', [
                '--database' => 'setup',
                '--force' => 'true',
                '--no-interaction' => true,
            ]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

        /**
     * This function is used to change the Database Config In ENV File
    */

    public function changeEnvDatabaseConfig($config)
    {
        $this->changeEnvValues('DB_HOST', $config['host']);
        $this->changeEnvValues('DB_PORT', $config['port']);
        $this->changeEnvValues('DB_DATABASE', $config['database']);
        $this->changeEnvValues('DB_USERNAME', $config['user']);
        $this->changeEnvValues('DB_PASSWORD', $config['password']);
    }


    /**
     * This function is used to change the ENV Values
     */

    private function changeEnvValues($key, $value)
    {
        file_put_contents(app()->environmentFilePath(), str_replace(
            $key . '=' . env($key),
            $key . '=' . $value,
            file_get_contents(app()->environmentFilePath())
        ));
    }

        /**
     * This function is used to print the setup complete View
     * @return Renderable
     * @method GET /setup/complete/
     */

    public function setupComplete()
    {
        try{
           $setupStage = ConfigurationModel::where('config', 'setup_stage')->firstOrFail();
           if($setupStage['value'] != '3'){
               return redirect()->back()->withInput()->withErrors(['errors' => 'Setup Is Incomplete']);
           }
           $setupStage->update(['value' => '4']);
           ConfigurationModel::where('config', 'setup_complete')->firstOrFail()->update(['value' => '1']);
           return view('setup.complete');
        }catch(Exception $e){
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }



/**
     * This function is used to return View of Configuration
     * @method GET /setup/configuration/
     * @return Renderable
     */

     public function configuration()
     {
         return view('setup.config');
     }

     /**
      * This function is used to save the configuration values in the database
      * @param Request
      * @return Renderable
      * @method POST /setup/configuration-submit/
      */

    public function configurationSubmit(Request $request)
    {
        try{
            $configurations = $this->processInputs($request);
            $configurations['setup_stage'] = '3';
            foreach($configurations as $key => $config){
                ConfigurationModel::updateOrCreate(
                    [
                      'config' => $key
                    ],
                    [
                      'value' => $config
                    ]
                  );
            }
            return redirect()->route('setup.complete');
        }catch(Exception $e){
            return redirect()->route('setup.config')->withInput()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * This function is used to process the inputs
     * It makes the validation first and saves the images etc. to desired path
     * @param Array
     * @return Array
     */

    public function processInputs($request)
    {
        // Set the ID for the settings to be updated
        $id = 1;

        // Validate the form data, ensuring the logo is an image file
        $validatedData = $request->validate([
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Extract relevant data from the request
        $data = $request->only(['company', 'pagename', 'datetime', 'timezone']);

        // Update settings with the provided image
        $settings = $this->settingsService->updatewithimagedata($id, $data, $request->file('logo'));
    }



       /**
     * This function is used to create the user Account
     * @param Request
     * @method POST /setup/account-submit/
     * @return Renderable
     */

     public function accountSubmit(Request $request)
     {
         try {
             $request->validate([
                 'name' => 'required',
                 'email' => 'required|email',
                 'password' => 'required|same:confirm_password',
             ]);
            
             $data           = $request->only(['name', 'email']);
             $hashedPassword = bcrypt($request->password);
    
             $dataUser = $this->userService->createUser($hashedPassword, $data);
             $lastCreatedId = $dataUser->id;


            //create default user role also to table role 
            $this->roleService->generateRole($lastCreatedId);

             $stage = ConfigurationModel::where('config', 'setup_stage')->firstOrFail()->update(['value' => '2']);
             return redirect()->route('setup.configuration');
         } catch (Exception $e) {
             return redirect()->route('setup.account')->withInput()->withErrors([$e->getMessage()]);
         }
     }


    /**
     * This function is used to validate the config submitted input values
     * @param Array
     * @return Array
     */

     public function validateInput($request)
     {
         return $request->validate([
             'config_company_name' => 'required',
             'config_app_name' => 'required',
             'config_app_lang' => 'required|in:en',
             'config_app_logo' => 'required|max:2048|mimes:png,jpeg,jpg,ico,gif',
             'config_app_timestamp' => 'required|in:Asia/Singapore',
             'config_mail_mailer' => 'required|in:smtp',
             'config_mail_host' => 'required',
             'config_mail_port' => 'required|integer',
             'config_mail_encryption' => 'required',
             'config_mail_username' => 'required',
             'config_mail_password' => 'required',
             'config_mail_from' => 'required|email',
         ]);
     }


     public function licenseSubmit(Request $request){
        return view('setup.database');
        // Close cURL session
        curl_close($ch);
     }

}