<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}search_page")
 */
class SearchPage extends AbstractSearchPage
{
    public function getSearcher()
    {
        return 'hgabka_lucene.search.node';
    }	
    
    /*
     * return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:Pages\SearchPage:view.html.twig';
    }
}
