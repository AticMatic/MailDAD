<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use Acelle\Library\StringHelper;

use function Acelle\Helpers\is_non_web_link;
use function Acelle\Helpers\getAppHost;

class PrependHostUrl implements StageInterface
{
    public function __invoke($html)
    {

        $out = StringHelper::transformUrls($html, function ($url, $element) {
            // If the given URL is a non-web link, like tel:, mailto:, ftp:...
            // then just skip
            if (is_non_web_link($url)) {
                return $url;
            }

            if (parse_url($url, PHP_URL_HOST) === false) {
                // false ==> if url is invalid (whereas null ==> if url does not have host information)
                return $url;
            }

            if (StringHelper::isTag($url)) {
                return $url;
            }

            if (!is_null(parse_url($url, PHP_URL_HOST))) {
                return $url;
            }

            if (strpos($url, '/') === 0) {
                // absolute url with leading slash (/) like "/hello/world"
                $urlWithHost = join_url(getAppHost(), $url);

                return $urlWithHost;
            } elseif (strpos($url, 'data:') === 0) {
                // base64 image. Like: "data:image/png;base64,iVBOR"
                return $url;
            } else {
                // URL is a relative path like "images/banner.jpg"
                // It is for a particular template only
                return $url;
            }
        });

        return $out;
    }
}
