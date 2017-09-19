<?php

namespace Kunstmaan\FixturesBundle\Parser\Property;

class Reference implements PropertyParserInterface
{
    /**
     * Check if this parser is applicable.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function canParse($value)
    {
        if (is_string($value) && 0 === strpos($value, '@')) {
            return true;
        }

        return false;
    }

    /**
     * Parse provided value into new data.
     *
     * @param $value
     * @param $providers
     * @param array $references
     *
     * @return mixed
     */
    public function parse($value, $providers = [], $references = [])
    {
        $referenceName = substr($value, 1);

        if (isset($references[$referenceName])) {
            return $references[$referenceName];
        }

        return $value;
    }
}
