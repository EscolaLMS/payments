<?php

use EscolaLms\Core\Migrations\EscolaMigration;
use EscolaLms\Core\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class CreateTransactionRegistrationTable extends EscolaMigration
{
    private string $table = 'escolalms_payments_transaction_registration';

    public function up()
    {
        Schema::create(
            $this->table,
            function (Blueprint $table) {
                $table->id('id');
                $table->unsignedInteger('amount');
                $table->string('currency', 3);
                $table->string('description', 255);
                $table->foreignIdFor(User::class, 'buyer_id');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
