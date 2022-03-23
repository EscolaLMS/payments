<?php

use EscolaLms\Core\Migrations\EscolaMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedirectUrlToPaymentsTable extends EscolaMigration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('redirect_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('redirect_url');
        });
    }
}
