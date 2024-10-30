<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->nullable(); // Consider if this should be nullable
            $table->foreignId('session_id')->constrained('appsessions')->onDelete('cascade'); 
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade'); 
            $table->string('dr_name')->nullable(); // Consider if this should be nullable
            $table->boolean('treatment_status')->default(0); 
            $table->text('prescription')->nullable(); 
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
        Schema::dropIfExists('appointments');
    }
}
