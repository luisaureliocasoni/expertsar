<?php

use Phinx\Seed\AbstractSeed;

class SeedManager extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {

        $table = $this->table('Mantenedores');

        $table->insert([
            [
                'usuario' => 'root',
                'email' => 'root@root',
                'nome' => 'root',
                'senha' => Lib\DAOUtilis::criptografaSenha('root#1234')
            ]
        ])->saveData();

    }
}
