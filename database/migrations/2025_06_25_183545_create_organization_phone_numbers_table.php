<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('organization_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_phone_numbers');
    }
};
