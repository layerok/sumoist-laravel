<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('comment')->nullable();
            $table->decimal('sum', 8, 2)->default(0);
            $table->unsignedBigInteger('payment_id')->index()->default(1);
            $table->unsignedBigInteger('delivery_id')->index()->default(1);
            $table->unsignedBigInteger('payment_status_id')->default(10);
            $table->boolean('is_sent_to_poster')->default(0);

            $table->foreign('payment_id')
                ->references('id')->on('payment')
                ->onDelete('restrict');

            $table->foreign('delivery_id')
                ->references('id')->on('delivery')
                ->onDelete('restrict');

            $table->foreign('payment_status_id')
                ->references('id')->on('payment_statuses')
                ->onDelete('restrict');

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
        Schema::dropIfExists('orders');
    }
}
