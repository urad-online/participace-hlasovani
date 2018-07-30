<div class="imc-CardLayoutStyle">
    <input type="hidden" id="singleHlasovaniVotingId" value="<?php echo esc_html(the_ID()); ?>">
    <div class="imc-row">
        <h3 class="imc-SectionTitleTextStyle">
            <?php echo __('Aktivační kód','participace-projekty'); ?></h3>
            <input type="text" autocomplete="off"
                placeholder="Zadejte kód" name="votingRegistrationCode" id="votingRegistrationCode" class="imc-InputStyle" value="" ></input>
                <label id="votingRegistrationCodeLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
    </div>
    <div class="imc-row">
        <div><a id="votingGenerateCodeLink" href='javascript:void(0)' onclick="voting_callAjaxGetCode()">Generovat kód</a></div>
    </div>
    <div class="imc-row">
        <span class="u-pull-left" id="votingRegistrationCodeError"></span>
    </div>

</div>
