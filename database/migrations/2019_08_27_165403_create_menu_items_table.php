<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id');
            $table->string('title');
            $table->string('slug');
            $table->string('url')->nullable();
            $table->string('target')->default('_self');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('order')->unsigned()->default(0);
            $table->string('route')->nullable();
            $table->text('params')->nullable();
            $table->string('controller')->nullable();
            $table->string('middleware')->nullable();
            $table->string('icon')->nullable();
            $table->string('custom_class')->nullable();
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
        Schema::dropIfExists('menu_items');
    }
}
