/**
 * @since 1.0.0
 * @requires jQuery
 * @output assets/js/pb-voting.js
 */
var waitingForResponse = false;

jQuery(document).ready(function(){
    waitingForResponse = false;
    pbvotingDisableBtn();
    jQuery("#votingRegistrationCode").focus();
});

function ou_test(input,label)
{
    console.log(input + " - "+ label);
    alert(input + " - "+ label);
}

function voting_callAjaxGetCode()
{
    pbvotingDisableBtn();
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
    jQuery("#votingRegistrationCodeSuccess").html("Čekáme na potvrzení o doručení odeslané SMS vaším operátorem (max. 1 min.).  Již přišla? Můžete pokračovat tlačítkem 'Kód již mám'.");
    jQuery("#votingRegistrationCodeSuccess").css("display", "block");
    jQuery("body").css("cursor", "progress");
    waitingForResponse = true;
    jQuery.post(ajax_object.ajax_url, postRequest, function(response) {

        if ( response.indexOf("``") == 0 ) {
            response = response.replace("``", "");
        }
        var resp = JSON.parse(response);

        jQuery("body").css("cursor", "default");
        waitingForResponse = false;
        if (resp.result == 'error') {
            jQuery("#votingRegistrationCodeError").html(resp.message);
            jQuery("#votingRegistrationCodeError").css("display", "block");
            jQuery("#votingRegistrationCodeSuccess").css("display", "none");
            grecaptcha.reset();
        } else {
            jQuery("#votingRegistrationCodeError").html("OK");
            jQuery("#votingRegistrationCodeError").css("display", "none");
            jQuery("#votingRegistrationCodeSuccess").html("SMS s přístupovým kódem byla odeslána. Zadejte přístupový kód");
            jQuery("#votingRegistrationCodeSuccess").css("display", "block");
            votingSwitchToTokenEntry();
        }

        // jQuery(document.body).trigger('post-load'); // musi byt po vlozeni obsahu do stranky
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
    if (! waitingForResponse) {
      jQuery('#votingGenerateCodeBtn').prop('disabled', false);
    }
}
function pbvotingDisableBtn()
{
    jQuery('#votingGenerateCodeBtn').prop('disabled', true);
}
function votingSwitchToTokenEntry()
{
    document.getElementById("votingGenerateCodeBtn").hidden = "hidden";
    document.getElementById("votingSendTokenBtn").hidden = "";
    // document.getElementById("linkSwitchToSendCode").hidden = "hidden";
    // document.getElementById("linkSwitchToGenerateCode").hidden = "";
    jQuery("#pbvote_block_reg_id").hide();
    jQuery("#pbvote_block_token").show();
    jQuery("#votingToken").focus();
}
function votingSwitchToGenerateCode()
{
    document.getElementById("votingGenerateCodeBtn").hidden = "";
    document.getElementById("votingSendTokenBtn").hidden = "hidden";
    // document.getElementById("linkSwitchToSendCode").hidden = "";
    // document.getElementById("linkSwitchToGenerateCode").hidden = "hidden";
    jQuery("#pbvote_block_reg_id").show();
    jQuery("#pbvote_block_token").hide();
    jQuery("#votingRegistrationCode").focus();
}
function voting_GoToSurvey()
{
    var survey_url = jQuery('#hidden_url_to_survey').val() + "?token=" + jQuery('#votingToken').val();
    window.open( survey_url,"_self")
}
