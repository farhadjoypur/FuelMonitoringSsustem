<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::insert([
            [
                'name' => 'Padma Oil Company Ltd.',
                'email' => 'info@padmaoil.gov.bd',
                'code' => 'POCL',
                'type' => 'Government',
                'contact_person' => 'Md. Kamal Hossain',
                'phone' => '+880 2 9898989',
                'status' => 1,
            ],
            [
                'name' => 'Meghna Petroleum Ltd.',
                'email' => 'contact@meghnapetroleum.gov.bd',
                'code' => 'MPL',
                'type' => 'Government',
                'contact_person' => 'Mrs. Fatema Begum',
                'phone' => '+880 2 8787878',
                'status' => 1,
            ],
            [
                'name' => 'Jamuna Fuel Corporation',
                'email' => 'admin@jamunafuel.gov.bd',
                'code' => 'JFC',
                'type' => 'Government',
                'contact_person' => 'Md. Rashidul Islam',
                'phone' => '+880 2 7676767',
                'status' => 1,
            ],
        ]);
    }
}
