    function pbProjectAddFile(e){
        jQuery("#"+e.id+"Name").val( e.files[0].name );
    }
    function pbProjectAttachTblAddFile(e, limit){
        if (! limit) { limit = 2000000};
        var file_types = []
        jQuery("#pbvote-error-message-size").attr("hidden","hidden");
        jQuery("#pbvote-error-message-type").attr("hidden","hidden");
        var no_error = true;
        if (e.files[0].size > limit) {
          jQuery("#pbvote-error-message-size").removeAttr("hidden");
          no_error = false;
        }
        if ( ! pbProjectAttachTblCHeckFIleType(e.files[0].name)) {
          jQuery("#pbvote-error-message-type").removeAttr("hidden");
          no_error = false;
        }

        if (no_error) {
          jQuery("#"+e.id+"Name").val( e.files[0].name );
        }
         else {
           jQuery("#"+e.id+"Name").val();
           jQuery("#"+e.id).val();
        }
    }
    function pbProjectAttachTblCHeckFIleType(filename)
    {
      var parts = filename.toLowerCase().split('.');
      var ext = parts[parts.length - 1];
       switch (ext) {
         case 'gif':
         case 'png':
         case 'jpg':
         case 'jpeg':
         case 'pdf':
           //etc
           return true;
       }
       return false;
    }
    function imcDeleteAttachedFile( id ){
        document.getElementById(id).value = "";

        jQuery("#"+id+"Name").html("");
        jQuery("#"+id+"Name").val("");
        jQuery("#"+id+"Link").hide();
    }
    if (jQuery('#pb_link_to_katastr').length) {
      document.getElementById("pb_link_to_katastr").onclick = function() {
        if (document.getElementById('imcLatValue')) {
          var lat = document.getElementById('imcLatValue').value;
          var lng = document.getElementById('imcLngValue').value;

        } else {
          var lat = document.getElementById('imc_lat').value;
          var lng = document.getElementById('imc_lng').value;
        }
        var url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat="+
          lat+"&lon="+lng+"&layers_3=000B00FFTFFT&ilat="+lat+"&ilon="+lng;
        // document.getElementById('imcLatValue').value+"&lon="+
        // document.getElementById('imcLngValue').value+"&layers_3=000B00FFTFFT&ilat="+document.getElementById('imcLatValue').value+"&ilon="+
        // document.getElementById('imcLngValue').value;
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

    function add_data_to_attachment_list( inputsContainer, id)
    {
      var listElement = jQuery(inputsContainer).closest(".attachment-container").find("input.pbvote-attach-table-hidden-list");

      var list_values = Array.from(JSON.parse(jQuery(listElement).val()));
      list_values.push( id);
      jQuery(listElement).val( JSON.stringify(list_values) );
    }
    function delete_data_from_attachment_list( inputsContainer, id)
    {
      var listElement = jQuery(inputsContainer).closest(".attachment-container").find("input.pbvote-attach-table-hidden-list");
      var list_values = Array.from(JSON.parse(jQuery(listElement).val()));
      var index = -1;
      for (var i = 0; i < list_values.length; i++) {
        if (list_values[i] == id ) {
          index = i;
        }
      }
      if (index > -1) {
        list_values.splice( index, 1);
      }
      jQuery(listElement).val( JSON.stringify(list_values) );
    }
    function set_attach_table_new_row_index(el)
    {
      return "new" + gen_id();
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
      jQuery(clonedFile).removeClass('attach-input-add-mandatory attach-input-add-file pbVoteAttachTblInputFile');
      jQuery(clonedFile).addClass('pbvote-attach-table-file');
      jQuery(clonedFile).attr("id",  "attach_table_file_input_"+new_id);
      jQuery(clonedFile).attr("name","attach_table_file_input_"+new_id);
      return clonedFile;
    }
    function clean_attach_table_inputs( container)
    {
      jQuery(container).find('input.pbVoteAttachTblInputTitle').val("");
      jQuery(container).find('input.pbVoteAttachTblInputFileName').val("");
      jQuery(container).find('input.pbVoteAttachTblInputFile').val("");

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
      delete_data_from_attachment_list(this, delete_value);
      jQuery(this).closest("tr").remove();
    });

    jQuery(document).on("click", "button.attach-add-new", function(){
      if (attach_table_check_mandatory_fied(this)) {
        var new_id = set_attach_table_new_row_index(this);
        var table = jQuery(this).closest(".attachment-container").find("table.pbvote-attach-table");
        var inputsContainer = jQuery(this).closest(".attach-table-new-container");
        var clonedRow = jQuery(table).find("tbody tr:first-child").clone();
        var clonedFile = attach_table_cloneFile(jQuery(inputsContainer).find("input.pbVoteAttachTblInputFile"), new_id);


        clonedRow.find("#attach_table_row_id").val(new_id);
        clonedRow.find("#attach_table_title_input_new00").val(jQuery(inputsContainer).find("input.pbVoteAttachTblInputTitle").val());

        clonedRow.find("#attach_table_title_input_new00").attr("name", "attach_table_title_input_"+new_id);
        clonedRow.find("#attach_table_title_input_new00").attr("id", "attach_table_title_input_"+new_id);
        clonedRow.find("#place_for_file").html(clonedFile);
        clonedRow.find("#attach_table_link_new00").hide();
        clonedRow.removeAttr('hidden');
        jQuery(table).append(clonedRow);
        add_data_to_attachment_list(inputsContainer, new_id);
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

    jQuery('.tab-carusel-element').keydown(function(e){
      var el = jQuery(this);
      var mySibling = jQuery(this).closest('.tab-carusel-container').find('.tab-carusel-element:not([disabled],[hidden])');
      var myIndex = jQuery(mySibling).index(el);
      var first = mySibling[0];
      var last = mySibling.get(-1);
      if (e.which === 9 || e.keyCode === 9){
        if (e.shiftKey) {
          e.preventDefault();
          var prev = mySibling[myIndex - 1];
          if(prev){
            jQuery(prev).focus();
          }else {
            jQuery(last).focus();
          }
        } else {
          e.preventDefault();
          var next = mySibling[myIndex + 1];
          if(next){
            jQuery(next).focus();
          }else {
            jQuery(first).focus();
          }
        }
      }
    });

    re_save_hidden_locality();
});
function gen_id(){
    var dt = new Date().getTime();
    var id = 'xxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (dt + Math.random()*16)%16 | 0;
        dt = Math.floor(dt/16);
        return (c=='x' ? r :(r&0x3|0x8)).toString(16);
    });
    return id;
}
jQuery( setSubmitBtnLabel );
