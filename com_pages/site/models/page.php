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

use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

class PagesModelPage extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 *
	 * @since  1.0.0
	 */
	protected $_context = 'com_pages.page';

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     AdminModel
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		JLoader::register('imageFolderHelper', JPATH_PLUGINS . '/fieldtypes/ajaximage/helpers/imagefolder.php');
		$this->imageFolderHelper = new imageFolderHelper('images/pages');

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('page.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * Method to get type data for the current type
	 *
	 * @param   integer $pk The id of the type.
	 *
	 * @return  mixed object|false
	 *
	 * @since  1.0.0
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('page.id');
		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true)
					->select('p.*')
					->from('#__pages AS p')
					->where('p.id = ' . (int) $pk);

				$db->setQuery($query);
				$data = $db->loadObject();

				if (empty($data))
				{
					return JError::raiseError(404, Text::_('COM_PAGES_ERROR_PAGE_NOT_FOUND'));
				}

				// Link
				$data->link = Route::_(PagesHelperRoute::getPageRoute($data->id));

				$data->imageFolder = $this->imageFolderHelper->getItemImageFolder($data->id);
				$data->header      = (!empty($data->header) && JFile::exists(JPATH_ROOT . '/' . $data->header)) ?
					Uri::root(true) . '/' . $data->header : false;

				// Convert the images field to an array.
				$registry     = new Registry($data->images);
				$data->images = $registry->toArray();

				// Prepare content
				$data->content = str_replace('{id}', $data->id, $data->content);
				$data->content = str_replace('{title}', $data->title, $data->content);
				$data->content = str_replace('{imageFolder}', $data->imageFolder . '/content', $data->content);

				// Convert the metadata field
				$data->metadata = new Registry($data->metadata);

				// Convert the css field
				$data->css = new Registry($data->css);

				// Convert the js field
				$data->js = new Registry($data->js);

				// Get Tags
				$data->tags = new TagsHelper;
				$data->tags->getItemTags('com_pages.page', $data->id);

				// Convert parameter fields to objects.
				$registry     = new Registry($data->attribs);
				$data->params = clone $this->getState('params');
				$data->params->merge($registry);


				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param   integer $pk Optional primary key of the article to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 *
	 * @since  1.0.0
	 */
	public function hit($pk = 0)
	{
		$app      = Factory::getApplication();
		$hitcount = $app->input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('page.id');

			$table = Table::getInstance('Pages', 'PagesTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}
}