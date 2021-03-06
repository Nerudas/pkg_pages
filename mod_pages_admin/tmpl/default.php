<?php
/**
 * @package    Pages - Administrator Module
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'media/mod_pages_admin/js/ajax.min.js', array('version' => 'auto'));
HTMLHelper::_('stylesheet', 'media/mod_pages_admin/css/default.min.css', array('version' => 'auto'));

?>
<div data-mod-pages-admin="<?php echo $module->id; ?>">
	<div class="loading">
		<?php echo Text::_('MOD_PAGES_ADMIN_LOADING'); ?>
	</div>
	<div class="result">
		<div class="items"></div>
		<div class="actions ">
			<div class="btn-group">
				<a class="btn"
				   href="<?php echo Route::_('index.php?option=com_pages'); ?>">
					<?php echo Text::_('MOD_PAGES_ADMIN_TO_COMPONENT'); ?>
				</a>
				<a class="btn"
				   data-mod-pages-admin-reload="<?php echo $module->id; ?>"
				   title="<?php echo Text::_('MOD_PAGES_ADMIN_REFRESH'); ?>">
					<i class="icon-loop"></i>
				</a>
			</div>
		</div>
	</div>
</div>