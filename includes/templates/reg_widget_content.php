<?php
$post_id    = get_the_ID();
$voting_url = get_post_meta( $post_id, "voting_url", true);
$survey_id  = get_post_meta( $post_id, "survey_id", true);
/*
Add survay_id if not included in URL
*/
$voting_url = PbVote_GenWidget::get_url( $voting_url, $survey_id );

?>

<div class="imc-CardLayoutStyle">
    <input type="hidden" id="singleHlasovaniVotingId" value="<?php echo esc_html( $post_id); ?>">

    <div class="pbvote-collapse-section-container">
        <input class="pbvote-collapse-section " id="section_collapsed_status" name="section_collapsed_status" type="checkbox"></input>
        <label for="section_collapsed_status" >
            <div class="pbvote-collapse-section-style">
                <span><?PHP echo __('Voting &amp; Registration', 'pb-voting'); ?></span>
                <i class="material-icons md-24 u-pull-right" id="sectionExpandIndicator"></i>
            </div>
        </label>
        <article class="pbvote-collapsible-section">
            <div class="pbvote-row">
                <h4 class="pbvote-RegWidgetInputStyleLabel"><?php echo __('Aktivační kód','pb_voting'); ?></h4>
                <input type="text" autocomplete="off"
                    placeholder="Zadejte kód" name="votingRegistrationCode" id="votingRegistrationCode" class="pbvote-RegWidgetInputStyle" value="" ></input>
            </div>
            <div class="pbvote-row">
                <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_CAPTCHA_SITE_KEY ?>" data-callback="pbvotingEnableBtn"
                    data-expired-callback="pbvotingDisableBtn">
                </div>
            </div>
            <div class="pbvote-row">
                <span class="u-pull-left pbvote-RegWidgetInputErrorStyle" id="votingRegistrationCodeError">Tady text chyby</span>
            </div>
            <div class="pbvote-row">
                <!-- <div class="pbvote-RegWidgetSubmitLinkStyle"><a id="votingGenerateCodeLink" href='javascript:void(0)' onclick="voting_callAjaxGetCode()">Poslat kód</a></div> -->
                <div class="pbvote-RegWidgetBtnStyle"><button class="pbvote-RegWidgetBtn btn btn-success btn-sm"
                    type="button" id="votingGenerateCodeBtn" onclick="voting_callAjaxGetCode()" disabled readonly="readonly">Poslat kód</button></div>
            </div>

            <div class="pbvote-row">
                <span class="pbvote-RegWidgetText">Pokud Vám již byl doručen platný registrační kód přejděte na tuto stránku s </span>
                <a href='<?php echo $voting_url; ?>' target="_blank" title="Odkaz na stránku s hlasováním">hlasováním</a>
            </div>
        </article>
    </div>

</div>
