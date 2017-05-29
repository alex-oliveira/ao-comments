<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAoCommentsTables extends Migration
{

    public function up()
    {
        Schema::create('ao_comments_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on(config('ao.tables.users'));
            $table->text('message');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('ao_comments_comments');
    }

}