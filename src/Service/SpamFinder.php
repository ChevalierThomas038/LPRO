<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpamFinder
{
    private $logger;
    protected $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function isSpam(string $text): bool
    {
        $spam = array("aaaa","sdfsdf");

        for ($i = 0; $i < $spam.count(); $i++)
        {
            if ($text == $spam($i))
            {
                return true;
                $request = $this->requestStack->getCurrentRequest();
                $text = $text . $request.getClientIp();
                $this->logger->info($text);
            }
        }

        return false;
    }
}