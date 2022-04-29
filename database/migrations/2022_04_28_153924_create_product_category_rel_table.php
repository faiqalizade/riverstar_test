<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')
                ->unsigned();

            $table->bigInteger('category_id')
                ->unsigned();

            $table->softDeletes();


            $table->timestamps();

        });


        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_category_rel');
    }
};
