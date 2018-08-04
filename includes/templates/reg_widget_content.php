<div class="imc-CardLayoutStyle">
    <input type="hidden" id="singleHlasovaniVotingId" value="<?php echo esc_html(the_ID()); ?>">

    <div class="pbvote-collapse-section-container">
        <input class="pbvote-collapse-section " id="section_collapsed_status" name="section_collapsed_status" type="checkbox"></input>
        <label for="section_collapsed_status" >
            <div class="pbvote-collapse-section-style">
                <span><?PHP echo __('Voting &amp; Registration', 'pb-voting'); ?></span>
                <i class="material-icons md-24 u-pull-right" id="sectionExpandIndicator"></i>
            </div>
        </label>
        <article class="pbvote-collapsible-section">
            <div>
                <h4 class=""><?php echo __('Aktivační kód','participace-projekty'); ?></h4>
                <input type="text" autocomplete="off"
                    placeholder="Zadejte kód" name="votingRegistrationCode" id="votingRegistrationCode" class="imc-InputStyle" value="" ></input>
                <label id="votingRegistrationCodeLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
            </div>
                <div class="imc-row">
                    <div><a id="votingGenerateCodeLink" href='javascript:void(0)' onclick="voting_callAjaxGetCode()">Poslat kód</a></div>
                </div>
                <div class="imc-row">
                    <span class="u-pull-left" id="votingRegistrationCodeError"></span>
                </div>
        </article>
    </div>

</div>
