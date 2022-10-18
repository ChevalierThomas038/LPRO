<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpamFinder
{
    private LoggerInterface $logger;
    protected String $ip;
    private array $textSpam;


    public function __construct(array $textSpam, LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->textSpam= $textSpam;
        $this->logger = $logger;
        $this->ip = $requestStack->getCurrentRequest()->getClientIp();
    }

    public function isSpam(string $text)
    {
        if (array_search($text, $this->textSpam))
        {
            $this->logger->info('error : message, ip : '.$this->ip);
            return true;
        }
        return false;
    }
}