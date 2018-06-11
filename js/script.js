function BudgetNew() {
  $.get(OC.generateUrl('apps/effectcash/budget_new'), {}, function(budget) {
    FormRender(budget);
  });
}

function BudgetEdit(id) {
  $.get(OC.generateUrl('apps/effectcash/budget_edit'), { id:id }, function(budget) {
    FormRender(budget);
  });
}

function BudgetDateFormat(date) {
  return moment(date).format($('#app-settings-dateformat-moment').val());
}

function FormRender(budget) {
  var effectform = $('#effectcash-form');

  effectform.find("[name='id']").val(budget.id);
  effectform.find("[name='title']").val(budget.title);
  effectform.find("[name='group_title']").val(budget.group_title);
  effectform.find("[name='group_title']")[0].selectize.setValue(budget.group_title);
  effectform.find("[name='repeat']").val(budget.repeat);
  effectform.find("[name='is_income']").val(budget.is_income);
  effectform.find("[name='date']").val(BudgetDateFormat(budget.date));
  effectform.find("[name='amount']").val(budget.amount);
  effectform.find("[name='description']").val(budget.description);

  $('#app-navigation-right').show();
  effectform.find("[name='title']").focus();
}

function FormSubmit() {
  var data = $('#effectcash-form :input').serialize();

  $.ajax({
    type: "POST",
    url: OC.generateUrl('apps/effectcash/budget_submit'),
    data: data,
    success: function() {
      $('#app-navigation-right').hide();
      effectcash.load_budgets();
    }
  });
}

var effectcash = {
  date: null,
  init: function() {
    effectcash.today();
  },
  today: function() {
    effectcash.date = moment();
    effectcash.update_title();
    effectcash.load_budgets();
  },
  next: function() {
    effectcash.date = moment(effectcash.date).add(1, 'M');
    effectcash.update_title();
    effectcash.load_budgets();
  },
  prev: function() {
    effectcash.date = moment(effectcash.date).subtract(1, 'M');
    effectcash.update_title();
    effectcash.load_budgets();
  },
  update_title: function() {
    var weekday = t('effectcash', effectcash.date.format("MMMM"));
    var year = effectcash.date.format("YYYY");
    $('#effectcash-date').html(weekday + ' ' + year);
  },
  load_budgets: function() {
    var start = effectcash.date.startOf('month').format('YYYY-MM-DD');
    var end = effectcash.date.endOf('month').format('YYYY-MM-DD');

    $.get(OC.generateUrl('apps/effectcash/budgets_load'), { start:start, end:end }, function(budgets) {
      list.render('#overview-list', budgets);
    });
  }
}

var list = {
  init: function(list_id) {
    $(list_id).on('click', '.row', function() {
      BudgetEdit($(this).attr('budget-id'));
    });
  },
  render: function(list_id, budgets) {
    var html = '';

    for(i=0; i<budgets.length; i++) {
      var budget = budgets[i];
      var date = BudgetDateFormat(budget.date);
      html += '<div class="row" budget-id="' + budget.id + '"><div><div class="l">' + budget.group_title.charAt(0) + '</div></div><div><div class="t">' + budget.title + '</div><div class="c">' + budget.group_title + '</div><div class="d">' + date + '</div></div><div class="' + (budget.is_income === '1' ? 'i' : 'o')  + '">' + budget.amount + ' €</div></div>';
    }

    $(list_id).html(html);
  }
}

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
      $.get(OC.generateUrl('apps/effectcash/search'), { search:value }, function(budgets) {
        list.render('#search-list', budgets);
        $('#app-content-search').css('opacity', '1');
      });
    }, 500);
    return true;
  }
}

var settings = {
  save: function() {
    var data = $('#effectcash-settings-form :input').serialize();

    $.ajax({
      type: "POST",
      url: OC.generateUrl('apps/effectcash/settings_save'),
      data: data,
      success: function() {
        location.reload();
      }
    });
  }
}




$('#effectcash-new').on('click', function() {
  BudgetNew();
});
$('#effectcash-prev').on('click', function() {
  effectcash.prev();
});
$('#effectcash-next').on('click', function() {
  effectcash.next();
});
$('#effectcash-today').on('click', function() {
  effectcash.today();
});
$('#effectcash-bttn-submit').on('click', function() {
  FormSubmit();
});
$('#effectcash-bttn-cancel').on('click', function() {
  $('#app-navigation-right').hide();
});

var dateformat = $('#app-settings-dateformat-datepicker').val();
$(".datepicker").datepicker({
  dateFormat: dateformat,
  changeMonth: true,
  changeYear: true,
  minDate: '-20y'
});

$('#effectcash-input-search').on('keyup', function() {
  search.find($(this).val());
});

var _groups;

$.get(OC.generateUrl('apps/effectcash/groups_load'), function(groups) {
  _groups = groups;
  var opts = ['<option></option>'];
  for(i=0; i<groups.length; i++) {
    opts.push('<option>' + groups[i] + '</option>');
  }

  $('#effectcash-form [name="group_title"]')
  .html(opts)
  .selectize({
    create: true,
    sortField: 'text'
  });
});

$('#effectcash-settings-bttn-submit').on('click', function() {
  settings.save();
});



effectcash.init();
list.init('#overview-list');
list.init('#search-list');
