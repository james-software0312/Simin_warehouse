<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display the settings index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch settings data by ID
        $id = 1;
        $data = $this->settingsService->getdataById($id);
        // Convert data to JSON for use in the view
        $getdata = response()->json($data);

        // Render the settings index view with the retrieved data
        return view('setting.index', ['data' => $data]);
    }

    /**
     * Store new settings based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Extract relevant data from the request
        $data = $request->only([
            'company', 'pagename', 'website', 'datetime', 'company_email', 'company_phone', 'company_address',
            'timezone', 'logo',  
        ]);

        // Create new settings using the extracted data
        $companySettings = $this->settingsService->create($data);

        // Redirect to the settings index page with a success message
        return redirect()->route('setting.index')->with('success', __('text.msg_settings_created'));
    }

    /**
     * Update settings with an image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatewithimage(Request $request)
    {
        // Set the ID for the settings to be updated
        $id = 1;

        // Validate the form data, ensuring the logo is an image file
        $validatedData = $request->validate([
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update settings with the provided image
        $settings = $this->settingsService->updatewithimage($id, $request->file('logo'));

        // Redirect to the settings index page with a success message
        return redirect()->route('setting.index')->with('success', __('text.msg_settings_updated'));
    }

    /**
     * Update settings based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Set the ID for the settings to be updated
        $id = 1;
        
        // Extract relevant data from the request
        $data = $request->only(['company', 'pagename', 'datetime', 'timezone',  'company_email', 'company_phone', 'company_address']);

        // Update settings with the extracted data
        $settings = $this->settingsService->update($id, $data);

        // Redirect to the settings index page with a success message
        return redirect()->route('setting.index')->with('success', __('text.msg_settings_updated'));
    }

    /**
     * Delete settings based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Extract the ID of the settings to be deleted
        $id = $request->input('deleteid');

        // Delete settings with the specified ID
        $this->settingsService->delete($id);

        // Redirect to the settings index page with a success message
        return redirect()->route('setting.index')->with('success', __('text.msg_settings_deleted'));
    }
}
