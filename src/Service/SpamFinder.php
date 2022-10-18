<?php

declare(strict_types=1);

namespace App\Service;

use http\Client\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpamFinder
{
    private LoggerInterface $logger;
    protected RequestStack $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function send(string $text): bool
    {
        $spam = array("aaaa","sdfsdf");

        foreach ($spam as $text)
        {
            $request = $this->requestStack->getCurrentRequest();
            $text = $text . $request->getClientIp();
            $this->logger->info($text);
            return true;
        }

        return false;
    }
}