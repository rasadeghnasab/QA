<?php

use App\Enums\PracticeStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_user', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', [PracticeStatusEnum::Correct, PracticeStatusEnum::Incorrect]);
            $table->timestamps();

            $table->primary(['question_id', 'user_id']);

//             put these here to show that we should have foreign key constraints
//            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_user');
    }
}
