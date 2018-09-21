<?php
/**
 * @package    Pages Component
 * @version    1.1.0
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
	 *
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @since  1.0.0
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
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