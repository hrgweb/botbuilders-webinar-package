<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uuid')->unique()->index()->nullable();
            $table->integer('schedule_id');
            $table->integer('webinar_id')->index();
            $table->string('affiliate');
            $table->text('webinar_title')->nullable();
            $table->boolean('is_jit')->default(0);
            $table->unsignedBigInteger('webinar_user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->index();
            $table->string('email')->index();
            $table->string('phone_country_code')->nullable();
            $table->string('phone_calling_code')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('webinar_date')->nullable();
            $table->string('webinar_date_label')->nullable();
            $table->string('timezone')->nullable();
            $table->string('gmt')->nullable();
            $table->string('ip')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->text('page')->nullable();
            $table->text('live_room_link')->nullable();
            $table->text('replay_link')->nullable();
            $table->text('webinar_link')->nullable();
            $table->text('custom_live_url')->nullable();
            $table->text('confirmation_link')->nullable();
            $table->text('custom_thankyou_url')->nullable();
            $table->string('password')->nullable();
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
        Schema::dropIfExists('registrants');
    }
};
