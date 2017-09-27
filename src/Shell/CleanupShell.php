<?php

namespace LoginAttempts\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use LoginAttempts\Model\Table\AttemptsTable;

/**
 * Cleanup shell command.
 */
class CleanupShell extends Shell
{

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main()
    {
        TableRegistry::get('LoginAttempts.Attempts')->cleanup();
        $this->out('Cleaning up attempts table.');
    }
}
