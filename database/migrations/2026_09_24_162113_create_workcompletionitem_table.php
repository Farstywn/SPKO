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
        Schema::create('workcompletionitem', function (Blueprint $table) {
            $table->bigInteger('IDM');
            $table->integer('Ordinal');
            $table->integer('Qty');
            $table->decimal('Weight', 15, 2);
            $table->bigInteger('LinkID')->nullable();
            $table->integer('LinkOrd')->nullable();
            $table->integer('FG');

            $table->primary(['IDM', 'Ordinal']);

            $table->foreign('IDM')
                ->references('ID')
                ->on('workcompletion')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('FG')
                ->references('Id_product')
                ->on('product')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign(['LinkID', 'LinkOrd'])
                ->references(['IDM', 'Ordinal'])
                ->on('workallocationitem')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->index(['LinkID', 'LinkOrd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workcompletionitem');
    }
};
