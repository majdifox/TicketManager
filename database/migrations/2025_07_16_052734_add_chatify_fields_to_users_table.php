<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update existing users to have default values for Chatify fields
        DB::table('users')->whereNull('messenger_color')->update([
            'messenger_color' => '#2180f3'
        ]);
        
        DB::table('users')->whereNull('active_status')->update([
            'active_status' => true
        ]);
    }

    public function down()
    {
        // No need to rollback
    }
};