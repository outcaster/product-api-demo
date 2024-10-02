<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test-command',
    description: 'a test command.',
    hidden: false
)]
class TestCommand extends Command
{
    private string $arg1;

    public function __construct(bool $arg1 = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->arg1 = $arg1;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ...
            ->addArgument('arg1', $this->arg1 ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'Arg1')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->arg1 = $input->getArgument('arg1');

        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        $output->writeln([
            $this->arg1,
        ]);

        return Command::SUCCESS;
    }
}
