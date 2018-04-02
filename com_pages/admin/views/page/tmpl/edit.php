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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$doc = Factory::getDocument();

HTMLHelper::stylesheet('media/com_companies/css/admin-company.min.css', array('version' => 'auto'));

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

$doc->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "page.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');
?>
<form action="<?php echo Route::_('index.php?option=com_pages&view=page&id=' . $this->item->id); ?>"
	  method="post"
	  name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_PAGES_PAGE_CONTENT')); ?>
		<div class="row-fluid adminform">
			<div class="span7">
				<div class="content-field control-group">
					<?php echo $this->form->getInput('content'); ?>
				</div>
				<div class="content-field control-group">
					<?php echo $this->form->getInput('images'); ?>
				</div>
				<?php echo $this->form->renderField('imagefolder'); ?>
			</div>
			<div class="span5">
				<div class="control-group">
					<div class="well">
						<div class="lead">
							<?php echo Text::_('COM_PAGES_PAGE_SHORTCODES'); ?>
						</div>
						<div>
							<div class="row-fluid">
								<div class="span2 text-right"><strong class="text-error">{id}</strong></div>
								<div class="span10"><?php echo Text::_('COM_PAGES_PAGE_SHORTCODES_ID'); ?></div>
							</div>
							<div class="row-fluid">
								<div class="span2 text-right"><strong class="text-error">{imageFolder}</strong></div>
								<div class="span10">
									<?php echo Text::_('COM_PAGES_PAGE_SHORTCODES_TITLE'); ?>
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2 text-right"><strong class="text-error">{title}</strong></div>
								<div class="span10">
									<?php echo Text::_('COM_PAGES_PAGE_SHORTCODES_IMAGEFOLDER'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="extends control-group">
					<div class="lead">
						<?php echo Text::_('COM_PAGES_PAGE_CSS'); ?>
					</div>
					<div class="code-field control-group">
						<?php  echo $this->form->getInput('code', 'css'); ?>
					</div>
					<div class="files-field control-group">
						<?php echo $this->form->getInput('files', 'css'); ?>
					</div>
				</div>
				<div class="extends control-group">
					<div class="lead">
						<?php echo Text::_('COM_PAGES_PAGE_JS'); ?>
					</div>
					<div class="code-field control-group">
						<?php  echo $this->form->getInput('code', 'js'); ?>
					</div>
					<div class="files-field control-group">
						<?php  echo $this->form->getInput('files', 'js'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php
		echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'tags', Text::_('JTAG'));
		echo $this->form->getInput('tags');
		echo HTMLHelper::_('bootstrap.endTab');
		?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo $this->form->renderFieldset('publishingdata'); ?>
			</div>
			<div class="span6">
				<?php echo $this->form->renderFieldset('metadata'); ?>
			</div>
		</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php
		echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'attribs', Text::_('JGLOBAL_FIELDSET_OPTIONS'));
		echo $this->form->renderFieldset('attribs');
		echo HTMLHelper::_('bootstrap.endTab');
		?>

		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>