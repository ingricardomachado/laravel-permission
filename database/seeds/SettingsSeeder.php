<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert(array(        
            'company'      => 'MTS, Management Technical Services',
            'NIT'      => 'J-9999999',
            'address'      => 'Avda Salvador Allende 0131, Sitio 2A-1',
            'phone'      => ' +52 2584304',
            'email'      => 'atencion.cliente@mts.com',
        	'coin' => '$',
        	'money_format' => 'PC2',
        	'created_at' => '2020-12-01',
        	'updated_at' => '2020-12-01',            
 		));    
    }
}
