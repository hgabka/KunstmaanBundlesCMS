<?php

namespace Kunstmaan\LeadGenerationBundle\Service;

use Doctrine\ORM\EntityManager;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;

class PopupManager
{
    /**
     * @var null|array
     */
    private $popups;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get a list of popup definitions.
     *
     * @return array
     */
    public function getPopups()
    {
        if (null === $this->popups) {
            $this->popups = $this->em->getRepository('KunstmaanLeadGenerationBundle:Popup\AbstractPopup')->findAll();
        }

        return $this->popups;
    }

    /**
     * Get a list of unique javascript files that should be included in the html.
     *
     * @return array
     */
    public function getUniqueJsIncludes()
    {
        $includes = [];
        foreach ($this->getPopups() as $popup) {
            foreach ($popup->getRules() as $rule) {
                $includes[] = $rule->getJsFilePath();
            }
        }

        return array_unique($includes);
    }

    /**
     * @param AbstractPopup $popup
     *
     * @return array
     */
    public function getAvailableRules(AbstractPopup $popup)
    {
        if (null !== $popup->getAvailableRules()) {
            return $popup->getAvailableRules();
        }

        return [
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\AfterXSecondsRule',
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\AfterXScrollPercentRule',
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\MaxXTimesRule',
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\RecurringEveryXTimeRule',
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlBlacklistRule',
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlWhitelistRule',
                'Kunstmaan\LeadGenerationBundle\Entity\Rule\OnExitIntentRule',
            ];
    }
}
