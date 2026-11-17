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
        Schema::table('registrations', function (Blueprint $table) {
            $table->json('attendees')->nullable();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('tickets_registration_id_unique');
            $table->string('attendee_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('registrations')->whereNotNull('attendees')->exists()) {
            throw new \LogicException('Registration attendee names must be removed before rolling back this migration.');
        }

        if (DB::table('tickets')->whereNotNull('attendee_name')->exists()) {
            throw new \LogicException('Ticket attendee names must be removed before rolling back this migration.');
        }

        if (DB::table('tickets')
            ->select('registration_id')
            ->groupBy('registration_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists()) {
            throw new \LogicException('Multiple tickets per registration must be removed before rolling back this migration.');
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('attendee_name');
            $table->unique('registration_id');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('attendees');
        });
    }
};
