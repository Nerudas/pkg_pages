<?php
/**
 * @package    Pages Component
 * @version    1.0.5
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

JLoader::register('PagesHelperRoute', JPATH_SITE . '/components/com_pages/helpers/route.php');

$controller = BaseController::getInstance('Pages');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();