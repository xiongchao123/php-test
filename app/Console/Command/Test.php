<?php

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tool\Algorithm\WeightCalculator;

class Test extends Command
{
    protected function configure()
    {
        $this->setName('test');
        $this->setHelp("test");
        $this->setDescription("just for test");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $weight = ['A' => 45.5, 'B' => 20.5, 'C' => 19.5,'D'=>9.5];
        $lib = new WeightCalculator();
        $lib->setWeightValues($weight);

        $aCount = $bCount = $cCount = $dCount =  0;
        for ($i = 0; $i < 1000000; $i++) {
            $key = $lib->getRandomKey();
            switch ($key) {
                case 'A':
                    $aCount++;
                    break;
                case 'B':
                    $bCount++;
                    break;
                case 'C':
                    $cCount++;
                    break;
                case 'D':
                    $dCount++;
                    break;
            }
        }

        $output->writeln("<info>zero count : $aCount</info>");
        $output->writeln("<info>one count : $bCount</info>");
        $output->writeln("<info>one count : $cCount</info>");
        $output->writeln("<info>one count : $dCount</info>");
    }

    protected function testRandom(OutputInterface $output)
    {
        $zeroCount = $oneCount = 0;
        for ($i = 0; $i < 100000; $i++) {
            if (random_int(0, 1) === 0) {
                $zeroCount++;
            } else {
                $oneCount++;
            }
        }

        $output->writeln("<info>zero count : $zeroCount</info>");
        $output->writeln("<info>one count : $oneCount</info>");
    }
}