<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id')->after('id'); // Add this line to define the column
        $table->foreign('role_id')->references('id')->on('roles');
    });
}