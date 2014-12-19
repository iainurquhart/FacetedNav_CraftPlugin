## Faceted Category Navigation for Craft CMS

### Overview

This plugin was quickly put together to assist in creating faceted navigation across single or multiple category groups.

The following code examples will use an example use case of a Plant Nursery website that requires filtering of plant types, soil types, and light exposures. Ultimately we want a url structure of:

category_group/category_1|category_2/category_group/category_1

In my example a valid url is /catalogue/plants/shrubs/sun/partial-shade|full-sun/soil/sandy-volcanic

The end result when filters are active looks something like this:
![Faceted Navigation](https://s3.amazonaws.com/f.cl.ly/items/2N03250d202f0k1s3z2e/Image%202014-12-19%20at%204.30.09%20pm.png)

### Craft Setup

1. I've created 3 category groups with the handles of `plants`, `soil` and `sun`. Those categories have each been assigned to a field and each assigned to the section `catalogue`. 

![Categories](https://s3.amazonaws.com/f.cl.ly/items/3J120w0E3p301I1D2p0q/Image%202014-12-19%20at%2010.22.12%20pm.png)

Each have a maximum level set of 1 as that is a current limitation of the plugin.

2. I've created a template folder with an index in /craft/templates/catalogue for the front-end.

3. In my `/craft/config/routes.php`, I've added `'catalogue/(.*?)' => 'catalogue/index'` to push all requests no matter how many segments through to the /catalogue template.

4. I've added a bunch of entries and classified them using the available category groups for testing :)

5. Install the plugin and open /craft/templates/catalogue/index

### Craft Plugin Usage

Calling `craft.facetedNav.getNav` allows you to render your navigation sets and output current filters, as well as build a parameter for your main `craft.entries` call when outputting your entries. 

So start with a basic set tag, and pass an array of your category group *handles* to the plugin, and also set a couple variables that will come in useful later.

	{% set navItems = craft.facetedNav.getNav(['plants', 'sun', 'soil']) %}
	{% set relatedTo = '' %}
	{% set params = {section: 'catalogue'} %}

Now that `navItems` is set, you can output your navigation group or groups if more than one category group is set.

	{% for catGroup in navItems.categoryGroups %}
	<h4>{{ catGroup.name }}</h4>
	<ul>
		{% for cat in attribute(navItems.categories, catGroup.handle) %}
			<li{% if cat.active %} class="active"{% endif %}>
				<a href="{{ url('catalogue'~cat.url.add) }}" class="add">{{ cat.title}}</a>
				{% if cat.active %} <a href="{{ url('catalogue'~cat.url.remove) }}" class="remove" title="Remove this filter">&times;</a> {% endif %}
			</li>
		{% endfor %}
	</ul>
	{% endfor %}

We also want to output a breadcrumb/tag-list which shows current filters if there are any and allow for those to be removed by the user, and while we're building that we'll set out parameters for our main `craft.entries` tag.

	{% if navItems.activeCategories|length %}
		{% set relatedTo = ['and'] %}
		<nav>
			<h3>Browsing items filed under:</h3>
			{% for category in navItems.activeCategories %}
				{% set relatedTo = relatedTo|merge([category.model]) %}
				<a href="{{ url('catalogue'~category.url.remove) }}" title="Remove this filter">{{ category.title }} <span>&times;</span></a>
			{% endfor %}
			{% set params = params|merge({relatedTo: relatedTo}) %}
		</nav>
	{% endif %}


Now that we've got our params set, we can call our main `craft.entries` tag:

	{% paginate craft.entries(params).limit(12) as entries %}

		{% for entry in entries %}
			<article>
				<h2>{{ entry.title }}</h2>
				...
			</article>
		{% endfor %}

		{% include '_partials/_pagination' %}

	{% endpaginate %}

Voila! Here's the full template as a gist also: https://gist.github.com/iainurquhart/c6570823898965fcd1c1

### Support

Please log issues in the issue tracker here, if you want to improve/modify the plugin please send a pull request with a description of your changes.

### License / Disclaimer

Copyright (c) 2014 Iain Urquhart

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
