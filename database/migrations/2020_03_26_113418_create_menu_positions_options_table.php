<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuPositionsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_position_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('values');
        });
        Schema::create('menu_position_option_values', function (Blueprint $table) {
            $table->id();
            $table->integer('menu_position_id')->index();
            $table->integer('menu_position_option_id');
            $table->string('value');
            $table->decimal('price')->default(0);

            $table->foreign('menu_position_id', 'menu_position_option_values_position_id_fk')
                ->references('id')
                ->on('menu_positions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('menu_position_option_id', 'menu_position_option_values_option_id_fk')
                ->references('id')
                ->on('menu_position_options')
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
        Schema::dropIfExists('menu_position_option_values');
        Schema::dropIfExists('menu_position_options');
    }
}
