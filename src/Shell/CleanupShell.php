<?php

namespace LoginAttempts\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Cleaning up attempts table shell command.
 */
class CleanupShell extends Shell
{
    /**
     * Gets the option parser instance and configures it.
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->getDescription('Cleaning up attempts table, delete expired record.');

        return $parser;
    }

    /**
     * main() method.
     *
     * @return void
     */
    public function main()
    {
        TableRegistry::get('LoginAttempts.Attempts')->cleanup();
        $this->out('Cleaning up attempts table.');
    }
}
