<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_archives', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('campaign_id')->nullable();
            $table->unsignedInteger('email_id')->nullable();
            $table->unsignedInteger('subscriber_id')->nullable();
            $table->unsignedInteger('sending_server_id')->nullable();

            $table->string('runtime_message_id')->nullable();
            $table->string('message_id')->nullable();
            $table->string('status')->nullable();
            $table->text('error')->nullable();

            $table->timestamps(); // includes created_at and updated_at

            $table->timestamp('open_at')->nullable();
            $table->string('open_ip_address')->nullable();
            $table->text('open_user_agent')->nullable();

            $table->timestamp('click_at')->nullable();
            $table->text('click_url')->nullable();
            $table->string('click_ip_address')->nullable();
            $table->text('click_user_agent')->nullable();

            $table->timestamp('bounce_at')->nullable();
            $table->string('bounce_status_code')->nullable();
            $table->string('bounce_type')->nullable();
            $table->text('bounce_raw')->nullable();

            $table->timestamp('feedback_at')->nullable();
            $table->string('feedback_type')->nullable();
            $table->text('feedback_raw')->nullable();

            $table->timestamp('unsubscribe_at')->nullable();
            $table->string('unsubscribe_ip_address')->nullable();
            $table->text('unsubscribe_user_agent')->nullable();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->foreign('sending_server_id')->references('id')->on('sending_servers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_archives');
    }
};
