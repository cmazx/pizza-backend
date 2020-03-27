<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('menu_category_id')->index();
            $table->string('name');
            $table->boolean('active')->index();
            $table->decimal('price');
            $table->string('image');
            $table->text('description')->nullable(true);
            $table->integer('position')->unique();
            $table->foreign('menu_category_id', 'menu_positions_menu_category_id_fk')
                ->references('id')
                ->on('menu_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_categories_positions_unique');
        Schema::dropIfExists('menu_categories_positions');
        Schema::dropIfExists('menu_positions');
    }
}
