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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reg_num');
            $table->timestamp('found_date');
            $table->foreignId('country_id')
            ->constrained('countries')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('zip_code');
            $table->foreignId('city_id')
            ->constrained('cities')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('street_address');
            $table->decimal('latitude', 8, 5);
            $table->decimal('longitude', 8, 5);
            $table->string('owner');
            $table->integer('employees');
            $table->foreignId('activity_id')
            ->constrained('activities')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->boolean('active');
            $table->string('email')->unique();
            $table->string('password');
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
        Schema::dropIfExists('companies');
    }
};
