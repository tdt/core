var Filter = (function() {

	var state, form, elements;

	var setFilter = function() {

		// Build state from scratch
		state = {};

		// Checkboxes
		$.each(form.serializeArray(), function(index, obj) {
			if (!state[obj.name]) {
				state[obj.name] = [];
			}
			state[obj.name] = obj.value + '#' + (state[obj.name] || '');
		})

		// Searchbox
		var search = $('#dataset-filter').val();
		if (search && search.length > 0) {
			$('.dataset-filter').html(search);
			state.search = new RegExp(search, 'i');
		}

		// Set filter
		var results = false;
		elements.each(function() {

			var dataset = $(this);

			// Check if we can find a match
			if (isMatch(dataset)) {
				results = true;
				dataset.removeClass('hide');
			} else {
				dataset.addClass('hide');
			}
		});

		// Show 'no results' message
		$('.empty').toggleClass('hide', results);
	}

	var isMatch = function(dataset) {
		if (state.search && !dataset.data('title').match(state.search) && !dataset.data('description').match(state.search)) {
			return false;
		}

		if (state.language && (!dataset.data('language') || dataset.data('language') && state.language.indexOf(dataset.data('language')) === -1)) {
			return false;
		}
		if (state.license && (!dataset.data('license') || dataset.data('license') && state.license.indexOf(dataset.data('license')) === -1)) {
			return false;
		}
		if (state.publisher && (!dataset.data('publisher') || dataset.data('publisher') && state.publisher.indexOf(dataset.data('publisher')) === -1)) {
			return false;
		}
		if (state.theme && (!dataset.data('theme') || dataset.data('theme') && state.theme.indexOf(dataset.data('theme')) === -1)) {
			return false;
		}

		return true;
	}

	var init = function() {

		form = $('#filter');
		if (!form) {
			return;
		}

		// Cache and "index" datasets
		elements = $('.dataset');
		elements.each(function(i, elem) {
			var dataset = $(this);
			dataset.data('title', $('.dataset-title', dataset).text());
			dataset.data('description', $('.dataset-description', dataset).text());

			// TODO: replace data() in isMatch() by these
			elem.theme = dataset.data('theme');
			elem.language = dataset.data('language');
			elem.publisher = dataset.data('publisher');
			elem.license = dataset.data('license');
		});

		// Set up listeners
		form.on('change', setFilter);
		$('#dataset-filter').on('input', setFilter);
	}

	return {
		init: init
	}
})();
