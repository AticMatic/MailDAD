<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use Acelle\Library\StringHelper;

class MakeInlineCss implements StageInterface
{
    public $cssFiles;

    public function __construct(array $cssFiles)
    {
        $this->cssFiles = $cssFiles;
    }

    public function __invoke($html)
    {
        // First of all, remove all <link href=....> from email content
        $html = StringHelper::updateHtml($html, function ($document) use ($html) {
            do {
                $links = $document->getElementsByTagName('link');
                foreach ($links as $link) {
                    $link->remove();
                }
            } while ($links->count() != 0);
        });

        // Then make inline
        return makeInlineCss($html, $this->cssFiles);
    }
}
