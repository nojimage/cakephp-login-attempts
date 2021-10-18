<?php

namespace LoginAttempts\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

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
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $parser->setDescription('Cleaning up attempts table, delete expired record.');

        return $parser;
    }

    /**
     * main() method.
     *
     * @return void
     */
    public function main()
    {
        $this->loadModel('LoginAttempts.Attempts')->cleanup();
        $this->out('Cleaning up attempts table.');
    }
}
