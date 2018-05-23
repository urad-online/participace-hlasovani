var cgbmSelectForm;
var cgbmEditForm;
function cgbmModelEditForm()
{
    this.submitButton = '#cgbm_edit_submit';
    this.deleteButton = '#cgbm_edit_delete';

    this.setFormListeners();
}

cgbmModelEditForm.prototype.setFormListeners = function()
{
    var myThis = this;
    jQuery(document).on('click dblclick', myThis.submitButton  , function(){myThis.saveData(this)});
    jQuery(document).on('click dblclick', myThis.deleteButton  , function(){myThis.deleteItem(this)});
}
cgbmModelEditForm.prototype.deleteItem = function( element)
{
    var post_id = jQuery(element).attr('post_id');
    if ( ! post_id > 0 ) {
        this.showAlert( 'danger', ['Neplatný identifikátor záznamu - 0' ] );
        return;
    }

    this.sendAjaxRequest( "delete" , [{ field_name: 'post_id', data: post_id }] );
}


cgbmModelEditForm.prototype.saveData = function( element)
{
    if ( ! this.validateFormRequired()) {
        this.showAlert( 'danger', ['Zadaná data nejsou kompletní'] );
        return;
    }

    if ( ! this.validateFormUrl()) {
        this.showAlert( 'danger', ['Neplatné URL adresy'] );
        return;
    }


    var form_data = this.collect_data( element);

    this.sendAjaxRequest( "save", form_data );
}

cgbmModelEditForm.prototype.sendAjaxRequest = function( action_type, data )
{
    // alert("submit");

    var myThis = this;

    myThis.hideAlert();

    jQuery("body").css("cursor", "progress");

    var ajaxRequest = {
        type: 'POST',
        url:  cgbmEditFormSaveActionParams.ajax_url ,
        data: {
            action: 'cgbm_edit_form_save_data',
            class: cgbmEditFormSaveActionParams.class,
            action_type: action_type,
            post_status: cgbmEditFormSaveActionParams.post_status,
            form_data: JSON.stringify(data),
        },
    };


    jQuery.ajax( ajaxRequest ).done( function(response, status) {
        var resp = JSON.parse(response);
        if (( status === "success") && ( resp.status === 'OK' )) {
            myThis.hideFormSucces();
        } else {
            if  (typeof resp.data === 'undefined') {
                resp = new Array({data:['Chyba serveru ']});
            }
            myThis.showAlert( 'danger', ['Chyba serveru'] );
        }
        jQuery("body").css("cursor", "default");
    });
}

cgbmModelEditForm.prototype.validateFormRequired = function()
{
    var errorElements = new Array();
    var result;

    jQuery('div.cgbm-edit-form').find('[required]').each( function(){
        switch ( jQuery(this).prop('tagName') ) {
            case "INPUT":
            case "TEXTAREA":
                result = jQuery(this).val().length ;
                break;
            case "UL" :
            case "OL" :
                result = jQuery( this).find("LI").length;
                break;
            case "SELECT":
                jQuery( this).find("option:selected").length;
            default:
                result = 1;
        }
        if ( ! result ) {
            errorElements.push( jQuery(this).attr('id'));
        }
    });

    this.inputSetError( errorElements );
    return ( errorElements.length === 0);
}
cgbmModelEditForm.prototype.validateFormUrl = function()
{
    var errorElements = new Array();
    var r = new RegExp(/(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/);
    // var r = new RegExp(/^(http|https):\/\/[^ "]+$/);
    var result;

    jQuery('div.cgbm-edit-form').find('input[type=url]').each( function(){
        if (( ( jQuery(this).val().length )) && (! r.test(jQuery(this).val() ))) {
            errorElements.push( jQuery(this).attr('id'));
        }
    });

    this.inputSetError( errorElements );
    return ( errorElements.length === 0);
}
cgbmModelEditForm.prototype.showAlert = function( alert_type, message)
{
    var el_alert = '#oucaldera_notices_1';
    var el_alert = '#' + jQuery('.cgbm-edit-form-notice').attr('id');

    if (  Array.isArray(message )) {
        message.toString()
    }

    if ( ! ( typeof jQuery( el_alert ).attr('class') === 'undefined')) {
        var classes = jQuery( el_alert ).attr('class').split(/\s+/);
        var class_remove = "";
        for (var i = 0; i < classes.length; i++) {
            if ( classes[i].match(/alert/) ) {
                class_remove += classes[i] + " ";
            }
        }
        jQuery( el_alert ).removeClass( class_remove );
    }

    jQuery( el_alert ).html( message);

    jQuery( el_alert ).addClass( "alert alert-"+alert_type).show();

    window.scrollTo(0, 0);
}

cgbmModelEditForm.prototype.hideAlert = function( )
{
    var el_alert = jQuery('.cgbm-edit-form-notice').attr('id');

    jQuery( el_alert ).html( "").hide();
    this.inputRemoveError();
}

cgbmModelEditForm.prototype.hideFormSucces = function( message)
{
    if ((typeof message === 'undefined') || ( message.length == 0 )) {
        message = ["Operace úspěšně provedena."];
    }
    this.showAlert( 'success', message);
    jQuery('.cgbm-edit-form-main-content-wrap').hide();

    window.scrollTo(0, 0);
}

cgbmModelEditForm.prototype.inputSetError = function( elements )
{
    this.inputRemoveError();

    for (var i = 0; i < elements.length; i++) {
        jQuery('#'+elements[i]).addClass( 'cgbm-input-error');

        jQuery('#'+elements[i]).closest( 'div.form-group').addClass( 'cgbm-has-error');
    }
}
cgbmModelEditForm.prototype.inputRemoveError = function()
{
        jQuery('.cgbm-input-error').removeClass( 'cgbm-input-error');
        jQuery('.cgbm-has-error').removeClass( 'cgbm-has-error');
}

cgbmModelEditForm.prototype.collect_data = function( element)
{
    var root = jQuery( element).closest(".container.cgbm-edit-form");
    var output = new Array();
    var type = "";

    output.push( {
        field_name: 'cgbm_form_verify_nonce',
        data: jQuery('#cgbm_form_verify_nonce').val()
    });

    jQuery( root ).find("[data-field]").each(function() {
        type = jQuery(this).prop('tagName');
        var item = {
            field_name: jQuery( this).attr("name"),
        };

        switch ( type) {
            case "INPUT":
            case "TEXTAREA":
                item['data'] = jQuery( this).val();
                break;
            case "SELECT":
                var select_options = new Array();
                var options = new Array();

                jQuery( this).find("option").each(function() {
                    options.push( this.value ) ;
                });

                jQuery( this).find("option:selected").each(function() {
                    select_options.push( this.value ) ;
                });
                item['data'] = {selected: select_options, options: options };
                break;
            case "UL" :
            case "OL" :
                var options = new Array();
                jQuery( this).find("LI").each(function() {
                    options.push( this.value ) ;
                });
                item['data'] = { options: options };
                break;
            default:
                item['data'] = jQuery( this).val();
        }
        output.push(item);
    });
    return output;
}

function myOnLoad()
{
    if (typeof cgbmSelectFormIds !== 'undefined') {
        cgbmSelectForm = new cgbmModelSelectItem( cgbmSelectFormIds );
    }
    cgbmEditForm = new cgbmModelEditForm();
}
jQuery( myOnLoad);
