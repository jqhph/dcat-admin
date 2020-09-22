<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_extensions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('slug', 100)->unique();
            $table->string('version', 20)->default('');
            $table->tinyInteger('is_enabled')->default(0);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_extension_histories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('slug', 100);
            $table->tinyInteger('type')->default(1);
            $table->string('version', 20)->default(0);
            $table->text('description')->nullable();

            $table->index('slug');
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
        Schema::dropIfExists('admin_extensions');
        Schema::dropIfExists('admin_extension_histories');
    }
}
