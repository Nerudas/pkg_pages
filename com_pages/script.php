<?php
/**
 * @package    Pages Component
 * @version    1.0.3
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class com_PagesInstallerScript
{
	/**
	 * Runs right after any installation action is preformed on the component.
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	function postflight()
	{
		$path = '/components/com_pages';
		$this->fixTables($path);
		$this->tagsIntegration();
		$this->createImageFolders();

		return true;
	}

	/**
	 * Create or image folders
	 *
	 * @since  1.0.0
	 */
	protected function createImageFolders()
	{
		$folder = JPATH_ROOT . '/images/pages';
		if (!JFolder::exists($folder))
		{
			JFolder::create($folder);
			JFile::write($folder . '/index.html', '<!DOCTYPE html><title></title>');
		}
	}

	/**
	 * Create or update tags integration
	 *
	 * @since  1.0.0
	 */
	protected function tagsIntegration()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('type_id')
			->from($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_pages.page'));
		$db->setQuery($query);
		$current_id = $db->loadResult();

		$page                                               = new stdClass();
		$page->type_id                                      = (!empty($current_id)) ? $current_id : '';
		$page->type_title                                   = 'Pages Page';
		$page->type_alias                                   = 'com_pages.page';
		$page->table                                        = new stdClass();
		$page->table->special                               = new stdClass();
		$page->table->special->dbtable                      = '#__pages';
		$page->table->special->key                          = 'id';
		$page->table->special->type                         = 'Pages';
		$page->table->special->prefix                       = 'PagesTable';
		$page->table->special->config                       = 'array()';
		$page->table->common                                = new stdClass();
		$page->table->common->dbtable                       = '#__ucm_content';
		$page->table->common->key                           = 'ucm_id';
		$page->table->common->type                          = 'Corecontent';
		$page->table->common->prefix                        = 'JTable';
		$page->table->common->config                        = 'array()';
		$page->table                                        = json_encode($page->table);
		$page->rules                                        = '';
		$page->field_mappings                               = new stdClass();
		$page->field_mappings->common                       = new stdClass();
		$page->field_mappings->common->core_content_item_id = 'id';
		$page->field_mappings->common->core_title           = 'title';
		$page->field_mappings->common->core_state           = 'state';
		$page->field_mappings->common->core_alias           = 'id';
		$page->field_mappings->common->core_created_time    = 'null';
		$page->field_mappings->common->core_modified_time   = 'null';
		$page->field_mappings->common->core_body            = 'html';
		$page->field_mappings->common->core_hits            = 'hits';
		$page->field_mappings->common->core_publish_up      = 'null';
		$page->field_mappings->common->core_publish_down    = 'null';
		$page->field_mappings->common->core_access          = 'null';
		$page->field_mappings->common->core_params          = 'attribs';
		$page->field_mappings->common->core_featured        = 'null';
		$page->field_mappings->common->core_metadata        = 'metadata';
		$page->field_mappings->common->core_language        = 'null';
		$page->field_mappings->common->core_images          = 'images';
		$page->field_mappings->common->core_urls            = 'null';
		$page->field_mappings->common->core_version         = 'null';
		$page->field_mappings->common->core_ordering        = 'id';
		$page->field_mappings->common->core_metakey         = 'metakey';
		$page->field_mappings->common->core_metadesc        = 'metadesc';
		$page->field_mappings->common->core_catid           = 'null';
		$page->field_mappings->common->core_xreference      = 'null';
		$page->field_mappings->common->asset_id             = 'null';
		$page->field_mappings->special                      = new stdClass();
		$page->field_mappings                               = json_encode($page->field_mappings);
		$page->router                                       = 'PagesHelperRoute::getPageRoute';
		$page->content_history_options                      = '';

		(!empty($current_id)) ? $db->updateObject('#__content_types', $page, array('type_id'))
			: $db->insertObject('#__content_types', $page);
	}


	/**
	 *
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @since  1.0.0
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		$db = Factory::getDbo();
		// Remove content_type
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_pages.page'));
		$db->setQuery($query)->execute();

		// Remove tag_map
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__contentitem_tag_map'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_pages.page'));
		$db->setQuery($query)->execute();

		// Remove ucm_content
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__ucm_content'))
			->where($db->quoteName('core_type_alias') . ' = ' . $db->quote('com_pages.page'));
		$db->setQuery($query)->execute();

		// Remove images
		JFolder::delete(JPATH_ROOT . '/images/pages');
	}

	/**
	 * Method to fix tables
	 *
	 * @param string $path path to component directory
	 *
	 * @since  1.0.0
	 */
	protected function fixTables($path)
	{
		$file = JPATH_ADMINISTRATOR . $path . '/sql/install.mysql.utf8.sql';
		if (!empty($file))
		{
			$sql = JFile::read($file);

			if (!empty($sql))
			{
				$db      = Factory::getDbo();
				$queries = $db->splitSql($sql);
				foreach ($queries as $query)
				{
					$db->setQuery($db->convertUtf8mb4QueryToUtf8($query));
					try
					{
						$db->execute();
					}
					catch (JDataBaseExceptionExecuting $e)
					{
						JLog::add(Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()),
							JLog::WARNING, 'jerror');
					}
				}
			}
		}
	}
}