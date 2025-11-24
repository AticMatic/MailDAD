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
        Schema::dropIfExists('webhook_job_logs');
        Schema::dropIfExists('webhook_jobs');
        Schema::dropIfExists('webhooks');

        Schema::create('http_configs', function (Blueprint $table) {
            // Webhook
            $table->id();
            $table->uuid('uid');
            $table->integer('customer_id')->unsigned()->nullable();

            // HTTP Request
            $table->string('request_method');
            $table->string('request_url')->nullable();
            $table->string('request_auth_type');
            $table->string('request_auth_bearer_token')->nullable();
            $table->string('request_auth_basic_username')->nullable();
            $table->string('request_auth_basic_password')->nullable();
            $table->string('request_auth_custom_key')->nullable();
            $table->string('request_auth_custom_value')->nullable();
            $table->text('request_headers')->nullable();
            $table->string('request_body_type');
            $table->text('request_body_params')->nullable();
            $table->text('request_body_plain')->nullable();

            $table->timestamps();

            // foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::create('http_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->bigInteger('http_config_id')->unsigned()->nullable();
            $table->longText('params');
            $table->string('status')->default(0);
            $table->integer('retries')->default(0);
            
            $table->timestamps();

            // foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('http_config_id')->references('id')->on('http_configs')->onDelete('cascade');
        });

        Schema::create('http_request_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->bigInteger('http_request_id')->unsigned()->nullable();
            $table->longText('request_details');
            $table->string('response_http_code');
            $table->longText('response_content');
            $table->longText('response_error')->nullable();
            
            $table->timestamps();

            // foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('http_request_id')->references('id')->on('http_requests')->onDelete('cascade');
        });

        Schema::create('webhooks', function (Blueprint $table) {
            // Webhook
            $table->id();
            $table->uuid('uid');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->string('event');
            $table->string('name');
            $table->string('status');
            $table->integer('setting_retry_times');
            $table->integer('setting_retry_after_seconds');

            // HTTP Request
            $table->bigInteger('http_config_id')->unsigned()->nullable();

            $table->timestamps();

            // foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('http_config_id')->references('id')->on('http_configs')->onDelete('cascade');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('http_request_logs');
        Schema::dropIfExists('http_requests');
        Schema::dropIfExists('http_configs');
    }
};
