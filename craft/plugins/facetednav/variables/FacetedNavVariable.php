<?php
namespace Craft;

class FacetedNavVariable
{
    public function getNav($categoryHandles = array())
    {
    	if(empty($categoryHandles))
    	{
    		throw new HttpException('501', 'No category group handles supplied in getNav, eg: craft.facetedNav.getNav(["plants", "sun", "soil"])');
    	}
        return craft()->facetedNav_navigation->getNav($categoryHandles);
    }

}