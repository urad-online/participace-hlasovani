var integer_pattern = new RegExp('^[0-9,.]+$');
var vat_values = [];
var type_values = [];
var row_values = [];
var row_edit_index = -1;
var budget_data = [];
var table_def = [];
var value_to_total_sum_index = -1;
jQuery(document).ready(function(){
    // jQuery('[data-toggle="tooltip"]').tooltip();
	// Append table with add row form on add new button click
    jQuery(".add-new").click(function(){
    		jQuery(this).attr("disabled", "disabled");
        jQuery("table.pbvote-budget-table tbody").find(".edit, .delete").attr("disabled", "disabled");
    		var index = jQuery("table.pbvote-budget-table tbody tr:last-child").index();
        var row = render_row_edit();
        jQuery("table.pbvote-budget-table").append(row);
    		jQuery("table.pbvote-budget-table tbody tr").eq(index + 1).find(".add, .edit").toggle();
        // jQuery('[data-toggle="tooltip"]').tooltip();
        row_edit_index = -1;
    });
	// Add row on add button click
  	jQuery(document).on("click", ".add", function(){
    		var empty = false;
    		var input = jQuery(this).closest("tr").find('input[type="text"], select');
        var i = 0;
        input.each(function(){
      			if(!jQuery(this).val()){
        				jQuery(this).addClass("error");
      				empty = true;
      			} else{
                jQuery(this).removeClass("error");
            }
            jQuery(this).parents("td").addClass( table_def[i].class);
            i++;
    		});
    		jQuery(this).closest("tr").find(".error").first().focus();
    		if(!empty){
            i = 0;
            row_values = [];
      			input.each(function(){
      				jQuery(this).parent("td").html(format_value(jQuery(this).val(),table_def[i].class));
              row_values.push(jQuery(this).val());
              i++;
      			});
            udate_data_in_list( row_values, row_edit_index);
      			jQuery(this).closest("td").find(".add, .edit").toggle();
            if (row_edit_index > -1 ) {
              jQuery(this).closest("td").find(".delete, .cancel").toggle();
            }
            jQuery("table.pbvote-budget-table tbody").find(".add, .cancel, .edit, .delete").removeAttr("disabled");
      			jQuery(".add-new").removeAttr("disabled");
    		}
    });
	// Edit row on edit button click
  	jQuery(document).on("click", ".edit", function(){
      jQuery("table.pbvote-budget-table tbody").find(".edit, .delete").attr("disabled", "disabled");
      row_values = [];
      row_edit_index  = return_row_index( jQuery(this).parents("tr") );
      var i=0;
      var pocetsloupcu = jQuery(this).parents("td").siblings("td");
      // jQuery(this).parents("tr").find("td:not(:last-child)").each(function(){
      jQuery(this).parents("td").siblings("td").each(function(){
          row_values.push(jQuery(this).text());
    			// jQuery(this).html('<input type="text" class="form-control" value="' + jQuery(this).text() + '">');
    			jQuery(this).html(render_input( table_def[i].id, table_def[i].input_type, jQuery(this).text(), table_def[i].class, table_def[i].attr));
          jQuery(this).removeClass();
          i++;
  		});
  		jQuery(this).closest("td").find(".add, .edit, .delete, .cancel").toggle();
  		jQuery(".add-new").attr("disabled", "disabled");
    });
  	// Delete row on delete button click
  	jQuery(document).on("click", ".delete", function(){
      row_edit_index  = return_row_index( jQuery(this).closest("tr") );
      jQuery(this).closest("tr").remove();
      delete_data_from_list( row_edit_index );
  		jQuery(".add-new").removeAttr("disabled");
    });
    // Cancel editing and set original values on cancel button click
  	jQuery(document).on("click", ".cancel", function(){
        var input = jQuery(this).closest("tr").find('input[type="text"], select');
        var i = 0;
        input.each(function(){
          jQuery(this).closest("td").addClass( table_def[i].class);
          jQuery(this).closest("td").html(row_values[i]);
          i++;
        });
        jQuery(this).closest("td").find(".add, .edit, .delete, .cancel").toggle();
        jQuery("table.pbvote-budget-table tbody").find(".add, .cancel, .edit, .delete").removeAttr("disabled");
        jQuery(".add-new").removeAttr("disabled");
    });

    jQuery(document).on("change", ".pb-table-input.calculate", function(){
      result = 1;
      jQuery(this).closest("tr").find(".calculate").each(function(){
        result *= jQuery(this).val();
      });
      jQuery(this).closest("tr").find(".result").val(result);
    });
    get_initial_params();
    read_data_to_list();
    // render_rows();
});
function render_rows (){
    var total_sum = 0;
    budget_data.forEach( function(data){
        var new_row = "<tr>";
        for (var i = 0; i < data.length; i++) {
          new_row += '<td class="'+ table_def[i].class+'">' + format_value(data[i], table_def[i].class) + "</td>";
          if (i == value_to_total_sum_index) {
            total_sum += Number( data[i]);
          }
        }
        new_row += gen_action_buttons() + "</tr>";
        jQuery("table").append(new_row);
    });
    jQuery("#total_budget_sum").html( Number(total_sum).toLocaleString());
    jQuery("#budget_data").val( JSON.stringify(budget_data) );
}

