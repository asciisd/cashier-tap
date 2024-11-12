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
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('users', 'phone_code')) {
                $table->string('phone_code')->nullable();
            }

            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('users', 'tap_id')) {
                $table->string('tap_id')->nullable()->index();
                $table->string('card_brand')->nullable();
                $table->string('card_last_four', 4)->nullable();
                $table->timestamp('trial_ends_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tap_id',
                'card_brand',
                'card_last_four',
                'trial_ends_at',
            ]);
        });
    }
};
