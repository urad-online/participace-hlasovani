    /*Google Maps API*/
    var maxFileSize = 200000;
    var rules       = formValidatorData.rules;
    var budgetLimits = formValidatorData.budgetTable;
    var mapData      = formValidatorData.mapData;
    var maxFileSize  = parseInt(formValidatorData.fileSize,10);

    jQuery( document ).ready(function() {
        google.maps.event.addDomListener(window, 'load', imcInitMap);

        var validator = new FormValidator(
          'report_an_issue_form',
          rules,
          function(errors, events) {
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
                jQuery('#imcInsertIssueSubmitBtn').attr('disabled', 'disabled');
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
                if (total >= budgetLimits.min && total <= budgetLimits.max) {
                  result = true;
                }
              }
          };
          return result;
        }).setMessage('pb_project_js_validate_budget', 'Celková částka předpokládaných nákladů včetně rezervy musí být mezi ' + budgetLimits.help +' Kč.');

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

        document.getElementById('imcReportAddImgInput').onchange = function (e) {
          var file = jQuery("#imcReportAddImgInput")[0].files[0];
          // Delete image if "Cancel"
          if (document.getElementById("imcReportAttachedImageThumb")) {
            imcDeleteAttachedImage("imcReportAttachedImageThumb");
          }
          // If image is too big
          // Get filesize
          if(file && file.size < maxFileSize) {
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
                    jQuery("#imcReportFormSubmitErrors").html("Ilustrační obrázek může obsahovat pouze ssoubory typu gif, png, jpg.").show();
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
            imcDeleteAttachedImage('imcReportAddImgInput');
            e.preventDefault();
            jQuery("#imcNoPhotoAttachedLabel").hide();
            jQuery("#imcPhotoAttachedLabel").hide();
            jQuery("#imcLargePhotoAttachedLabel").show();
          }
        }
    });

function imcInitMap() {
    "use strict";
    var mapId = "imcReportIssueMapCanvas";
    // Checking the saved latlng on settings
    var lat = parseFloat(mapData.initial_lat); // rozdil - 3 radku jso jinak
    var lng = parseFloat(mapData.initial_lng);
    if (lat === '' || lng === '' ) { lat = 40.1349854; lng = 22.0264538; }
    // Options casting if empty
    var zoom = parseInt(mapData.initial_zoom);
    if(!zoom){ zoom = 7; }

    var allowScroll = mapData.initial_scroll;
    var boundaries = mapData.initial_bound;

    imcInitializeMap(lat, lng, mapId, 'imcAddress', true, zoom, allowScroll, boundaries);
    imcFindAddress('imcAddress', false, lat, lng);
}
