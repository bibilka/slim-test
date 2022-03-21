<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

/**
 * Миграция для создания таблиц access и refresh токенов.
 */
final class CreateAccessTokensTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change(): void
    {
        // create access_tokens table
        $table = $this->table('oauth_access_tokens');
        $table->addColumn('identifier', 'string') // идентификатор
                ->addColumn('user_id', 'integer', ['null' => false]) // пользователь токена
                ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addColumn('revoked', 'boolean', ['default' => false]) // признак токен анулирован или нет
                ->addColumn('issued_at', 'timestamp', 'timestamp', [ // дата выпуска токена
                    'timezone' => true,
                ])
                ->addColumn('expired_at', 'timestamp', 'timestamp', [ // дата окончания валидности токена
                    'timezone' => true,
                ])
                ->create();
        $table->save();
        $this->execute('ALTER TABLE `oauth_access_tokens` MODIFY COLUMN `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

        // create refresh_tokens table
        $table = $this->table('oauth_refresh_tokens');
        $table->addColumn('access_token_id', 'integer', ['null' => false]) // указатель на токен доступа
                ->addForeignKey('access_token_id', 'oauth_access_tokens', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addColumn('identifier', 'string') // идентификатор
                ->addColumn('revoked', 'boolean', ['default' => false]) // признак токен анулирован или нет
                ->addColumn('issued_at', 'timestamp', 'timestamp', [ // дата выпуска токена
                    'timezone' => true,
                    'default' => Literal::from('now()')
                ])
                ->addColumn('expired_at', 'timestamp', 'timestamp', [ // дата окончания валидности токена
                    'timezone' => true,
                ])
                ->create();
 
        $table->save();
        $this->execute('ALTER TABLE `oauth_refresh_tokens` MODIFY COLUMN `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
}
