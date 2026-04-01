<?php

// cakephp/migrations 4.x uses Phinx, 5.x uses Migrations\BaseMigration
if (class_exists('Migrations\BaseMigration')) {
    class_alias('Migrations\BaseMigration', 'CreateAttemptsBase');
} else {
    class_alias('Phinx\Migration\AbstractMigration', 'CreateAttemptsBase');
}

class CreateAttempts extends CreateAttemptsBase
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
