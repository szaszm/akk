<?php
/**
 * Created by PhpStorm.
 * User: marci
 * Date: 8/13/17
 * Time: 12:55 PM
 */

namespace AKK\ModelBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('akk:purge')
            ->setDescription('Purge the database')
            ->setHelp('Purge the database');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $userRepository = $this->getContainer()->get('akk_model.repository.user');
        $passTypeRepository = $this->getContainer()->get('akk_model.repository.pass_type');
        $passRepository = $this->getContainer()->get('akk_model.repository.pass');

        $userRepository->purge();
        $passTypeRepository->purge();
        $passRepository->purge();
    }
}