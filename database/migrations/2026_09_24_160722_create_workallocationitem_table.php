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
        Schema::create('workallocationitem', function (Blueprint $table) {
            $table->bigInteger('IDM');
            $table->integer('Ordinal');
            $table->integer('Qty');
            $table->decimal('Weight', 15, 2);
            $table->integer('FG');

            $table->primary(['IDM', 'Ordinal']);

            $table->foreign('IDM')
                ->references('ID')
                ->on('workallocation')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('FG')
                ->references('Id_product')
                ->on('product')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workallocationitem');
    }
};
