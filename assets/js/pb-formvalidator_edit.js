(function() {
    /*Google Maps API*/
    google.maps.event.addDomListener(window, 'load', imcInitMap);

    jQuery( document ).ready(function() {

        var validator = new FormValidator('report_an_issue_form', <?PHP
          echo $project_single->render_fields_js_validation();
          ?>, function(errors, events) {
          jQuery('label.imc-ReportFormErrorLabelStyle').html("");
          if (errors.length > 0) {
              var i, j;
              var errorLength;
              jQuery("#imcReportFormSubmitErrors").html("");
              jQuery('#postTitleLabel').html();

              for (i = 0, errorLength = errors.length; i < errorLength; i++) {
                  if (errors[i].name === "featured_image") {
                      imcDeleteAttachedImage('imcReportAddImgInput');
                      jQuery("#imcReportFormSubmitErrors").html(errors[i].message);
                  } else {
                      for(j=0; j < Math.min(1, errors[i].messages.length); j++) {
                        jQuery('#'+errors[i].id+'Label').html(errors[i].messages[j]);
                        jQuery("#imcReportFormSubmitErrors").append("<p>"+errors[i].message+"</p>");
                      }
                  }
              }
          } else {
              jQuery('#imcEditIssueSubmitBtn').attr('disabled', 'disabled'); // rozdil - id tlacitka
              jQuery('label.imc-ReportFormErrorLabelStyle').html();
          }
        });
        validator.registerConditional( 'pb_project_js_validate_required', function(field){
          /* povinna pole se validuji pouze pokud narhovatel zaskrtne odeslat k vyhodnoceni
           plati pro pole s pravidlem "depends" */
          return jQuery('#pb_project_edit_completed').prop('checked');
        });
        validator.registerCallback( 'pb_project_js_validate_budget', function(value){
          var result = false;
          var pom = Array.from(JSON.parse( value ));
          if( Array.isArray(pom)) {
              if ( pom.length > 0) {
                var total = Math.round(calculate_total_sum()*1.1);
                if (total >=350000 && total <= 2000000) {
                  result = true;
                }
              }
          };
          return result;
        }).setMessage('pb_project_js_validate_budget', 'Celková částka předpokládaných nákladů včetně rezervy musí být mezi 350tis až 2mil Kč.');;

        validator.registerCallback( 'pb_project_js_validate_locality', function(value){
          var result = false;
          var pom = Array.from(JSON.parse( value ));
          if( Array.isArray(pom)) {
              if ( pom.length > 0) {
                result = true;
              }
          };
          return result;
        }).setMessage('pb_project_js_validate_locality', 'Vyberte alespoň jednu lokalitu, které se návrh týká.');
        validator.setMessage( 'required', 'Pole %s je povinné.');
        validator.setMessage( 'min_length', 'Délka pole %s je minimálně %s znaků.');
        validator.setMessage( 'max_length', 'Délka pole %s je maximálně %s znaků.');
        validator.setMessage( 'valid_email', 'Pole %s neobsahuje platnou emailovou adresu.');
        validator.setMessage( 'valid_phone', 'Pole %s neobsahuje platné telefonní číslo.');
        re_save_hidden_locality();  //rozdil - tohle v inc neni
    });
})();

function imcInitMap() {
    "use strict";
    var mapId = "imcReportIssueMapCanvas";
    // Checking the current latlng of the issue
    var lat = parseFloat('<?php echo floatval($issue_lat); ?>');
    var lng = parseFloat('<?php echo floatval($issue_lng); ?>');
////rozdil - jinak nastavena hodnovta
    var allowScroll;
    "<?php echo intval($map_options_initial_mscroll, 10); ?>" === '1' ? allowScroll = true : allowScroll = false;
    var boundaries = <?php echo json_encode($map_options_initial_bound);?> ?
    <?php echo json_encode($map_options_initial_bound);?>: null;

    imcInitializeMap(lat, lng, mapId, 'imcAddress', true, 15, allowScroll, JSON.parse(boundaries));
    imcFindAddress('imcAddress', false, lat, lng);
}

document.getElementById('imcReportAddImgInput').onchange = function (e) {
// rozdil - tento if je nevic oproti INS
    if (document.getElementById('imcPreviousImg')) {
        jQuery('#imcPreviousImg').remove();
    }

    var file = jQuery("#imcReportAddImgInput")[0].files[0];
    // Delete image if "Cancel"
    if (document.getElementById("imcReportAttachedImageThumb")) {
        imcDeleteAttachedImage("imcReportAttachedImageThumb");
    }
    // If image is too big
    // Get filesize
    var maxFileSize = '<?php echo imc_file_upload_max_size(); ?>'; //rozdil - vlozeno z INS, MOZNA NENI POTREBA
    if(file && file.size < 2097152) { // 2 MB (this size is in bytes)
        loadImage.parseMetaData(file, function(data) { //read image metadata to get orientation info
            var orientation = 0;
            if (data.exif) {
                orientation = data.exif.get('Orientation');
            }
            document.getElementById('imcPhotoOri').value = parseInt(orientation, 10);
            var loadingImage =	loadImage (
                file,
                function (img) {
                    if(img.type === "error") {
                        console.log("Error loading image ");
                        jQuery("#imcReportFormSubmitErrors").html("The Photo field must contain only gif, png, jpg files.").show();
                        if (document.getElementById("imcReportAttachedImageThumb")) {
                            imcDeleteAttachedImage("imcReportAttachedImageThumb");
                        }
                    } else {
                        if (document.getElementById("imcReportAttachedImageThumb")) {
                            imcDeleteAttachedImage("imcReportAttachedImageThumb");
                        }
                        img.setAttribute("id", "imcReportAttachedImageThumb");
                        img.setAttribute("alt", "Attached photo");
                        img.setAttribute("class", "imc-ReportAttachedImgStyle u-cf");
                        document.getElementById('imcImageSection').appendChild(img);
                        jQuery("#imcReportFormSubmitErrors").html("");
                        jQuery("#imcNoPhotoAttachedLabel").hide();
                        jQuery("#imcLargePhotoAttachedLabel").hide();
                        jQuery("#imcPhotoAttachedFilename").html(" " + file.name);
                        jQuery("#imcPhotoAttachedLabel").show();
                        document.getElementById('imcImgScenario').value = "2"; //rozdil - tohle je navic
                    }
                },
                {
                    maxHeight: 200,
                    orientation: orientation,
                    canvas: true
                }
            );
        });

    } else {
        // imcDeleteAttachedImage('imcReportAddImgInput'); // rozdil -prodano z INS
        e.preventDefault();
        jQuery("#imcNoPhotoAttachedLabel").hide();
        jQuery("#imcPhotoAttachedLabel").hide();
        jQuery("#imcLargePhotoAttachedLabel").show();
    }
};
