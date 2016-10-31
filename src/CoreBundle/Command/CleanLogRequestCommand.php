<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanLogRequestCommand extends ContainerAwareCommand
{
    const OPTION_KEEP = 'keep';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:clean:log-request')
            ->setDescription('Remove oldest LogRequest')
            ->addArgument(
                self::OPTION_KEEP,
                InputArgument::OPTIONAL,
                'Period you want to keep',
                '1day'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $periodToKeep = $input->getArgument(self::OPTION_KEEP);

        if (($timestamp = strtotime('now -' . $periodToKeep)) === false) {
            $output->writeln('argument is invalid');
        } else {
            $datetime = \DateTime::createFromFormat('U', $timestamp);

            $qb = $this->getContainer()->get('doctrine.orm.entity_manager')->createQueryBuilder();

            $qb
                ->delete('CoreBundle:LogRequest', 'l')
                ->where('l.createdAt < :before')
                ->setParameter('before', $datetime)
            ;

            $qb->getQuery()->execute();

            $output->writeln('LogRequest was cleaned!');
        }
    }
}
