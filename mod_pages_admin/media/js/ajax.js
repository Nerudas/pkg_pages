/*
 * @package    Pages - Administrator Module
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-mod-pages-admin]').each(function () {
			var block = $(this),
				reload = $(block).find('[data-mod-pages-admin-reload]');
			getItems(block);
			$(reload).on('click', function () {
				getItems(block);
			});
		});

		function getItems(block) {
			var loading = block.find('.loading'),
				items = block.find('.items'),
				result = block.find('.result'),
				module_id = $(block).data('mod-pages-admin');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'index.php?option=com_ajax&module=pages_admin&format=json',
				data: {module_id: module_id},
				beforeSend: function () {
					loading.slideDown(750);
					result.slideUp(750);
					items.html('');
				},
				success: function (response) {
					if (response.success) {
						items.html(response.data);
					}
					else {
						items.html(response.message);
					}
				},
				complete: function () {
					loading.slideUp(750);
					result.slideDown(750);
				}
			});
		}
	});
})(jQuery);