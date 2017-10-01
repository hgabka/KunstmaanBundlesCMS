<?php

namespace Kunstmaan\LeadGenerationBundle\Twig;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Kunstmaan\LeadGenerationBundle\Service\PopupManager;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PopupTwigExtension extends \Twig_Extension
{
    /**
     * @var PopupManager
     */
    private $popupManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $popupTypes;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param PopupManager       $popupManager
     * @param ContainerInterface $container
     * @param array              $popupTypes
     * @param bool               $debug
     */
    public function __construct(PopupManager $popupManager, ContainerInterface $container, array $popupTypes, $debug)
    {
        $this->popupManager = $popupManager;
        $this->container = $container;
        $this->popupTypes = $popupTypes;
        $this->debug = $debug;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('lead_generation_render_js_includes', [$this, 'renderJsIncludes'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('lead_generation_render_popups_html', [$this, 'renderPopupsHtml'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('lead_generation_render_initialize_js', [$this, 'renderInitializeJs'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('lead_generation_get_rule_properties', [$this, 'getRuleProperties']),
            new \Twig_SimpleFunction('get_available_popup_types', [$this, 'getAvailablePopupTypes']),
            new \Twig_SimpleFunction('get_available_rule_types', [$this, 'getAvailableRuleTypes']),
        ];
    }

    /**
     * @return string
     */
    public function renderJsIncludes(\Twig_Environment $environment)
    {
        $files = $this->popupManager->getUniqueJsIncludes();

        return $environment->render('KunstmaanLeadGenerationBundle::js-includes.html.twig', ['files' => $files]);
    }

    /**
     * @return string
     */
    public function renderPopupsHtml(\Twig_Environment $environment)
    {
        $popups = $this->popupManager->getPopups();

        return $environment->render('KunstmaanLeadGenerationBundle::popups-html.html.twig', ['popups' => $popups]);
    }

    /**
     * @return string
     */
    public function renderInitializeJs(\Twig_Environment $environment)
    {
        $popups = $this->popupManager->getPopups();

        return $environment->render('KunstmaanLeadGenerationBundle::initialize-js.html.twig', ['popups' => $popups, 'debug' => $this->debug]);
    }

    /**
     * @param AbstractRule $rule
     *
     * @return array
     */
    public function getRuleProperties(AbstractRule $rule)
    {
        $properties = [];
        if (null !== $rule->getService()) {
            $service = $this->container->get($rule->getService());
            if ($service instanceof RuleServiceInterface) {
                $properties = $service->getJsProperties($rule);
            }
        }

        return array_merge($rule->getJsProperties(), $properties);
    }

    /**
     * Get the available popup types.
     *
     * @return array
     */
    public function getAvailablePopupTypes()
    {
        $popups = [];
        foreach ($this->popupTypes as $popupType) {
            $object = new $popupType();
            $popups[$object->getClassname()] = $object->getFullClassname();
        }

        return $popups;
    }

    /**
     * Get the available popup types for a specific popup.
     *
     * @param AbstractPopup $popup
     *
     * @return array
     */
    public function getAvailableRuleTypes(AbstractPopup $popup)
    {
        $rulesTypes = $this->popupManager->getAvailableRules($popup);

        $rules = [];
        foreach ($rulesTypes as $ruleType) {
            $object = new $ruleType();
            $rules[$object->getClassname()] = $object->getFullClassname();
        }

        return $rules;
    }
}
