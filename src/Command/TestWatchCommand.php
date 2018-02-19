<?php

namespace App\Command;

use App\Manager\WatchManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestWatchCommand extends Command
{
    protected static $defaultName = 'test-watch';

    /** @var WatchManager */
    protected $manager;

    protected function configure()
    {
        $this
            ->setDescription('Test')
        ;
    }

    public function __construct(?string $name = null, WatchManager $manager)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        var_dump($this->manager->getWatchById(1));

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
