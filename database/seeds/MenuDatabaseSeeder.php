<?php

use Illuminate\Database\Seeder;

class MenuDatabaseSeeder extends Seeder
{
    protected $seedersPath = __DIR__.'/../../database/seeds/';

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seeds = [
            'MenuItemsSeeder',
            'MenuSettingsSeeder',
        ];

        foreach ($seeds as $class) {
            $file = $this->seedersPath.$class.'.php';
            if (file_exists($file) && !class_exists($class)) {
                require_once $file;
            }
            with(new $class())->run();
        }
    }
}
