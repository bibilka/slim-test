<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

/**
 * Миграция для создания таблицы пользователей.
 */
final class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change(): void
    {
        // create the table
        $table = $this->table('users');
        $table->addColumn('name', 'string') // имя
              ->addColumn('password', 'string') // пароль
              ->addColumn('created_at', 'timestamp', [
                'timezone' => true,
                'default' => Literal::from('now()')
                ]) // дата создания
              ->addColumn('updated_at', 'timestamp', [
                'timezone' => true,
                'default' => Literal::from('now()')
                ]) // дата редактирования
              ->create();
        $table->addIndex('name', [
          'unique' => true,
          'name' => 'idx_users_name',
        ]); // уникальный индекс для имени
        $table->save();
    }
}
