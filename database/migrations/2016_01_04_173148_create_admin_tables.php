<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminTables extends Migration
{
    /**
     * Modify once, the whole document is happy and safe.
     * @var string
     */
    private $use_config = 'admin';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config($this->use_config . '.database.connection') ?: config('database.default');

        Schema::connection($connection)->create(config($this->use_config . '.database.users_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 191)->unique();
            $table->string('password', 60);
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.roles_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.permissions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('order')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.menu_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50)->nullable();
            $table->string('uri', 50)->nullable();

            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.role_users_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('user_id');
            $table->string('user_type', 191)->nullable();//laravel polymorphic many to many for multi admin app
            $table->unique(['role_id', 'user_id']);
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.role_permissions_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.role_menu_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('menu_id');
            $table->string('menu_type', 191)->nullable();//laravel polymorphic many to many for multi admin app
            $table->unique(['role_id', 'menu_id']);
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.permission_menu_table'), function (Blueprint $table) {
            $table->bigInteger('permission_id');
            $table->bigInteger('menu_id');
            $table->string('menu_type', 191)->nullable();//laravel polymorphic many to many for multi admin app
            $table->unique(['permission_id', 'menu_id']);
            $table->timestamps();
        });

        Schema::connection($connection)->create(config($this->use_config . '.database.operation_log_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('user_type', 191)->nullable();//laravel polymorphic many to many for multi admin app
            $table->string('path');
            $table->string('method', 10);
            $table->string('ip');
            $table->text('input');
            $table->index('user_id');
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
        $connection = config($this->use_config . '.database.connection') ?: config('database.default');

        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.users_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.roles_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.permissions_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.menu_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.user_permissions_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.role_users_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.role_permissions_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.role_menu_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.permission_menu_table'));
        Schema::connection($connection)->dropIfExists(config($this->use_config . '.database.operation_log_table'));
    }
}
