<?php

namespace NewVision\FrontendBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;

/**
 * Voter based on the uri
 */
class UriVoter implements VoterInterface
{
    private $uri;

    public function __construct($uri = null)
    {
        $this->uri = $uri;
    }

    public function matchItem(ItemInterface $item)
    {
        if (null === $this->uri || null === $item->getUri()) {
            return null;
        }

        if ($item->getUri() === $this->uri) {
            return true;
        }

        $uri = str_replace('/', '\/', $item->getUri());
        $uri = '/' . $uri . '/';

        if (preg_match('/\/.{2}\/$/', $item->getUri())) {
            return null;
        }

        if (preg_match($uri, $this->uri)) {
            return true;
        }

        return null;
    }
}
