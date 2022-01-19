<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            //連携許可の際に得られる許可コード
            $table->longText('zoom_code')->nullable()->default(null);
            //APIにアクセスするためのアクセストークン
            $table->longText('access_token')->nullable()->default(null);
            //アクセストークンを更新するためのトークン
            $table->longText('refresh_token')->nullable()->default(null);
            //アクセストークンの期限を記録しておく APIにアクセスする前にこれでチェック
            $table->timestamp('zoom_expires_in', 0)->nullable()->default(null);
            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
