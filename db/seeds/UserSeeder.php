<?php

use Phinx\Seed\AbstractSeed;

/**
 * Seeder для таблицы пользователей в базе данных.
 */
class UserSeeder extends AbstractSeed
{
    /**
     * Добавляет в базу данных 10 новых пользователей со случайными данными.
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'name'        => $faker->userName,
                'password'    => sha1($faker->password),
                'created_at'  => date('Y-m-d H:i:s'),
            ];
        }

        $this->table('users')->insert($data)->saveData();
    }
}
