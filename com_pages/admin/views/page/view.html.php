<?php
/**
 * @package    Pages Component
 * @version    1.0.2
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\SiteApplication;

class PagesViewPage extends HtmlView
{
	/**
	 * The JForm object
	 *
	 * @var  JForm
	 *
	 * @since  1.0.0
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 *
	 * @since  1.0.0
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  object
	 *
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var  JObject
	 *
	 * @since  1.0.0
	 */
	protected $canDo;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise an Error object.
	 *
	 * @throws Exception
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the type title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);
		$canDo = PagesHelper::getActions('com_pages', 'page', $this->item->id);

		if ($isNew)
		{
			// Add title
			JToolBarHelper::title(
				TEXT::_('COM_PAGES') . ': ' . TEXT::_('COM_PAGES_PAGE_ADD'), 'list'
			);
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::apply('page.apply');
				JToolbarHelper::save('page.save');
				JToolbarHelper::save2new('page.save2new');
			}
		}
		// Edit
		else
		{
			// Add title
			JToolBarHelper::title(
				TEXT::_('COM_PAGES') . ': ' . TEXT::_('COM_PAGES_PAGE_EDIT'), 'list'
			);
			// Can't save the record if it's and editable
			if ($canDo->get('core.edit'))
			{
				JToolbarHelper::apply('page.apply');
				JToolbarHelper::save('page.save');
				JToolbarHelper::save2new('page.save2new');
			}

			// Go to page
			JLoader::register('PagesHelperRoute', JPATH_SITE . '/components/com_pages/helpers/route.php');
			$siteRouter = SiteApplication::getRouter();
			$pageLink   = $siteRouter->build(PagesHelperRoute::getPageRoute($this->item->id))->toString();
			$pageLink   = str_replace('administrator/', '', $pageLink);
			$toolbar    = JToolBar::getInstance('toolbar');
			$toolbar->appendButton('Custom', '<a href="' . $pageLink . '" class="btn btn-small btn-primary"
					target="_blank">' . Text::_('COM_PAGES_PAGE_GO_TO') . '</a>', 'goTo');
		}

		JToolbarHelper::cancel('page.cancel', 'JTOOLBAR_CLOSE');
		JToolbarHelper::divider();
	}
}