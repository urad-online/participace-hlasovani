var cgbmSelectForm;
var cgbmEditForm;

jQuery(document).ready(function(){
    pbvotingDisableBtn();
});

function ou_test(input,label)
{
    console.log(input + " - "+ label);
    alert(input + " - "+ label);
}
function voting_callAjaxGetCode_A()
{
    console.log("tak jsem tady");
}

function voting_callAjaxGetCode()
{
    var code = jQuery('#votingRegistrationCode').val();
    var votingId = jQuery('#singleHlasovaniVotingId').val();
    var recaptchaResponse = jQuery('#g-recaptcha-response').val();

    var postRequest = {
        'action':       'pbvote_getcode',
        'voter_id':     code,
        'voting_id':    votingId,
        'captcha_response': recaptchaResponse,
    };


    jQuery("#votingRegistrationCodeError").html("");
    jQuery("#votingRegistrationCodeError").css("display", "none");
    jQuery("#votingRegistrationCodeSuccess").html("Registrační kód je odesílán....");
    jQuery("#votingRegistrationCodeSuccess").css("display", "block");
    jQuery("body").css("cursor", "progress");
    grecaptcha.reset();
    jQuery.post(ajax_object.ajax_url, postRequest, function(response) {

        if ( response.indexOf("``") == 0 ) {
            response = response.replace("``", "");
        }
        var resp = JSON.parse(response);

        jQuery("body").css("cursor", "default");
        if (resp.result == 'error') {
            jQuery("#votingRegistrationCodeError").html(resp.message);
            jQuery("#votingRegistrationCodeError").css("display", "block");
            jQuery("#votingRegistrationCodeSuccess").css("display", "none");
        } else {
            jQuery("#votingRegistrationCodeError").html("OK");
            jQuery("#votingRegistrationCodeError").css("display", "none");
            jQuery("#votingRegistrationCodeSuccess").html("Registrační kód byl odeslán.");
            jQuery("#votingRegistrationCodeSuccess").css("display", "block");
        }

        jQuery(document.body).trigger('post-load'); // musi byt po vlozeni obsahu do stranky
    });
}
function getVotingCode()
{
    var postRequest = {
        'action': 'ou_getCgbmModel',
        'id': input,
        'requestType' : requestType,
    };
}
function pbvotingEnableBtn()
{
    jQuery('#votingGenerateCodeBtn').prop('disabled', false);
}
function pbvotingDisableBtn()
{
    jQuery('#votingGenerateCodeBtn').prop('disabled', true);
}
