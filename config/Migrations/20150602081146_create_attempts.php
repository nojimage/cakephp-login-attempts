<?php

use Phinx\Migration\AbstractMigration;

class CreateAttempts extends AbstractMigration
{

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('attempts');
        $table->addColumn('ip', 'string', [
            'default' => null,
            'limit'   => 45,
            'null'    => false,
        ]);
        $table->addColumn('action', 'string', [
            'default' => null,
            'limit'   => 64,
            'null'    => false,
        ]);
        $table->addColumn('expires', 'timestamp', [
            'default' => null,
            'null'    => false,
        ]);
        $table->addColumn('created_at', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'update'  => '',
            'null'    => false,
        ]);
        $table->addIndex(['ip', 'action'], ['name' => 'IX_ip_action']);
        $table->create();
    }
}
