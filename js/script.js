// #################################
// Prototypes
// #################################

Number.prototype.formatMoney = function(places, symbol, symbol_ahead, thousand, decimal) {
	places = !isNaN(places = Math.abs(places)) ? places : 2;
	symbol = symbol !== undefined ? symbol : "$";
	thousand = thousand || ",";
	decimal = decimal || ".";
	var number = this,
	    negative = number < 0 ? "-" : "",
	    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
	    j = (j = i.length) > 3 ? j % 3 : 0;
	return (symbol_ahead ? symbol + ' ' : '') + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "") + (symbol_ahead ? '' : ' ' + symbol);
};

// #################################
// BudgetList Formatter
// #################################

function BudgetGroupTitleFormatter(value, row) {
	var chr = value.charAt(0);
	if(chr.length === 0) {
		chr = row.title.charAt(0);
	}
  return `<div class="group-char">` + chr + `</div>`;
}

function BudgetTitleFormatter(value, row) {
  var date_formated = moment(row.date).format(settings.getOptions('dateformat').moment);
  return `
    <div>` + row.title + `</div>
    <div>` + row.group_title + `</div>
    <div>` + date_formated + `</div>`;
}

function BudgetAmountFormatter(value, row) {
	var c = settings.getOptions('currency');
  value = value.formatMoney(c.places, c.symbol, c.symbol_ahead, c.thousand, c.decimal);
  if(row.is_income === "1") {
    return '<div class="positive">' + value + '</div>';
  }
  return '<div class="negative">' + value + '</div>';
}

// #################################
// Init BudgetList
// #################################

var budget_list = $('#budget-list').effectlist({
  url: OC.generateUrl('apps/effectcash/budgets/between'),
	sortBy: 'date',
  cells: [
    { key:'group_title', cls:'group-title', formatter:BudgetGroupTitleFormatter },
    { key:'title', cls:'title', formatter:BudgetTitleFormatter },
    { key:'amount', cls:'amount', formatter:BudgetAmountFormatter }
  ],
  onReceive: function(rows) {
    var sum = 0.0;
    for(i=0; i<rows.length; i++) {
      if(rows[i].is_income === "1") {
        sum += rows[i].amount;
      } else {
        sum -= rows[i].amount;
      }
    }

		var c = settings.getOptions('currency');
	  var sum_nice = sum.formatMoney(c.places, c.symbol, c.symbol_ahead, c.thousand, c.decimal);
    $('#effectcash-sum').html(sum_nice).removeClass('negative').removeClass('positive').addClass((sum > 0 ? 'positive' : 'negative'));
  },
  onClick: function(el, row) {
    budget.edit(row.id);
  }
});

// #################################
// Init BudgetSearchList
// #################################

var budget_search_list = $('#budget-search-list').effectlist({
  url: OC.generateUrl('apps/effectcash/budgets/search'),
	sortBy: 'date',
  cells: [
    { key:'group_title', cls:'group-title', formatter:BudgetGroupTitleFormatter },
    { key:'title', cls:'title', formatter:BudgetTitleFormatter },
    { key:'amount', cls:'amount', formatter:BudgetAmountFormatter }
  ],
  onReceive: function(rows) {
    $('#app-content-search').css('opacity', '1');
  },
  onClick: function(el, row) {
    budget.edit(row.id);
  }
});

// #################################
// Budget
// #################################

var budget = {
  new: function() {
    $.get(OC.generateUrl('apps/effectcash/budgets/new'), {}, function(bget) {
      budget.form_render(bget, true);
    });
  },
  edit: function(id) {
    $.get(OC.generateUrl('apps/effectcash/budgets/'+id), {}, function(bget) {
      budget.form_render(bget, false);
    });
  },
  form_render: function(bget, is_new) {
    var form = $('#budgetFrm');

    var date_preview = moment(bget.date).format(settings.getOptions('dateformat').moment);

    // Insert to fields
    form.find("[name='title']").val(bget.title);
    form.find("[name='group_title']").val(bget.group_title);
    form.find("[name='group_title']")[0].selectize.setValue(bget.group_title);
    form.find("[name='repeat']").val(bget.repeat);
    form.find("[name='is_income']").val(bget.is_income);
    form.find('#budget-form-budget-date-preview').val(date_preview);
    form.find("[name='date']").val(bget.date);
    form.find("[name='amount']").val(bget.amount);
    form.find("[name='description']").val(bget.description);

    // Set buttons
    $('#effectcash-bttn-submit').off('click').on('click', function() {
      if(is_new) {
        budget.create();
      } else {
        budget.update(bget.id);
      }
    });
    $('#effectcash-bttn-cancel').off('click').on('click', function() {
      $('#app-navigation-right').hide();
			$('#app-content').removeClass('sidebar-visible');
    });
    // Show form and select first field
    $('#app-navigation-right').show();
		$('#app-content').addClass('sidebar-visible');
    form.find("[name='title']").focus();
  },
  create: function() {
    $.ajax({
      type: "POST",
      url: OC.generateUrl('apps/effectcash/budgets'),
      data: $('#budgetFrm :input').serialize(),
      success: function() {
        $('#app-navigation-right').hide();
				$('#app-content').removeClass('sidebar-visible');
        budget_list.load(datehandler.list_params());
      }
    });
  },
  update: function(id) {
    $.ajax({
      type: "PUT",
      url: OC.generateUrl('apps/effectcash/budgets/'+id),
      data: $('#budgetFrm :input').serialize(),
      success: function() {
        $('#app-navigation-right').hide();
				$('#app-content').removeClass('sidebar-visible');
        budget_list.load(datehandler.list_params());
      }
    });
  }
};

