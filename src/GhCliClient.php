<?php

namespace GhCli;

use GuzzleHttp\Client;

class GhCliClient
{
    protected $guzzleClient;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->guzzleClient = new Client();
    }

    public function fetchComments($url)
    {
        $query['access_token'] = $this->config['token'];
        $res = $this->guzzleClient->request('GET', $url, ['query' => $query]);

        return json_decode($res->getBody()->getContents());
    }

    public function fetchIssue($number)
    {
        $query['access_token'] = $this->config['token'];
        $url = $this->config['host'] . '/repos/' . $this->config['repo'] . '/issues/' . $number;
        $res = $this->guzzleClient->request('GET', $url, ['query' => $query]);

        return json_decode($res->getBody()->getContents());
    }

    public function fetchIssues($query = [])
    {
        $query['access_token'] = $this->config['token'];
        $issues = $this->fetchIssuePage($this->config['host'] . '/repos/' . $this->config['repo'] . '/issues', $query);

        return $issues;
    }

    protected function fetchIssuePage($url, $query = [])
    {
        $issues = [];

        $requestSettings = [];
        if (!empty($query)) {
            $requestSettings['query'] = $query;
        }

        $res = $this->guzzleClient->request('GET', $url, $requestSettings);
        $issues = array_merge($issues, json_decode($res->getBody()->getContents()));

        $link = $res->getHeader('Link');
        if (stripos($link[0], 'rel="next"') !== false) {
            $link = explode(', ', $link[0]);
            preg_match('/^\<(.*)\>/', $link[0], $matches);

            $issues = array_merge($issues, $this->fetchIssuePage($matches[1]));
        }

        return $issues;
    }
}