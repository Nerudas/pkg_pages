<?php
/**
 * @package    Pages Component
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class PagesViewPage extends HtmlView
{
	/**
	 * Item object
	 *
	 * @var    object
	 *
	 * @since  1.0.0
	 */
	protected $item;

	/**
	 * params data
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  1.0.0
	 */
	protected $params;

	/**
	 * State data
	 *
	 * @var    \Joomla\CMS\Object\CMSObject
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise an Error object.
	 *
	 * @throws Exception
	 *
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		$app        = Factory::getApplication();
		$dispatcher = JEventDispatcher::getInstance();

		$this->item  = $this->get('Item');
		$this->link  = $this->item->link;
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Check menu assign
		$active      = $app->getMenu()->getActive();
		$activeQuery = (!empty($active->query)) ? $active->query : array();
		$activeQuery = new Registry($activeQuery);
		if ($activeQuery->get('option') != 'com_pages' || $activeQuery->get('view') != 'page' ||
			$activeQuery->get('id' != $this->item->id))
		{
			JError::raiseError(404, Text::_('COM_PAGES_ERROR_PAGE_NOT_FOUND'));
		}

		// Create a shortcut for $item.
		$item = $this->item;

		// Merge item params. If this is single-item view, menu params override item params
		// Otherwise, item params override menu item params
		$this->params = $this->state->get('params');
		$temp         = clone $this->params;
		$currentLink  = $active->link;

		// If the current view is the active item and an item view for this item, then the menu item params take priority
		if (strpos($currentLink, 'view=page') && strpos($currentLink, '&id=' . (string) $item->id))
		{
			// Load layout from active query (in case it is an alternative menu item)
			if (isset($active->query['layout']))
			{
				$this->setLayout($active->query['layout']);
			}
			// Check for alternative layout of item
			elseif ($layout = $item->params->get('page_layout'))
			{
				$this->setLayout($layout);
			}

			// $item->params are the item params, $temp are the menu item params
			// Merge so that the menu item params take priority
			$item->params->merge($temp);
		}
		else
		{
			// Current view is not a single item, so the item params take priority here
			// Merge the menu item params with the item params so that the item params take priority
			$temp->merge($item->params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-item menu item)
			// Single-item menu item layout takes priority over alt layout for an item
			if ($layout = $item->params->get('page_layout'))
			{
				$this->setLayout($layout);
			}
		}


		$offset = $this->state->get('list.offset');

		// Process the content plugins.
		PluginHelper::importPlugin('content');

		$item->text = &$item->content;

		$dispatcher->trigger('onContentPrepare', array('com_pages.page', &$item, &$item->params, $offset));

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		// Set hits
		$this->getModel()->hit();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function _prepareDocument()
	{
		$app      = Factory::getApplication();
		$pathway  = $app->getPathway();
		$item     = $this->item;
		$url      = rtrim(URI::root(), '/') . $item->link;
		$sitename = $app->get('sitename');
		$menus    = $app->getMenu();
		$menu     = $menus->getActive();

		$this->params->def('page_heading', $this->params->get('page_title', $menu->title));

		// Set pathway title
		$title = array();
		foreach ($pathway->getPathWay() as $value)
		{
			$title[] = $value->name;
		}
		$title = (!empty($title)) ? implode(' / ', $title) : $this->params->get('page_title', $menu->title);

		// Set Meta Title
		$this->document->setTitle($title);

		// Set Meta Description
		if (!empty($item->metadesc))
		{
			$this->document->setDescription($item->metadesc);
		}
		elseif ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		// Set Meta Keywords
		if (!empty($item->metakey))
		{
			$this->document->setMetadata('keywords', $item->metakey);
		}
		elseif ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		// Set Meta Robots
		if ($item->metadata->get('robots', ''))
		{
			$this->document->setMetadata('robots', $item->metadata->get('robots', ''));
		}
		elseif ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		// Set Meta Author
		if ($app->get('MetaAuthor') == '1' && $item->metadata->get('author', ''))
		{
			$this->document->setMetaData('author', $item->metadata->get('author'));
		}

		// Set Meta Rights
		if ($item->metadata->get('rights', ''))
		{
			$this->document->setMetaData('author', $item->metadata->get('rights'));
		}

		// Set Meta Image
		if ($item->metadata->get('image', ''))
		{
			$this->document->setMetaData('image', URI::base() . $item->metadata->get('image'));
		}
		elseif ($this->params->get('menu-meta_image', ''))
		{
			$this->document->setMetaData('image', Uri::base() . $this->params->get('menu-meta_image'));
		}

		// Set Meta twitter
		$this->document->setMetaData('twitter:card', 'summary_large_image');
		$this->document->setMetaData('twitter:site', $sitename);
		$this->document->setMetaData('twitter:creator', $sitename);
		$this->document->setMetaData('twitter:title', $this->document->getTitle());
		if ($this->document->getMetaData('description'))
		{
			$this->document->setMetaData('twitter:description', $this->document->getMetaData('description'));
		}
		if ($this->document->getMetaData('image'))
		{
			$this->document->setMetaData('twitter:image', $this->document->getMetaData('image'));
		}
		$this->document->setMetaData('twitter:url', $url);

		// Set Meta Open Graph
		$this->document->setMetadata('og:type', 'website', 'property');
		$this->document->setMetaData('og:site_name', $sitename, 'property');
		$this->document->setMetaData('og:title', $this->document->getTitle(), 'property');
		if ($this->document->getMetaData('description'))
		{
			$this->document->setMetaData('og:description', $this->document->getMetaData('description'), 'property');
		}
		if ($this->document->getMetaData('image'))
		{
			$this->document->setMetaData('og:image', $this->document->getMetaData('image'), 'property');
		}
		$this->document->setMetaData('og:url', $url, 'property');

		// Set CSS
		if (!empty($item->css))
		{
			if (!empty($item->css->get('files')))
			{
				foreach (ArrayHelper::fromObject($item->css->get('files')) as $file)
				{
					if (!empty($file['file']))
					{
						$options             = array();
						$options['version']  = $file['version'];
						$options['relative'] = $file['relative'];

						HTMLHelper::stylesheet($file['file'], $options);
					}
				}
			}
			if (!empty($item->css->get('code')))
			{
				$this->document->addStyleDeclaration($item->css->get('code'));
			}
		}
		// Set CSS
		if (!empty($item->js))
		{
			if ($item->js->get('jquery'))
			{
				HTMLHelper::_('jquery.framework');
			}
			if (!empty($item->js->get('files')))
			{
				foreach (ArrayHelper::fromObject($item->js->get('files')) as $file)
				{
					if (!empty($file['file']))
					{
						$options             = array();
						$options['version']  = $file['version'];
						$options['relative'] = $file['relative'];

						$attribs = array();
						if (!empty($file['async']))
						{
							$attribs['async'] = 'async';
						}
						if (!empty($file['defer']))
						{
							$attribs['defer'] = 'defer';
						}
						HTMLHelper::script($file['file'], $options, $attribs);
					}
				}
			}
			if (!empty($item->js->get('code')))
			{
				$this->document->addScriptDeclaration($item->js->get('code'));
			}
		}
	}
}