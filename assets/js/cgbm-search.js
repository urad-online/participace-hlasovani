function cgbmModelSelectItem( options)
{
    this.maxListSize       = options.maxListSize;
    this.singleSelect      = false;
    this.ajaxType          = options.ajax_call.dataType;
    this.ajaxDataUrl       = options.ajax_call.url;
    this.ajaxDataType      = options.ajax_call.dataType;
    this.ajaxDataRequest   = options.ajax_call.request;
    this.fullList          = new Array();
    this.sourceItemList    = new Array();
    this.targetItemList    = new Array();
    this.searchStringInput = "#" + options.search_str_input;
    this.searchStringBtn   = "#" + options.search_str_btn;
    this.btnMove           = "#" + options.btn_move;
    this.btnSubmit         = "#" + options.btn_submit;
    this.searchTypeSelect  = "#" + options.search_type_select;
    this.sourceSelect      = new ou_selectList( options.source_select );
    this.targetSelect      = new ou_selectList( options.target_select );
    this.setFormListeners();
}
cgbmModelSelectItem.prototype.setFormListeners = function()
{
    var myThis = this;
    jQuery(document).on('click dblclick', myThis.searchStringBtn  , function(){myThis.getCgbmList() });
    jQuery(document).on('click dblclick', myThis.btnMove , function(){myThis.moveSelectedToTarget() });
    jQuery(document).on('click dblclick', myThis.btnSubmit , function(){myThis.sendTargetOptions() });
}
cgbmModelSelectItem.prototype.getCgbmList = function()
{
    var needle = jQuery(this.searchStringInput).val();
    var searchType = jQuery( this.searchTypeSelect).find("option:selected").val();
    if ( this.fullList.length == 0) {
            this.getDataCallServer( needle, searchType );
    } else {
        this.filterCgbmList(needle, searchType );
        this.sourceSelectAppend();
    }
}

cgbmModelSelectItem.prototype.getDataCallServer = function( needle,  searchType )
{
    var myThis = this;
    var ajaxRequest = {
        type: 'POST',
        url:  this.ajaxDataUrl,
        data: {
            action: this.ajaxDataRequest,
            include_draft: 0,
        },
        dataType: this.ajaxDataType,
    };

    jQuery("body").css("cursor", "progress");
    jQuery.ajax( ajaxRequest ).done( function(response, status) {
        if ( status === "success") {
            // var data = JSON.parse(response);

            myThis.fullList = response ;
            myThis.filterCgbmList(needle, searchType);
            myThis.sourceSelectAppend();
        }

        jQuery("body").css("cursor", "default");

    });
}

cgbmModelSelectItem.prototype.filterCgbmList = function( needle, searchType )
{
    this.sourceItemList = new Array();
    needle = needle.toLowerCase();
    for (var i = 0; i < this.fullList.length; i++) {
        var text = this.fullList[i].post_title.toLowerCase();
        var by_type = true;
        if (( searchType > "0") && ( searchType != this.fullList[i].term_id )) {
            by_type = false;
        }
        if (text.includes( needle) && by_type) {
            this.sourceItemList.push( this.fullList[i] );
        }
        if ( this.sourceItemList.length > this.maxListSize ) {
            this.sourceItemList.push({
                post_title: "... další záznamy (nad limit "+this.maxListSize+")",
                ID: "0",
                post_status: "",
                term_name: ""
            });
            break;
        }
    }
}

cgbmModelSelectItem.prototype.sourceSelectAppend = function()
{
    jQuery( this.sourceSelect.select +" > option[value=0]").remove();
    jQuery( this.sourceSelect.select).append( this.createOptionList() );
}

cgbmModelSelectItem.prototype.createOptionList = function()
{
    var output = new Array();
    var item, exist ;
    for (var i = 0; i < this.sourceItemList.length; i++) {
        exist = jQuery( this.sourceSelect.select ).find('option[value=' + this.sourceItemList[i].ID + ']' ).length ;
        if ( exist === 0 ) {
            var label = this.sourceItemList[i].post_title;
            if ( this.sourceItemList[i].term_name !== "" ) {
                label += " ( " + this.sourceItemList[i].term_name + " )";
            }
            item = new Option( label );
            item.value = this.sourceItemList[i].ID;
            output.push( item );
        }
    }
    return output;
}

