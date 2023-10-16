<?php

use EscolaLms\Core\Migrations\EscolaMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientSecretToPaymentsTable extends EscolaMigration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('client_secret')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('client_secret');
        });
    }
}
