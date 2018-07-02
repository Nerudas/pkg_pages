<?php
/**
 * @package    Pages Component
 * @version    1.0.6
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\RouteHelper;

class PagesHelperRoute extends RouteHelper
{
	/**
	 * Fetches the page route
	 *
	 * @param  int $id Company ID
	 *
	 * @return  string
	 *
	 * @since 1.0.0
	 */
	public static function getPageRoute($id = null)
	{
		return 'index.php?option=com_pages&view=page&id=' . $id;
	}
}
