<?php

use EscolaLms\Core\Migrations\EscolaMigration;
use Spatie\Permission\Models\Permission;

class CreatePermissions extends EscolaMigration
{
    public function up()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::create([
            'name'=>'register:payment-transaction',
            'guard_name'=>'api'
        ]);
    }

    public function down()
    {
        Permission::findByName('register:payment-transaction', 'api')->delete();
    }
}
