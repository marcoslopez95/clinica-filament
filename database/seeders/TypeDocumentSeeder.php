<?php

namespace Database\Seeders;

use App\Models\TypeDocument;
use Illuminate\Database\Seeder;

class TypeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Cédula', 'code' => 'C.I'],
            ['name' => 'RIF', 'code' => 'RIF'],
        ];

        foreach ($types as $t) {
            TypeDocument::updateOrCreate(['code' => $t['code']], $t);
        }
    }
}
