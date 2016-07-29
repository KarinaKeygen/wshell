<?php

namespace UnitBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PhpToolbox\Unit;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class UnitRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('unit:run')
            ->setDescription('Command for run PHP unit in console')
            ->addArgument('name', InputArgument::REQUIRED, 'Unit name')
            ->addArgument('args', InputArgument::OPTIONAL, 'Args in json (require file name from Tests/Unit/Data)')
            // ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unitName = $input->getArgument('name');
        $args = $input->getArgument('args');

        $unit = $this->getContainer()->get('mongo')->wshell->units->findOne([
            'name' => $unitName
        ]);

        $fs = new Filesystem();
        $fs->mkdir('/tmp/units', 0700);
        $fs->dumpFile("/tmp/units/$unitName.php", $unit->source);
        require "/tmp/units/$unitName.php";
        file_get_contents("/tmp/units/$unitName.php");

        $storage = new \PhpToolbox\Storages\Test(null);
        $unitRun = new $unitName($storage, $unit->getHookupAsArray());

        if ($args) {
            $args = file_get_contents(__DIR__."/../Tests/Unit/Data/$args");
            $args = json_decode($args, true);
        } else {
            $args = [];
        }


        $time_start = microtime(true);
        $output->writeln( $unitRun->uiOutput($args) );
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        $output->writeln('Time:' . round($time, 4));
        $output->writeln('Command result.');
    }

}
