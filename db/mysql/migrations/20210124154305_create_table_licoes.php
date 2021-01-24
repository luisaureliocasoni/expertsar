<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableLicoes extends AbstractMigration
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
        $table = $this->table('Licoes');

        $table->addColumn('nome', 'string', ['limit' => 100])
            ->addColumn('slug', 'string', ['limit' => 150])
            ->addColumn('idMantenedorCriou', 'integer', ['null' => true, 'default' => null])
            ->addColumn('idMantenedorAlterou', 'integer', ['null' => true, 'default' => null])
            ->addColumn('textoLicao', 'text')
            ->addForeignKey('idMantenedorCriou', 'Mantenedores', 'id', ['delete'=> 'SET NULL', 'update'=> 'CASCADE'])
            ->addForeignKey('idMantenedorAlterou', 'Mantenedores', 'id', ['delete'=> 'SET NULL', 'update'=> 'CASCADE'])
            ->create();
    }
}
