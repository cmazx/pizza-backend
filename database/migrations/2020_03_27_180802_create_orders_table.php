<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('phone');
            $table->text('address');
            $table->integer('user_id')->nullable();
            $table->time('updated_at');
            $table->time('created_at');
            $table->time('delivered_at')->nullable();
            $table->enum('status', ['new', 'approved', 'delivered', 'cancelled']);

            $table->foreign('user_id', 'orders_user_id_fk')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('order_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('position_id');
            $table->integer('count');
            $table->string('name');
            $table->decimal('price');
            $table->decimal('priceUSD');
            $table->time('updated_at');
            $table->time('created_at');

            $table->foreign('order_id', 'order_positions_order_id_fk')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('position_id', 'order_positions_position_id_fk')
                ->references('id')
                ->on('menu_positions')
                ->onDelete('set null')
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
        Schema::dropIfExists('order_positions');
        Schema::dropIfExists('orders');
    }
}
