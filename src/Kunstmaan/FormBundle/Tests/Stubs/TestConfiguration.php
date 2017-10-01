<?php

namespace Kunstmaan\FormBundle\Tests\Stubs;

use Doctrine\ORM\Configuration;

/**
 * TestConfiguration.
 */
class TestConfiguration extends Configuration
{
    /**
     * @return null|\Doctrine\ORM\Doctrine\ORM\Mapping\QuoteStrategy
     */
    public function getQuoteStrategy()
    {
        return null;
    }
}
