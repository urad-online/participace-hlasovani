<?php
$post_id    = $this->voting_id;
$voting_url = $this->pbvoting_meta["voting_url"][0];
$survey_id  = $this->pbvoting_meta["survey_id"][0];
// $voting_url = get_post_meta( $post_id, "voting_url", true);
// $survey_id  = get_post_meta( $post_id, "survey_id", true);
$regist_code_help = ( $this->msg_type == "sms") ? "číslo mobilního telefonu ve formátu (+420) nnn nnn nnn" : "emailovou adresu" ;
$reg_widget_labels = array(
        'title'       => $this->get_meta_value( 'regform_title', __('Registrace k hlasování', 'pb-voting')),
    'input_id'    => $this->get_meta_value( 'regform_voter_id', __('Registrační údaj','pb-voting')),
    'input_help'  => $this->get_meta_value( 'regform_input_help', 'Zadejte ' . __($regist_code_help,'pb-voting')),
    'input_token' => $this->get_meta_value( 'regform_token_id', ''),
    'submit_btn'  => $this->get_meta_value( 'regform_submit_btn', 'Poslat kód' ),
);
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
                <span><?PHP echo $reg_widget_labels['title'] ; ?></span>
                <i class="material-icons md-24 u-pull-right" id="sectionExpandIndicator"></i>
            </div>
        </label>
        <article class="pbvote-collapsible-section">
            <div class="pbvote-row">
                <h4 class="pbvote-RegWidgetInputStyleLabel"><?php echo $reg_widget_labels['input_id'] ; ?></h4>
                <input type="text" autocomplete="off"
                    placeholder="<?php echo $reg_widget_labels['input_help']; ?>" name="votingRegistrationCode" id="votingRegistrationCode" class="pbvote-RegWidgetInputStyle" value="" ></input>
            </div>
            <div class="pbvote-row">
                <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_CAPTCHA_SITE_KEY ?>" data-callback="pbvotingEnableBtn"
                    data-expired-callback="pbvotingDisableBtn">
                </div>
            </div>
            <div class="pbvote-row">
                <span class="pbvote-RegWidgetInputSuccessStyle" id="votingRegistrationCodeSuccess"></span>
            </div>
            <div class="pbvote-row">
                <span class="pbvote-RegWidgetInputErrorStyle" id="votingRegistrationCodeError"></span>
            </div>
            <div class="pbvote-row">
                <div class="pbvote-RegWidgetBtnStyle"><button class="pbvote-RegWidgetBtn btn btn-success btn-sm"
                    type="button" id="votingGenerateCodeBtn" onclick="voting_callAjaxGetCode()" disabled readonly="readonly"><?php echo $reg_widget_labels['submit_btn']; ?></button></div>
            </div>

            <div class="pbvote-row">
                <span class="pbvote-RegWidgetText">Pokud Vám již byl doručen platný registrační kód přejděte na tuto stránku s </span>
                <a href='<?php echo $voting_url; ?>' target="_blank" title="Odkaz na stránku s hlasováním">hlasováním</a>
            </div>
        </article>
    </div>

</div>
