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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['admin', 'employee'])->default('employee');
            $table->string('employee_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('designation_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->string('name', 191);
            $table->string('email', 50)->unique();
            $table->string('username', 50)->unique();
            $table->string('mobile', 20)->unique()->nullable();
            $table->double('salary',8,2)->default(0.00);
            $table->string('password_plain', 15);
            $table->string('password', 400);
            $table->rememberToken();
            $table->string('image', 255)->nullable();
            $table->string('cv_path', 255)->nullable();
            $table->enum('status', ['active', 'inactive'])->default("active");
            $table->dateTime('last_login_at')->nullable();
            $table->dateTime('last_logout_at')->nullable();
            $table->timestamps();
            $table->integer('created_by');
            $table->integer('password_reset_code')->nullable();
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
