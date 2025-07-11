<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add columns required by Chatify
            if (!Schema::hasColumn('users', 'active_status')) {
                $table->boolean('active_status')->default(0);
            }
            
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->default(config('chatify.user_avatar.default', 'avatar.png'));
            }
            
            if (!Schema::hasColumn('users', 'dark_mode')) {
                $table->boolean('dark_mode')->default(0);
            }
            
            if (!Schema::hasColumn('users', 'messenger_color')) {
                $table->string('messenger_color')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['active_status', 'avatar', 'dark_mode', 'messenger_color'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};