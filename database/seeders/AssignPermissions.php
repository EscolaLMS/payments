<?php

namespace EscolaLms\Payments\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignPermissions extends Seeder
{
    public function run()
    {
        #$admin = Role::findOrCreate('admin', 'api');
        #$tutor = Role::findOrCreate('tutor', 'api');
        $student = Role::findOrCreate('student', 'api');

        #$admin->givePermissionTo([]);
        #$tutor->givePermissionTo([]);
        $student->givePermissionTo(['register:payment-transaction']);
    }
}
