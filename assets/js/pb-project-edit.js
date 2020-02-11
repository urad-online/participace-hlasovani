    function pbProjectAddFile(e){
        jQuery("#"+e.id+"Name").val( e.files[0].name );
    }
    function pbProjectAttachTblAddFile(e){
        jQuery("#"+e.id+"Name").val( e.files[0].name );
    }

    function imcDeleteAttachedFile( id ){
        document.getElementById(id).value = "";

        jQuery("#"+id+"Name").html("");
        jQuery("#"+id+"Name").val("");
        jQuery("#"+id+"Link").hide();
    }
    if (jQuery('#pb_link_to_katastr').length) {
      document.getElementById("pb_link_to_katastr").onclick = function() {
        var lt = document.getElementById('imcLatValue').value;
        var url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat="+
        document.getElementById('imcLatValue').value+"&lon="+
        document.getElementById('imcLngValue').value+"&layers_3=000B00FFTFFT&ilat="+document.getElementById('imcLatValue').value+"&ilon="+
        document.getElementById('imcLngValue').value;
        var win = window.open( url, '_blank');
        win.focus();
        return false;
      };
    };

    jQuery("#pb_project_edit_completed").change( function(){
        setSubmitBtnLabel();
    });
    function setSubmitBtnLabel (){
        if (jQuery("#pb_project_edit_completed").prop("checked")) {
            jQuery(".pb-project-submit-btn").val( pbFormInitialData.completed_on );
        } else {
            jQuery(".pb-project-submit-btn").val( pbFormInitialData.completed_off );
        }
    };

    function attach_table_check_mandatory_fied(e)
    {
      var no_error=true;

      jQuery(e).closest(".attach-table-new-container").find("input.attach-input-add-mandatory").each(function(){
        if (!jQuery(this).val()) {
          no_error=false;
        };
      });
      return no_error;
    };

    function add_data_to_attachment_list( id)
    {
      var list_values = Array.from(JSON.parse(jQuery("#pb_project_attachment").val()));
      list_values.push( id);
      jQuery("#pb_project_attachment").val( JSON.stringify(list_values) );
    }
    function delete_data_from_attachment_list( id)
    {
      var list_values = Array.from(JSON.parse(jQuery("#pb_project_attachment").val()));
      var index = -1;
      for (var i = 0; i < list_values.length; i++) {
        if (list_values[i] == id ) {
          index = i;
        }
      }
      if (index > -1) {
        list_values.splice( index, 1);
      }
      jQuery("#pb_project_attachment").val( JSON.stringify(list_values) );
    }
    function set_attach_table_new_row_index(el)
    {
      var table = jQuery(el).closest(".attachment-container").find("table.pbvote-attach-table");
      var last_id = jQuery(table).find("tbody tr:last-child").find('#attach_table_row_id').val().replace("new", "");
      var last_id_num = parseInt( last_id, 10);
      var new_id_num = 1;
      if (Number.isInteger(last_id_num)) {
        new_id_num = last_id_num + 1;
      }
      var new_id = "new" + new_id_num;
      return new_id;
    }
    function attach_table_cloneFile( fileToClone, new_id )
    {
      var clonedFile = fileToClone.clone();
      jQuery(clonedFile).removeClass('attach-input-add-mandatory attach-input-add-file');
      jQuery(clonedFile).addClass('pbvote-attach-table-file');
      jQuery(clonedFile).attr("id",  "attach_table_file_input_"+new_id);
      jQuery(clonedFile).attr("name","attach_table_file_input_"+new_id);
      return clonedFile;
    }
    function clean_attach_table_inputs( container)
    {
      jQuery(container).find('#pbVoteAttachTblInputTitle').val("");
      jQuery(container).find('#pbVoteAttachTblInputFileName').val("");
      jQuery(container).find('#pbVoteAttachTblInputFile').val("");

    }
    function re_save_hidden_locality()
    {
      var result = [];
      var el_to_store_value = jQuery("div.pbvote-CheckboxGroup-container").find('input[type="hidden"]').attr('id');
      if (el_to_store_value) {
        jQuery("#"+el_to_store_value).closest("div.pbvote-CheckboxGroup-container").find(".pbvote-CheckboxGroup-member:checked").each(function(){
          result.push(jQuery(this).attr('id'));
        });
        jQuery("#"+el_to_store_value).val( JSON.stringify(result) );
      }
    }
jQuery(document).ready(function(){
    jQuery(document).on("click", "a.attach-delete", function(){
      var delete_value = jQuery(this).closest("tr").find('#attach_table_row_id').val();
      delete_data_from_attachment_list(delete_value);
      jQuery(this).closest("tr").remove();
    });

    jQuery(document).on("click", "button.attach-add-new", function(){
      if (attach_table_check_mandatory_fied(this)) {
        var new_id = set_attach_table_new_row_index(this);
        var table = jQuery(this).closest(".attachment-container").find("table.pbvote-attach-table");
        var inputsContainer = jQuery(this).closest(".attach-table-new-container");
        var clonedRow = jQuery(table).find("tbody tr:first-child").clone();
        var clonedFile = attach_table_cloneFile(jQuery(inputsContainer).find("#pbVoteAttachTblInputFile"), new_id);


        clonedRow.find("#attach_table_row_id").val(new_id);
        clonedRow.find("#attach_table_title_input_new00").val(jQuery(inputsContainer).find("#pbVoteAttachTblInputTitle").val());

        clonedRow.find("#attach_table_title_input_new00").attr("name", "attach_table_title_input_"+new_id);
        clonedRow.find("#attach_table_title_input_new00").attr("id", "attach_table_title_input_"+new_id);
        clonedRow.find("#place_for_file").html(clonedFile);
        clonedRow.find("#attach_table_link_new00").hide();
        clonedRow.removeAttr('hidden');
        jQuery(table).append(clonedRow);
        add_data_to_attachment_list(new_id);
        clean_attach_table_inputs( inputsContainer);
      };
    });

    jQuery(document).on("change", ".attach-input-add-mandatory", function(){
      var no_error=attach_table_check_mandatory_fied(this);

      if (no_error) {
        jQuery(this).closest("div.attach-table-new-container").find('.attach-add-new').removeAttr("disabled");
      } else {
        jQuery(this).closest("div.attach-table-new-container").find('.attach-add-new').attr("disabled", "disabled");
      }

    });

    jQuery(document).on("change", ".pbvote-CheckboxGroup-member", function(){
      var result = [];
      var el_to_store_value = jQuery(this).closest("div.pbvote-CheckboxGroup-container").find('input[type="hidden"]').attr('id');
      jQuery(this).closest("div.pbvote-CheckboxGroup-container").find(".pbvote-CheckboxGroup-member:checked").each(function(){
        result.push(jQuery(this).attr('id'));
      });
      jQuery("#"+el_to_store_value).val( JSON.stringify(result) );
    });
    re_save_hidden_locality();
});

jQuery( setSubmitBtnLabel );