// #################################
// Datehandler
// #################################

var datehandler = {
  date: null,
  init: function() {
    datehandler.today();
  },
  today: function() {
    datehandler.date = moment();
    datehandler.update_title();
    budget_list.load(datehandler.list_params());
  },
  next: function() {
    datehandler.date = moment(datehandler.date).add(1, 'M');
    datehandler.update_title();
    budget_list.load(datehandler.list_params());
  },
  prev: function() {
    datehandler.date = moment(datehandler.date).subtract(1, 'M');
    datehandler.update_title();
    budget_list.load(datehandler.list_params());
  },
  update_title: function() {
    var weekday = t('effectcash', datehandler.date.format("MMMM"));
    var year = datehandler.date.format("YYYY");
    $('#effectcash-date').html(weekday + ' ' + year);
  },
  list_params: function() {
    return {
      start: datehandler.date.startOf('month').format('YYYY-MM-DD'),
      end: datehandler.date.endOf('month').format('YYYY-MM-DD')
    }
  }
};

// #################################
// Search
// #################################

var search = {
  timeout: null,
  last_value: null,
  find: function(value) {
    // Check if search changed
    if(search.last_value == value) {
      return false;
    }
    search.last_value = value;
    // Clear last timeout
    clearTimeout(search.timeout);
    // Check if string is given
    if(value.length == 0) {
      $('#app-content-search').hide();
      $('#app-content-main').show();
      return false;
    }
    // Search
    $('#app-content-main').hide();
    $('#app-content-search').show().css('opacity', '0.5');
    search.timeout = setTimeout(function() {
      budget_search_list.load({ title:value })
    }, 500);
    return true;
  }
}

// #################################
// Settings
// #################################

	var settings = {
		_settings: null,
		is_init: false,
		input_id: '#effectcash-settings',
		init: function(force) {
			if (!settings.is_init || force) {
				settings._settings = JSON.parse($(settings.input_id).val());
				settings.is_init = true;
			}
		},
		getValue: function(key) {
			settings.init(false);
			return settings._settings.settings[key];
		},
		getOptions: function(key) {
			settings.init(false);
			return settings.getOptionsByKey(key, settings.getValue(key));
		},
		getOptionsByKey: function(key, default_key) {
			return settings._settings.settings_defaults[key][default_key];
		},
		setValue: function(key, value) {
			settings.init(false);
			// SET NEW VALUE & SET JSON
			settings._settings.settings[key] = value;
			$(settings.input_id).val(JSON.stringify(settings._settings));
			// DO AJAX REQUEST
			$.ajax({
				type: "PUT",
				url: OC.generateUrl('apps/effectcash/settings/set_settings'),
				data: { 'settings': settings._settings.settings },
				success: function() {
					location.reload();
				}
			});
		},
		getAllOptions: function(key) {
			return settings._settings.settings_defaults[key];
		}
	};

// #################################
// SettingsForm
// #################################

function setSettingsToForm() {
	var setting_keys = Object.keys(settings._settings.settings_defaults);
	for(ski=0; ski<setting_keys.length; ski++) {
		var setting_key = setting_keys[ski];
		var setting_defaults = settings.getAllOptions(setting_key);
		var setting_default_keys = Object.keys(setting_defaults);

		var options = '';
		for(sdki=0; sdki<setting_default_keys.length; sdki++) {
			var key = setting_default_keys[sdki];
			options += '<option value="' + key + '" ' + (key === settings.getValue(setting_key) ? 'selected="selected"' : '') + '>' + setting_defaults[key].preview + '</option>';
		}

		$('#effectcash-settings-form [name="' + setting_key + '"]').html(options).on('change', function() {
			settings.setValue($(this).attr('name'), $(this).val());
		});
	}
}
