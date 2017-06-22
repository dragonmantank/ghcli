<?php

namespace GhCli\Command;

use Cilex\Provider\Console\Command;
use GhCli\GhCliClient;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IssuesViewCommand extends Command
{
    /**
     * @var GhCliClient
     */
    protected $client;

    public function __construct(GhCliClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    protected function configure()
    {
        $this->setName('issues:view')
            ->addArgument('number', InputArgument::REQUIRED, 'Issue number to view')
            ->setDescription('View a single issue in a repository')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $issue = $this->client->fetchIssue($input->getArgument('number'));
        $labels = 'No labels';
        $milestone = 'No milestone set';

        if (count($issue->labels)) {
            $labelNames = [];
            foreach ($issue->labels as $label) {
                $labelNames[] = $label->name;
            }
            $labels = implode(', ', $labelNames);
        }

        if ($issue->milestone) {
            $milestone = $issue->milestone->title . ' (' . $issue->milestone->number . ')';
        }

        $table = new Table($output);
        $table->addRow(['<options=bold,underscore>Title:</>',$issue->title]);
        $table->addRow(['<options=bold,underscore>Submitted:</>',$issue->user->login . ' at ' . $issue->created_at]);
        $table->addRow(['<options=bold,underscore>State:</>',$issue->state]);
        $table->addRow(['<options=bold,underscore>Labels:</>', $labels]);
        $table->addRow(['<options=bold,underscore>Milestone:</>', $milestone]);
        $table->addRow(['<options=bold,underscore>Number of Comments:</>',$issue->comments]);
        $table->addRow(['<options=bold,underscore>Webpage:</>',$issue->html_url]);
        $table->render();

        $style = new SymfonyStyle($input, $output);
        $output->writeln($issue->body);

        if ($issue->comments && $input->getOption('verbose')) {
            $comments = $this->client->fetchComments($issue->comments_url);
            foreach ($comments as $comment) {

                $style->title($comment->user->login . ' commented at ' . $comment->created_at . ':');
                $style->text($comment->body);
            }
        }
    }
}