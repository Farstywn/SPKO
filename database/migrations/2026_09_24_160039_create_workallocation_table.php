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
        if (!Schema::hasTable('workallocation')) {
            Schema::create('workallocation', function (Blueprint $table) {
                $table->bigInteger('ID')->primary();
                $table->text('Remarks')->nullable();
                $table->integer('Employee');
                $table->date('TransDate');
                $table->string('Process', 10);
                $table->string('SW', 25);

                $table->foreign('Employee')
                    ->references('Id_employee')
                    ->on('employee')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workallocation');
    }
};
