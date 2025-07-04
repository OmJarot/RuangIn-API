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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string("user_id",20)->nullable(false);
            $table->unsignedBigInteger("ruangan_id")->nullable(false);
            $table->string("title", 200)->nullable(false);
            $table->text("description")->nullable();
            $table->date("date")->nullable(false);
            $table->time("start")->nullable(false);
            $table->time("end")->nullable(false);
            $table->string("status", 50)->nullable(false)->default("waiting");
            $table->foreign("user_id")->on("users")->references("id");
            $table->foreign("ruangan_id")->on("ruangans")->references("id");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
