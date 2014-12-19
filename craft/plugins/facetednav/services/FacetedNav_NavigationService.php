<?php
namespace Craft;

class FacetedNav_NavigationService extends BaseApplicationComponent
{
	var $activeFilters = array();
	var $activeCategories = array();
	var $categoryGroups = array();
	var $categories = array();
	var $categoryHandles = array();

	public function getNav($categoryHandles) {

		$this->categoryHandles = (is_array($categoryHandles)) ? $categoryHandles : array($categoryHandles);
		$this->_setActiveFilters();
		$this->_setCategoryGroups();
		$this->_setCategories();

		$r = array(
			'activeFilters' 	=> $this->activeFilters,
			'categoryGroups' 	=> $this->categoryGroups,
			'categories' 		=> $this->categories,
			'activeCategories' 	=> $this->activeCategories
		);

		return $r;
	}


	private function _buildUri($slug, $group)
	{
		
		$activeGroups = array();
		$add = $remove = '';

		foreach($this->categoryHandles as $key)
		{
			if(isset($this->activeFilters[$key]))
			{
				$activeGroups[] = $key;
				$filters = $this->activeFilters[$key];
				$add .= '/'.$key.'/'.implode('|', $filters);

				if(!in_array($slug, $this->activeFilters[$key]) && $key == $group)
				{
					$add .= '|'.$slug;
				}

				foreach($filters as $k => $filter)
				{
					if($slug == $filter)
					{
						unset($filters[$k]);
					}
				}

				if(!empty($filters))
				{
					$remove .= '/'.$key.'/'.implode('|', $filters);
				}
			}
		}

		if(!in_array($group, $activeGroups))
		{
			$add .= '/'.$group.'/'.$slug;
		}
		
		return array('add' => $add, 'remove' => $remove);

	}


	private function _setCategories()
	{
		
		foreach($this->categoryGroups as $group)
		{
			$criteria = craft()->elements->getCriteria(ElementType::Category);
			$criteria->group = $group['handle'];
			$categories = craft()->elements->findElements($criteria);

			foreach($categories as $category)
			{
				$active = false;
				if(isset($this->activeFilters[ $group['handle'] ]))
				{
					if(in_array($category->attributes['slug'], $this->activeFilters[ $group['handle'] ] ))
					{
						$active = true;
					}
				}
				$data = array(
					'attributes' => $category->attributes,
					'title' => $category->title,
					'active' => $active,
					'url' => $this->_buildUri($category->attributes['slug'], $group['handle']),
					'model' => $category
				);
				$this->categories[ $group['handle'] ][] = $data;

				if($active)
				{
					$this->activeCategories[ $category->attributes['slug'] ] = $data;
				}
			}
		}
		
	}

	private function _setCategoryGroups()
	{
		foreach($this->categoryHandles as $handle)
		{
			$catGroup = craft()->categories->getGroupByHandle( $handle );
			$this->categoryGroups[ $handle ] = $catGroup->attributes;
		}
	}

	private function _setActiveFilters()
	{

		$segments = craft()->request->getSegments();

		foreach($segments as $key => $segment)
		{
			if(in_array($segment, $this->categoryHandles) && isset($segments[$key+1]))
			{
				$this->activeFilters[$segment] = explode('|', $segments[$key+1]);
			}
		}
		
		asort($this->activeFilters);
		
	}

}