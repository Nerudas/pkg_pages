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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class PagesControllerPage extends FormController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_PAGES_PAGE';

	/**
	 * Method to update item icon
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since  1.0.0
	 */
	public function updateImages()
	{
		$app   = Factory::getApplication();
		$id    = $app->input->get('id', 0, 'int');
		$value = $app->input->get('value', '', 'raw');
		$field = $app->input->get('field', '', 'raw');
		if (!empty($id) & !empty($field))
		{
			JLoader::register('imageFolderHelper', JPATH_PLUGINS . '/fieldtypes/ajaximage/helpers/imagefolder.php');
			$helper = new imageFolderHelper('images/pages');
			$helper->saveImagesValue($id, '#__pages', $field, $value);
		}

		$app->close();

		return true;
	}
}