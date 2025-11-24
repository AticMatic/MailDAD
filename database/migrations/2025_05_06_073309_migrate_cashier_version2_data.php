<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Run the PaymentMethodSeeder
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\PaymentMethodSeeder',
        ]);

        // Remove redundant customer columns
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('auto_billing_data');
        });

        // Drop transactions method column
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
