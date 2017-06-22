<?php

require_once 'vendor/autoload.php';

$config = getenv("HOME") . '/.ghcli';
if (is_file($config)) {
    $config = parse_ini_file($config);
} else {
    echo <<<ENDFILE
!!! WARNING !!!
Please create a config file at ~/.ghcli with the following format:

access_token = "SECRET_ACCESS_TOKEN_FROM_GITHUB"
api_url = "https://api.github.com"
repo = "owner/project"
ENDFILE;
    exit(1);
}

$client = new \GhCli\GhCliClient(['host' => $config['api_url'], 'token' => $config['access_token'], 'repo' => $config['repo']]);

$application = new \Cilex\Application('Github CLI');
$application->command(new \GhCli\Command\IssuesListCommand($client));
$application->command(new \GhCli\Command\IssuesViewCommand($client));
$application->run();