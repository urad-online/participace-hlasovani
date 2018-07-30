var cgbmSelectForm;
var cgbmEditForm;

function ou_test(input,label)
{
    console.log(input + " - "+ label);
    alert(input + " - "+ label);
}
function voting_callAjaxGetCode()
{
    var code = jQuery('#votingRegistrationCode').val();
    var votingId = jQuery('#singleHlasovaniVotingId').text();
    var postRequest = {
        'action': 'pbvote_getcode',
        'voter_id': code,
        'voting_id': votingId,
    };
    console.log( votingId);
    console.log( code);

    // jQuery('#voting_loader_image').show();

    jQuery("#votingRegistrationCodeError").html("");
    jQuery("body").css("cursor", "progress");

    jQuery.post(ajax_object.ajax_url, postRequest, function(response) {

        var resp = JSON.parse(response);

        console.log( resp );

        // jQuery('#cgbm_loader_image').hide();
        jQuery("body").css("cursor", "default");
        if (resp.result == 'error') {
            jQuery("#votingRegistrationCodeError").html(resp.message);
        } else {
            jQuery("#votingRegistrationCodeError").html("OK");
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

function ou_cgbm_Tabs( clickedId, newTabLabel)
{
    this.clickedLinkId = clickedId;
    this.tabIdPrefix = "cgbm-tab-level-";
    this.tabPanelClass = 'cgbm-tab-item';
    this.mainContainerClass = 'cgbm-tabs-container';
    this.tabListContainerClass = 'vc_tta-tabs-list';
    this.ttaPanelsContainerClass = 'vc_tta-panels';
    this.tabActiveClass = 'vc_active';
    this.clickedInTab;
    this.newTabId = "";
    this.newTabLabel = newTabLabel;
    this.numberOfTabs = -1;
    this.clickedInTabOrder = -1;
}

ou_cgbm_Tabs.prototype.ou_cgbm_setTabIds = function()
{
    var tabPanelId = jQuery('#'+this.clickedLinkId).closest('.'+this.tabPanelClass).attr('id');
    this.clickedInTab = document.getElementById(tabPanelId);
    var substrIndex = tabPanelId.lastIndexOf("-");
    if (substrIndex >-1) {
        this.newTabId = this.tabIdPrefix + (parseInt(tabPanelId.substr(substrIndex+1)) +1) ;
    } else {
        this.newTabId = this.tabIdPrefix + "n";
    }
    this.numberOfTabs = this.clickedInTab.parentNode.childElementCount;
    var tabIndex = 0;
    var element = this.clickedInTab;
    do {
        tabIndex++;
        element = element.previousSibling;
    } while (element);

    this.clickedInTabOrder = tabIndex - 1 ;
    this.mainContainer = document.getElementsByClassName(this.mainContainerClass)[0];
    this.tabListContainer = this.mainContainer.getElementsByClassName(this.tabListContainerClass)[0];
    this.ttaPanelsContainer = this.mainContainer.getElementsByClassName(this.ttaPanelsContainerClass)[0];
}

ou_cgbm_Tabs.prototype.Cgbm_Item_AddNewTab = function( content )
{
    this.tabContent = content;
    this.deleteLastTabs();
    this.Cgbm_Item_AddNewTabPanel();
    this.Cgbm_Item_AddNewTabLink();
    this.numberOfTabs = this.tabListContainer.childElementCount;
}

ou_cgbm_Tabs.prototype.Cgbm_Item_AddNewTabLink = function()
{
    var lastTab = this.tabListContainer.lastChild;
    var newTab = lastTab.cloneNode(true);
    newTab.getElementsByTagName("a")[0].href = "#"+this.newTabId;
    newTab.getElementsByTagName("span")[0].innerHTML = this.newTabLabel;
    // newTab.classList.add(this.tabActiveClass);
    lastTab.classList.remove(this.tabActiveClass);
    this.tabListContainer.appendChild(newTab);
}

ou_cgbm_Tabs.prototype.Cgbm_Item_AddNewTabPanel = function()
{
    var lastTab = this.ttaPanelsContainer.lastChild;
    var newTab = lastTab.cloneNode(true);
    newTab.id = this.newTabId;
    newTab.getElementsByTagName("a")[0].href = "#"+this.newTabId;
    newTab.getElementsByTagName("a")[0].getElementsByTagName("span")[0].innerHTML = this.newTabLabel;
    // newTab.classList.add(this.tabActiveClass);
    lastTab.classList.remove(this.tabActiveClass);
    this.ttaPanelsContainer.appendChild(newTab);
    this.deleteContent();
}

ou_cgbm_Tabs.prototype.renameTab = function()
{
    var lastTab = this.tabListContainer.lastChild;
    lastTab.getElementsByTagName("span")[0].innerHTML = this.newTabLabel;
    lastTab = this.ttaPanelsContainer.lastChild;
    lastTab.getElementsByTagName("a")[0].getElementsByTagName("span")[0].innerHTML = this.newTabLabel;
}

ou_cgbm_Tabs.prototype.deleteContent = function()
{
    this.ttaPanelsContainer.lastElementChild.getElementsByClassName('vc_tta-panel-body')[0].firstElementChild.innerHTML = "";
}
ou_cgbm_Tabs.prototype.writeContent = function( content)
{
    this.ttaPanelsContainer.lastElementChild.getElementsByClassName('vc_tta-panel-body')[0].firstElementChild.innerHTML = content;
}

ou_cgbm_Tabs.prototype.deleteLastTabs = function()
{
    var count = this.numberOfTabs - this.clickedInTabOrder - 1;
    if ( 0 <  count ) {
        for (var i = this.numberOfTabs; i > (this.numberOfTabs - count); i--) {
            this.tabListContainer.removeChild(this.tabListContainer.lastChild);
            this.ttaPanelsContainer.removeChild(this.ttaPanelsContainer.lastChild);
        }
        this.numberOfTabs = this.tabListContainer.childElementCount;
    }
}
