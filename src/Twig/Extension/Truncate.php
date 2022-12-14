<?php
    namespace App\Twig\Extension;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;
    
    class Truncate extends AbstractExtension
    {
        public function getFilters()
        {
            return [
                new TwigFilter('truncate', [$this, 'truncate']),
            ];
        }
            
        public function truncate($text, $max = 30)
        {
            if (mb_strlen($text) > $max) {
                $text = mb_substr($text, 0, $max);
                $text = mb_substr($text, 0, mb_strrpos($text,' '));
                $text .= '...';
            }
            return $text;
        }
    }
