/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (index.html)
 **/

$(function () {

  'use strict';

  // jQuery UI sortable for the todo list
	$('.todo-list').sortable({
		placeholder         : 'sort-highlight',
		handle              : '.handle',
		forcePlaceholderSize: true,
		zIndex              : 999999
	});

  /* The todo list plugin */
/*
  $('.todo-list').todoList({
    onCheck  : function () {
      window.console.log($(this), 'The element has been checked');
    },
    onUnCheck: function () {
      window.console.log($(this), 'The element has been unchecked');
    }
  });
*/
});
