var Filter = (function() {

	var state, form, elements;

	var onchange = function() {

		// Reset all state except search
		state = { search: state.search };

		// Checkboxes
		$.each(form.serializeArray(), function(index, obj) {
			if (!state[obj.name]) {
				state[obj.name] = [];
			}
			state[obj.name] = obj.value + '#' + (state[obj.name] || '');
		})

		applyFilter();
	}

	var oninput = function() {

		// Searchbox
		var search = $('#dataset-filter').val();
		if (search && search.length > 0) {
			$('.dataset-filter').html(search);
			state.search = new RegExp(search, 'i');
		} else {
			state.search = null;
		}

		applyFilter();
	}

	var expand = function() {
		$(this).parent().css('height', $(this).prev().position().top + $(this).prev().height());
		$(this).parent().removeClass('filter-collapsed');
	}

	var applyFilter = function() {
		var results = false;

		// Hide datasets that don't match
		elements.each(function(i, elem) {
			if (isMatch(elem)) {
				results = true;
				$(elem).removeClass('hide');
			} else {
				$(elem).addClass('hide');
			}
		});

		// Toggle 'no results' message
		$('.empty').toggleClass('hide', results);
	}

	var isMatch = function(elem) {
		if (state.search && !elem.title.match(state.search) && !elem.description.match(state.search)) {
			return false;
		}

		if (state.language && (!elem.language || state.language.indexOf(elem.language) === -1)) {
			return false;
		}
		if (state.license && (!elem.license || state.license.indexOf(elem.license) === -1)) {
			return false;
		}
		if (state.publisher && (!elem.publisher || state.publisher.indexOf(elem.publisher) === -1)) {
			return false;
		}
		if (state.theme && (!elem.theme || state.theme.indexOf(elem.theme) === -1)) {
			return false;
		}

		return true;
	}

	var init = function() {

		form = $('#filter');
		if (!form) {
			return;
		}
		state = {};

		// Cache and "index" datasets
		elements = $('.dataset');
		elements.each(function(i, elem) {
			var dataset = $(this);
			elem.title = $('.dataset-title', dataset).text();
			elem.description = $('.dataset-description', dataset).text();
			elem.theme = dataset.data('theme');
			elem.language = dataset.data('language');
			elem.publisher = dataset.data('publisher');
			elem.license = dataset.data('license');
		});

		// Set up listeners
		form.on('change', onchange);
		$('#dataset-filter').on('input', oninput);
		$('.filter-btn').on('click', expand);
	}

	return {
		init: init
	}
})();
