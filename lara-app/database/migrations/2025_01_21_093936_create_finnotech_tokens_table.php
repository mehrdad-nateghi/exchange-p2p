<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finnotech_tokens', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique()->index();

            // Token type (CLIENT_CREDENTIALS = 1 or AUTHORIZATION_CODE = 2)
            $table->tinyInteger('token_type')->index();

            // The actual tokens
            $table->text('access_token');
            $table->text('refresh_token')->nullable();

            // Token metadata
            $table->json('scopes')->nullable(); // Token permissions
            $table->string('national_id', 20)->nullable()->index(); // National ID
            $table->string('bank_code', 6)->nullable()->index(); // Bank identifier

            // Token lifetime management
            $table->unsignedBigInteger('lifetime')->comment('Token lifetime in milliseconds');
            $table->timestamp('expires_at')->index();

            // Status tracking
            $table->boolean('is_active')->default(true)->index();
            $table->integer('refresh_count')->default(0)->comment('Number of times this token has been refreshed');

            // Optional: Additional tracking fields
            //$table->json('last_usage')->nullable()->comment('Track when and where token was last used');
            $table->json('metadata')->nullable()->comment('Any additional token metadata');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finnotech_tokens');
    }
};