function render_row_edit()
{
  var new_row = "<tr>";
  table_def.forEach( function(item){
    new_row += '<td>' + render_input( item.id, item.input_type, "", item.class, item.attr) +'</td>';
  });
  new_row += gen_action_buttons() + "</tr>";
  return new_row;
}

function gen_action_buttons()  {
  var output = '<td>' +
      '<a class="add" title="Add" data-toggle="tooltip"><i class="material-icons">save</i></a>' +
      '<a class="edit" title="Edit" data-toggle="tooltip"><i class="material-icons">edit</i></a>' +
      '<a class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">delete</i></a>' +
      '<a class="cancel" title="Cancel" data-toggle="tooltip"><i class="material-icons">cancel</i></a>' +
      '</td>';
  return output;
}

function render_select( list_values, id, value = "", input_class, attr )
{
  var selected = "";
  var output = '<select class="form-control pb-table-input '+input_class+'" name="'+id+'" id="'+ id +'"'+ attr +'>';
  list_values.forEach(function(item){
    if (item == value ) {
      selected = "selected";
    } else {
      selected = "";
    }
      output += '<option ' + selected + ' value="'+item+'">'+item+'</option>';
  });
  output += '</select>';
  return output;
}

function render_input( id, type, value, input_class, attr)
{
  if (integer_pattern.test(value)) {
    value = value.replace(/,|\./gi,'');
  }
  var output = "";
  switch(type) {
      case "select_vat":
        output = render_select( vat_values , id, value, input_class, attr);
        break;
      case "select_type":
        output = render_select( type_values , id, value, input_class, attr);
        break;
      default:
        output = '<input type="text" class="form-control pb-table-input '+input_class+'" value="' + value + '"'+ attr +'>';

  }
  return output;
}

function return_row_index( element)
{
  var parent = jQuery(element).parents("tbody.pbvote-budget-table-body").find('tr');
  var parent_array = Array.from(parent);
  const index = Array.from(parent).indexOf(element[0]);
  return index;
}

function format_value( value, format = "text")
{
  var output = value;
  if (format.includes("integer")) {
    output = Number(value).toLocaleString();
  }
  return output;
}

function udate_data_in_list( values, row_index)
{
  if (row_index == -1) {
    budget_data.push( values);
  } else {
    budget_data[row_index] = values;
  }
  jQuery("#total_budget_sum").html( calculate_total_sum().toLocaleString());
  jQuery("#pb_project_naklady").val( JSON.stringify(budget_data) );
}

function delete_data_from_list( row_index)
{
  budget_data.splice( row_index, 1);
  jQuery("#total_budget_sum").html( calculate_total_sum().toLocaleString());
  jQuery("#pb_project_naklady").val( JSON.stringify(budget_data) );
}

function read_data_to_list()
{
  var pom = jQuery("#pb_project_naklady").val();
  if (pom) {
    budget_data = Array.from(JSON.parse( pom ));
  }
}

function get_initial_params()
{
  // vat_values = Array.from( pbTableListsData.vat);
  type_values = Array.from( pbTableListsData.types);
  table_def = Array.from( pbTableListsData.table_def);
  budget_data = Array.from( pbTableListsData.budget_data);
  jQuery("#pb_project_naklady").val( JSON.stringify(budget_data) );
  set_value_to_total_sum_index();
}

function set_value_to_total_sum_index()
{
  for (var i = 0; i < table_def.length; i++) {
    if ( table_def[i].class.includes("result")) {
      value_to_total_sum_index = i;
      break;
    }
  }
}

function calculate_total_sum()
{
  var sum = 0;
  if (value_to_total_sum_index > -1) {
    budget_data.forEach(function(item){
      sum += Number( item[value_to_total_sum_index]);
    })
  }
  return sum;
}