cgbmModelSelectItem.prototype.moveSelectedToTarget = function()
{
    var myThis = this;
    var exist ;
    jQuery( this.sourceSelect.select + "  > option:selected").each(function() {
        exist = jQuery( myThis.targetSelect.select ).find('option[value=' + this.value + ']' ).length ;
        if ((this.value != "0") && ( exist === 0)) {
            var item = new Option( this.text);
            item.value = this.value;
            jQuery( myThis.targetSelect.select).append( item );
        }
        jQuery( this).remove();
    });
}

cgbmModelSelectItem.prototype.sendTargetOptions = function( )
{
    var submittedOptions = new Array();
    var targetElement = jQuery( this.btnSubmit).attr("target-element").split(",");
    var elementAppendTo = "#" + targetElement[0] ;
    var itemFormatFun = window[ targetElement[1] ];
    var items = "";

    jQuery( elementAppendTo ).children().remove();

    jQuery( this.targetSelect.select + "  > option").each(function() {
        var exist = jQuery( elementAppendTo ).find(  '[value=' + this.value + ']' ).length ;
        if ( exist === 0 ) {
            items += itemFormatFun( { value: this.value, text: jQuery(this).text()} );
        }
    });

    jQuery( elementAppendTo ).append( items);

    jQuery( this.targetSelect.select + "  > option").remove();
}

cgbmModelSelectItem.prototype.getTargetOptions = function( getFromElement )
{
    myThis = this;
    jQuery( myThis.targetSelect.select).children().remove();
    jQuery( '#' + getFromElement ). children().each( function() {
        var item = new Option( jQuery(this).text());
        item.value = jQuery(this).val();
        jQuery( myThis.targetSelect.select).append( item );
    });
}

function ou_selectList( ElementIds)
{
    this.select = "#"+ElementIds.select;
    this.btnSelect = "#"+ElementIds.btn_sel;
    this.btnUnSelect = "#"+ElementIds.btn_unsel;
    this.btnRemove = "#"+ElementIds.btn_del;
    this.setFormListeners();
    return this;
}
ou_selectList.prototype.setFormListeners = function()
{
    var myThis = this;
    jQuery(document).on('click dblclick', this.btnSelect  , function(){myThis.selectAllToggle( true )});
    jQuery(document).on('click dblclick', this.btnUnSelect , function(){myThis.selectAllToggle( false )});
    jQuery(document).on('click dblclick', this.btnRemove, function(){myThis.removeSelected()});
}
ou_selectList.prototype.selectAllToggle = function( selected )
{
    jQuery( this.select + "  > option").prop("selected", selected);
    jQuery( this.select).focus();
}

ou_selectList.prototype.removeSelected = function( )
{
    jQuery( this.select + "  > option:selected").each(function() {
        jQuery( this).remove();
    });
    jQuery( this.select + " option:first").prop("selected", true);
    jQuery( this.select ).focus();
}
function cgbm_search_submit(elem)
{
    var element = jQuery('div.cgbm-search-form input[type="button"].cgbm_search_submit');
    jQuery( element).closest('div.cgbm-search-form').toggleClass();
}
function set_submit_button( targetElement, label)
{
    var element = jQuery('div.cgbm-search-form input[type="button"].cgbm_search_submit');
    jQuery( element).attr("target-element", targetElement );
    jQuery( element).val("Vložit " + label);
    cgbmSelectForm.getTargetOptions( targetElement.split(',')[0]);
}
function cgbmSearchFormatTargetItem( item )
{
    return '<li class="cgbm-list-item" value="'+ item.value + '"> <div><a href="javascript:void(0)" class="cgbm-delete-option"  "></a>'+ item.text + '</div></li>';
    // return '<li class="cgbm-list-item" value="'+ item.value + '" >' + item.text + ' <span class="CgbmDeleteOption"> [X]</span></li>';
}
function generate_json( url, action)
{

    var ajaxRequest = {
        type: 'POST',
        url:  url,
        data: {
            action: action,
            include_draft: 0,
        },
    };

    jQuery("#json_file_gen_msg").html("Probíhá generování souboru");
    // return;

    jQuery.ajax( ajaxRequest ).done( function(response, status) {
        var resp = JSON.parse(response);
        if (( status === "success") && ( resp.status === 'OK' )) {
            jQuery("#json_file_gen_msg").html("Generování ukončeno.");
        } else {
            jQuery("#json_file_gen_msg").html("Chyba při generování souboru");
        }
    });
}
