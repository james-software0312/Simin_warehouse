<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ConfigurationModel;
use App\Models\SettingsModel;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ConfigurationModel::create([
            'config' => 'setup_complete',
            'value' => 0,
        ]);
        ConfigurationModel::create([
            'config' => 'setup_stage',
            'value' => 1,
        ]);

        SettingsModel::create([
            'company' => 'default',
            'language' => 'en',
            'timezone' => 'Asia/Singapore'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
