    function pbProjectAddFile(e){
        jQuery("#"+e.id+"Name").val( e.files[0].name );
    }

    function imcDeleteAttachedFile( id ){
        document.getElementById(id).value = "";

        jQuery("#"+id+"Name").html("");
        jQuery("#"+id+"Name").val("");
        jQuery("#"+id+"Link").hide();
    }
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
    
    jQuery( setSubmitBtnLabel );
