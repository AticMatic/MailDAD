<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        $attachments = table('attachments');
        $emails = table('emails');
        $campaigns = table('campaigns');

        // Step 1: Update customer_id from emails
        DB::statement("
            UPDATE {$attachments} a
            INNER JOIN {$emails} e ON a.email_id = e.id
            SET a.customer_id = e.customer_id
            WHERE a.customer_id IS NULL
        ");

        // Step 2: Update customer_id from campaigns
        DB::statement("
            UPDATE {$attachments} a
            INNER JOIN {$campaigns} c ON a.campaign_id = c.id
            SET a.customer_id = c.customer_id
            WHERE a.customer_id IS NULL
        ");

        // Step 3: Record any attachments still null (log or cleanup)
        $orphans = DB::table('attachments')
            ->whereNull('customer_id')
            ->delete();

        // Step 4: Alter column to be NOT NULL
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        // Revert column back to nullable
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable()->change();
        });

        // Rollback logic for cleanup/logging is optional
    }
};
