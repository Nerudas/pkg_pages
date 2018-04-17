<?php
/**
 * @package    Pages - Administrator Module
 * @version    1.0.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$user    = Factory::getUser();
$columns = 5;

?>

<table class="table table-striped">
	<thead>
	<tr>
		<th style="min-width:100px" class="nowrap">
			<?php echo Text::_('JGLOBAL_TITLE'); ?>
		</th>
		<th width="1%" class="nowrap hidden-phone">
			<?php echo Text::_('JGLOBAL_HITS'); ?>
		</th>
		<th width="1%" class="nowrap hidden-phone center">
			<?php echo Text::_('JGRID_HEADING_ID'); ?>
		</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<td colspan="<?php echo $columns; ?>" class="center">
		</td>
	</tr>
	<tbody>
	<?php foreach ($items as $item):
		$canEdit = $user->authorise('core.edit', '#__pages.' . $item->id); ?>
		<tr>
			<td>
				<?php if ($canEdit) : ?>
					<a class="hasTooltip" title="<?php echo Text::_('JACTION_EDIT'); ?>"
					   href="<?php echo Route::_('index.php?option=com_pages&task=page.edit&id=' . $item->id); ?>">
						<strong><?php echo $item->title; ?></strong>
					</a>
				<?php else : ?>
					<strong><?php echo $item->title; ?></strong>
				<?php endif; ?>
			</td>
			<td class="hidden-phone center">
					<span class="badge badge-info">
						<?php echo (int) $item->hits; ?>
					</span>
			</td>
			<td class="hidden-phone center">
				<?php echo $item->id; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
