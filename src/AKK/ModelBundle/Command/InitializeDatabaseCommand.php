<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/12/17
 * Time: 4:21 PM
 */

namespace AKK\ModelBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('akk:initdb')
            ->setDescription('Initialize the database')
            ->setHelp('Initialize the database with empty tables. Use akk:purge to purge the db first!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userRepository = $this->getContainer()->get('akk_model.repository.user');
        $passTypeRepository = $this->getContainer()->get('akk_model.repository.pass_type');
        $passRepository = $this->getContainer()->get('akk_model.repository.pass');

        $userRepository->initTable();
        $passTypeRepository->initTable();
        $passRepository->initTable();
    }
}