<?php

namespace GhCli\Command;

use Cilex\Provider\Console\Command;
use GhCli\GhCliClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IssuesListCommand extends Command
{
    protected $client;

    public function __construct(GhCliClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    protected function configure()
    {
        $this->setName('issues:list')
            ->addOption('state', 's', InputOption::VALUE_OPTIONAL, 'State of the issues')
            ->addOption('milestone', 'm', InputOption::VALUE_OPTIONAL, 'Subset by milestone')
            ->setDescription('View issues in a repository')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $issues = $this->client->fetchIssues();
        $table = new \Symfony\Component\Console\Helper\Table($output);
        $table->setHeaders(['Number', 'Title', 'Milestone', 'Author', 'Assigned']);

        foreach ($issues as $issue) {
            $milestone = (!is_null($issue->milestone)) ? '(' . $issue->milestone->number . ') ' . $issue->milestone->title : '';
            $data = [$issue->number, $issue->title, $milestone, $issue->user->login];
            $table->addRow($data);
        }

        $table->render();
    }
}