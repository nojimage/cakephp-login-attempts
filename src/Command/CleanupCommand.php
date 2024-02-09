<?php
declare(strict_types=1);

namespace LoginAttempts\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Cleaning up attempts table console command.
 */
class CleanupCommand extends Command
{
    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $parser->setDescription('Cleaning up attempts table, delete expired record.');

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->fetchTable('LoginAttempts.Attempts')->cleanup();
        $io->out('Cleaning up attempts table.');
    }
}
