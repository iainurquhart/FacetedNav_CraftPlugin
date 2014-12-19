<?php
namespace Craft;

class FacetedNavPlugin extends BasePlugin
{

	function getName()
	{
		 return Craft::t('Faceted Navigation');
	}

	function getVersion()
	{
		return '0.1';
	}

	function getDeveloper()
	{
		return 'Iain Urquhart';
	}

	function getDeveloperUrl()
	{
		return 'http://iain.co.nz';
	}

	public function registerSiteRoutes()
{
    return array(
        'test/' => array('action' => 'cocktails/editCocktail'),
    );
}

}