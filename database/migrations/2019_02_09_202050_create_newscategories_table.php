<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewscategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newscategories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('active');
            $table->text('detail');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('newsgroup_id')->unsigned();
            $table->foreign('newsgroup_id')->references('id')->on('newsgroups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newscategories');
    }
}
