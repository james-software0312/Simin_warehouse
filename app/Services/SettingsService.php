<?php

// app/Services/SettingsService.php

namespace App\Services;

use App\Models\SettingsModel;
use App\Models\ConfigurationModel;
use Illuminate\Support\Str;

class SettingsService
{
    /**
     * Create a new settings data.
     *
     * @param array $data The data for creating a new settings.
     * @return \App\Models\SettingsModel
     */
    public function create($data)
    {
        // Create a new data
        return SettingsModel::create($data);
    }

    /**
     * Update settings data by ID.
     *
     * @param int $id The ID of the settings data to update.
     * @param array $data The data for updating the settings.
     * @return \App\Models\SettingsModel
     */
    public function update($id, $data)
    {
        // dd($data);
        $datasetting = SettingsModel::findOrFail($id);
        // dd($datasetting);
        $datasetting->update($data);
        return $datasetting;
    }

    /**
     * Update settings data with an image by ID.
     *
     * @param int $id The ID of the settings data to update.
     * @param \Illuminate\Http\UploadedFile $logo The logo image file.
     * @return \App\Models\SettingsModel
     */
    public function updatewithimage($id, $logo)
    {
        $datasetting = SettingsModel::findOrFail($id);

        if ($logo) {
            $logo_name_with_ext = $logo->getClientOriginalName();
            $logo_extenstion = $logo->getClientOriginalExtension();
            $logoName = pathinfo($logo_name_with_ext, PATHINFO_FILENAME);
            $logoName = strtolower(Str::slug($logoName));
            $logo_db = $logoName . time() . '.' . $logo_extenstion;
            $folder_path = base_path(env('MEDIA_UPLOADER_PATH'));
            // $logoPath = $logo->storeAs('public/settings', $logoName);
            $logo->move($folder_path, $logo_db);
            $datasetting->update(['logo' => $logo_db]);
        }

        return $datasetting;
    }

     /**
     * Update settings data by ID.
     *
     * @param int $id The ID of the settings data to update.
     * @param array $data The data for updating the settings.
     * @return \App\Models\SettingsModel
     */
    public function updatewithimagedata($id, $data, $logo)
    {
        $datasetting = SettingsModel::findOrFail($id);
        $datasetting->update($data);

        if ($logo) {
            $logoName = $logo->getClientOriginalName();
            $logoPath = $logo->storeAs('public/settings', $logoName);
            $datasetting->update(['logo' => $logoName]);
        }


        return $datasetting;
    }

    /**
     * Delete settings data by ID.
     *
     * @param int $id The ID of the settings data to delete.
     * @return void
     */
    public function delete($id)
    {
        $data = SettingsModel::findOrFail($id);
        $data->each->delete();
    }

    /**
     * Get settings data by ID.
     *
     * @param int $id The ID of the settings data to retrieve.
     * @return \App\Models\SettingsModel
     */
    public function getdataById($id)
    {
        return SettingsModel::findOrFail($id);
    }

    /**
     * Get all settings data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAlldata()
    {
        return SettingsModel::all();
    }

    /**
     * Get configuration
     *
     * @param int $id The ID of the settings data to retrieve.
     * @return \App\Models\ConfigurationModel
     */
    public function getConfiguration()
    {
        return ConfigurationModel::count();
    }

    function formatQuantity($value) {
        return (floor($value * 10) === $value * 10) ? (int)$value : round($value, 1);
    }
}
