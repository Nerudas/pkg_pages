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


defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;

class PagesRouter extends RouterView
{
	/**
	 * Router constructor
	 *
	 * @param   JApplicationCms $app  The application object
	 * @param   JMenu           $menu The menu object to work with
	 *
	 * @since 1.0.0
	 */
	public function __construct($app = null, $menu = null)
	{
		// Page route
		$page = new RouterViewConfiguration('page');
		$page->setKey('id');
		$this->registerView($page);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * Method to get the segment(s) for page view
	 *
	 * @param   string $id    ID of the item to retrieve the segments for
	 * @param   array  $query The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 *
	 * @since  1.0.0
	 */
	public function getPageSegment($id, $query)
	{
		return (!empty($id)) ? array($id => $id) : false;
	}

	/**
	 * Method to get the id for page view
	 *
	 * @param   string $segment Segment to retrieve the ID for
	 * @param   array  $query   The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 *
	 * @since  1.0.0
	 */
	public function getPageId($segment, $query)
	{
		return (!empty($segment)) ? $segment : false;
	}
}

function pagesBuildRoute(&$query)
{
	$app    = Factory::getApplication();
	$router = new PagesRouter($app, $app->getMenu());

	return $router->build($query);
}

function pagesParseRoute($segments)
{
	$app    = Factory::getApplication();
	$router = new PagesRouter($app, $app->getMenu());

	return $router->parse($segments);
}