
$('#effectcash-new').on('click', function() {
  budget.new();
});
$('#effectcash-prev').on('click', function() {
  datehandler.prev();
});
$('#effectcash-next').on('click', function() {
  datehandler.next();
});
$('#effectcash-today').on('click', function() {
  datehandler.today();
});

$(".datepicker").datepicker({
  dateFormat: settings.getOptions('dateformat').datepicker,
  changeMonth: true,
  changeYear: true,
  minDate: '-20y',
  altField: '[name="budget_date"]',
  altFormat: 'yy-mm-dd'
});

$('#effectcash-input-search').on('keyup', function() {
  search.find($(this).val());
});

$.get(OC.generateUrl('apps/effectcash/groups_load'), function(groups) {
  var opts = ['<option></option>'];
  for(i=0; i<groups.length; i++) {
    opts.push('<option>' + groups[i] + '</option>');
  }

  $('#budgetFrm [name="group_title"]')
  .html(opts)
  .selectize({
    create: true,
    sortField: 'text'
  });
});

setSettingsToForm();
datehandler.init();
