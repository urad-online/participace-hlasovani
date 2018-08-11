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
console.log(recaptchaResponse);
    var postRequest = {
        'action':       'pbvote_getcode',
        'voter_id':     code,
        'voting_id':    votingId,
        'captcha_response': recaptchaResponse,
    };

    // jQuery('#voting_loader_image').show();

    jQuery("#votingRegistrationCodeError").html("");
    jQuery("#votingRegistrationCodeError").css("display", "none");
    jQuery("body").css("cursor", "progress");

    jQuery.post(ajax_object.ajax_url, postRequest, function(response) {

        var resp = JSON.parse(response);

        // console.log( resp );
        // jQuery('#cgbm_loader_image').hide();
        jQuery("body").css("cursor", "default");
        if (resp.result == 'error') {
            jQuery("#votingRegistrationCodeError").html(resp.message);
            jQuery("#votingRegistrationCodeError").css("display", "block");
        } else {
            jQuery("#votingRegistrationCodeError").html("OK");
            jQuery("#votingRegistrationCodeError").css("display", "none");
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
    // jQuery('#votingGenerateCodeBtn').removeAttr('readonly');
}
function pbvotingDisableBtn()
{
    jQuery('#votingGenerateCodeBtn').prop('disabled', true);
    // jQuery('#votingGenerateCodeBtn').prop('readonly', true);
}
