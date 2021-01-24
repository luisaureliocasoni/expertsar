<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableMantenedores extends AbstractMigration
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
        $table = $this->table('Mantenedores');

        $table->addColumn('usuario', 'string', ['limit' => 50])
            ->addColumn('nome', 'string', ['limit' => 100])
            ->addColumn('email', 'string', ['limit' => 50])
            ->addColumn('senha', 'string', ['limit' => 64])
            ->addIndex(['usuario'], ['unique' => true])
            ->create();
    }
}
