<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class CreateAccessTokensTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // create the table
        $table = $this->table('oauth_access_tokens');
        $table->addColumn('token', 'text')
                ->addColumn('identifier', 'string')
                ->addColumn('user_id', 'integer', ['null' => false])
                ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addColumn('issued_at', 'timestamp', 'timestamp', [
                    'timezone' => true,
                ])
                ->addColumn('expired_at', 'timestamp', 'timestamp', [
                    'timezone' => true,
                ])
                ->create();
        $table->save();
        $this->execute('ALTER TABLE `oauth_access_tokens` MODIFY COLUMN `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

        $table = $this->table('oauth_refresh_tokens');
        $table->addColumn('refresh_token', 'text')
                ->addColumn('identifier', 'string')
                ->addColumn('access_token_id', 'integer', ['null' => false])
                ->addForeignKey('access_token_id', 'oauth_access_tokens', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addColumn('issued_at', 'timestamp', 'timestamp', [
                    'timezone' => true,
                    'default' => Literal::from('now()')
                ])
                ->addColumn('expired_at', 'timestamp', 'timestamp', [
                    'timezone' => true,
                ])
                ->create();
 
        $table->save();
        $this->execute('ALTER TABLE `oauth_refresh_tokens` MODIFY COLUMN `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
}
