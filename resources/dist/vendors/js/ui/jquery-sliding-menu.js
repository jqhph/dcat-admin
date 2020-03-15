/*
 *
 *	jQuery Sliding Menu Plugin
 *	Mobile app list-style navigation in the browser
 *
 *	Written by Ali Zahid
 *	http://designplox.com/jquery-sliding-menu
 *
 */

(function($)
{
	var usedIds = [];

	$.fn.slidingMenu = function(options)
	{
		var selector = this.selector;
		var rtl = false;
		if($('html').data('textdirection') == "rtl"){
			rtl = true;
		}

		var settings = $.extend(
		{
			dataJSON: false,
			backLabel: 'Back'

		}, options);

		return this.each(function()
		{
			var self = this,
				menu = $(self),
				data;

			if (menu.hasClass('sliding-menu'))
			{
				updateWidth();
				return;
			}

			var menuWidth = menu.outerWidth();


			// Updated menu widh
			//var menuWidth = menu[0].offsetWidth;

			if (settings.dataJSON)
			{
				data = processJSON(settings.dataJSON);
			}
			else
			{
				data = process(menu);
			}

			menu.empty().addClass('sliding-menu');

			var rootPanel;

			if (settings.dataJSON)
			{
				$(data).each(function(index, item)
				{
					var panel = $('<ul></ul>');

					if (item.root)
					{
						rootPanel = '#' + item.id;
					}

					panel.attr('id', item.id);
					panel.addClass('menu-panel');
					panel.width(menuWidth);

					$(item.children).each(function(index, item)
					{
						var link = $('<a></a>');
						link.attr('class', item.styleClass);
						link.attr('href', item.href);
						link.text(item.label);

						var li = $('<li></li>');

						li.append(link);

						panel.append(li);

					});

					menu.append(panel);

				});
			}
			else
			{
				$(data).each(function(index, item)
				{
					var panel = $(item);

					if (panel.hasClass('menu-panel-root'))
					{
						rootPanel = '#' + panel.attr('id');
					}

					panel.width(menuWidth);

					menu.append(item);

				});
			}

			rootPanel = $(rootPanel);
			rootPanel.addClass('menu-panel-root');

			var currentPanel = rootPanel;

			menu.height(rootPanel.height());

			var wrapper = $('<div></div>').addClass('sliding-menu-wrapper').width(data.length * menuWidth);

			menu.wrapInner(wrapper);

			wrapper = $('.sliding-menu-wrapper', menu);

			$('a', self).on('click', function(e)
			{
				var href = $(this).attr('href'),
					label = $(this).text();

				if (wrapper.is(':animated'))
				{
					e.preventDefault();

					return;
				}

				if (href == '#')
				{
					e.preventDefault();
				}
				else if (href.indexOf('#menu-panel') == 0)
				{
					var target = $(href),
						isBack = $(this).hasClass('back'),
						marginLeft,
						marginRight;
					if (rtl === true){
						marginRight = parseInt(wrapper.css('margin-right'));
					}
					else{
						marginLeft = parseInt(wrapper.css('margin-left'));
					}

					// Update menu width on menu toggle
					var menuWidth = menu.width();

					// Update current panel when menu is reset
					if($(this).closest('ul').hasClass('menu-panel-root')){

						currentPanel = rootPanel;
					}

					if (isBack)
					{
						if (href == '#menu-panel-back')
						{
							target = currentPanel.prev();
						}

						if(rtl === true)
							properties = {marginRight: marginRight + menuWidth};
						else
							properties = {marginLeft: marginLeft + menuWidth};
						wrapper.stop(true, true).animate(properties, 'fast');
					}
					else
					{
						target.insertAfter(currentPanel);

						if (settings.backLabel === true)
						{
							$('.back', target).html('<i class="fa fa-arrow-circle-o-left back-in"></i>'+label);
						}
						else
						{
							$('.back', target).text(settings.backLabel);
						}

						if(rtl === true)
							properties = {marginRight: marginRight - menuWidth};
						else
							properties = {marginLeft: marginLeft - menuWidth};
						wrapper.stop(true, true).animate(properties,'fast');
					}

					currentPanel = target;

					menu.stop(true, true).animate(
					{
						height: target.height()

					}, 'fast');

					e.preventDefault();
				}

			});

			return this;

		});

		function process(data)
		{
			var ul = $('ul', data),
				panels = [];

			$(ul).each(function(index, item)
			{
				var panel = $(item),
					handler = panel.prev(),
					id = getNewId();

				if (handler.length == 1)
				{
					handler.addClass('nav-has-children dropdown-item').attr('href', '#menu-panel-' + id);
					handler.append('<i class="ft-arrow-right children-in"></i>')
				}

				panel.attr('id', 'menu-panel-' + id);

				if (index == 0)
				{
					panel.addClass('menu-panel-root');
				}
				else
				{
					panel.addClass('menu-panel');

					var li = $('<li></li>'),
						back = $('<a></a>').addClass('nav-has-parent back primary dropdown-item').attr('href', '#menu-panel-back');

					panel.prepend(back);
				}

				panels.push(item);

			});

			return panels;
		}

		function processJSON(data, parent)
		{
			var root = { id: 'menu-panel-' + getNewId(), children: [], root: (parent ? false : true) },
				panels = [];

			if (parent)
			{
				root.children.push(
				{
					styleClass: 'back',
					href: '#' + parent.id

				});
			}

			$(data).each(function(index, item)
			{
				root.children.push(item);

				if (item.children)
				{
					var panel = processJSON(item.children, root);

					item.href = '#' + panel[0].id;
					item.styleClass = 'nav';

					panels = panels.concat(panel);
				}

			});

			return [root].concat(panels);
		}

		function getNewId()
		{
			var id;

			do
			{
				id = Math.random().toString(36).substring(3, 8);
			}
			while (usedIds.indexOf(id) >= 0);

			usedIds.push(id);

			return id;
		}

		function updateWidth(){

			var wrapper = $('.sliding-menu-wrapper'),
			menuPanels = $('.sliding-menu-wrapper ul');

			if(menuPanels.length){
				setTimeout(function(){
					var menuWidth = $(selector).width();

					// Update wrapper width
					wrapper.width(menuPanels.length * menuWidth);

					menuPanels.each(function(index, item)
					{
						var panel = $(item);

						panel.width(menuWidth);

					});


					wrapper.css('margin-left','');
				}, 300);
			}
		}

	};

} (jQuery));
