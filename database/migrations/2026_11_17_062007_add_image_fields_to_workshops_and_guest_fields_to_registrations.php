<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->text('image_url')->nullable();
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('registrations')->whereNull('user_id')->exists()) {
            throw new LogicException('Guest registrations must be removed before rolling back this migration.');
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('guest_name');
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_url']);
        });
    }
};
