(function( $ ){
  $.fn.effectlist = function(options){
    var object = $(this);
    var settings = $.extend({
        url: '',
        cells: [],
        sortBy: null,
        // events
        onClick: function(el, row) {},
        onReceive: function(rows) {}
    }, options);
    var row_data = [];

    // Add class and events to list
    object
      .addClass('efli-list')
      .on('click', '.efli-row', function() {
        var efli_data_id = parseInt($(this).attr('efli-data-id'));
        settings.onClick($(this), row_data[efli_data_id]);
      });

    // Add preclass to cells
    for(ci=0; ci<settings.cells.length; ci++) {
      var cell = settings.cells[ci];
      cell['preclass'] = 'class="efli-cell' + (cell['cls'] ? ' ' + cell['cls'] : '') + '"';
    }

    var render = function(data) {
      if(settings.sortBy !== null) {
        data.sort(function(a,b) {return (a[settings.sortBy] > b[settings.sortBy]) ? 1 : ((b[settings.sortBy] > a[settings.sortBy]) ? -1 : 0);} );
      }

      row_data = data;
      var rows_html = '';
      for(di=0; di<data.length; di++) {
        var row = data[di];
        var row_html = '<div class="efli-row" efli-data-id="' + di + '">';
        for(ci=0; ci<settings.cells.length; ci++) {
          var cell = settings.cells[ci];
          var cell_value = row[cell['key']];
          row_html += '<div ' + cell['preclass'] + '>' + (cell['formatter'] ? cell['formatter'](cell_value, row) : cell_value) + '</div>';
        }
        row_html += '</div>'
        rows_html += row_html;
      }
      object.html(rows_html);
    }

    return {
      load: function(params) {
        $.get(settings.url, params, function(data) {
          settings.onReceive(data);
          render(data);
        });
      },
      anotherAction: function(){
      }
    };
  }
})(jQuery);
