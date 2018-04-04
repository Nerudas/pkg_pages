<?php
/**
 * @package    Pages - Administrator Module
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ModPagesAdminHelper
{
	/**
	 * Get Items
	 *
	 * @return bool|string
	 *
	 * @since 1.0.0
	 */
	public static function getAjax()
	{
		if ($params = self::getModuleParams(Factory::getApplication()->input->get('module_id', 0)))
		{
			$app = Factory::getApplication();

			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pages/models', 'PagesModel');
			$model = BaseDatabaseModel::getInstance('Pages', 'PagesModel', array('ignore_request' => false));
			$app->setUserState('com_pages.pages.list', array(
				'fullordering' => $params->get('ordering', 'p.hits DESC'),
				'limit'        => $params->get('limit', 5)
			));

			$items = $model->getItems();
			$app->setUserState('com_pages.pages.list', '');

			if (count($items))
			{
				ob_start();
				require ModuleHelper::getLayoutPath('mod_' . $app->input->get('module'),
					$params->get('layout', 'default') . '_items');
				$response = ob_get_contents();
				ob_end_clean();

				return $response;
			}
		}

		throw new Exception(Text::_('MOD_PAGES_ADMIN_ERROR_MODULE_NOT_FOUND'), 404);
	}

	/**
	 * Get Module parameters
	 *
	 * @param int $pk module id
	 *
	 * @return bool|Registry
	 *
	 * @since 1.0.0
	 */
	protected static function getModuleParams($pk = null)
	{
		$pk = (empty($pk)) ? Factory::getApplication()->input->get('module_id', 0) : $pk;
		if (empty($pk))
		{
			return false;
		}

		// Get Params
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('params')
			->from('#__modules')
			->where('id =' . $pk);
		$db->setQuery($query);
		$params = $db->loadResult();

		return (!empty($params)) ? new Registry($params) : false;
	}
}