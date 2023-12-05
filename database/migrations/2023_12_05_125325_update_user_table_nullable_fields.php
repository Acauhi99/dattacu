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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cnpj')->nullable()->change();
            $table->string('telefone')->nullable()->change();
            $table->string('endereco')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cnpj')->nullable(false)->change();
            $table->string('telefone')->nullable(false)->change();
            $table->string('endereco')->nullable(false)->change();
        });
    }
};
