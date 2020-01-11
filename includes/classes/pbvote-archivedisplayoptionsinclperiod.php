<?php
class PbVote_ArchiveDisplayOptionsInclPeriod extends PbVote_ArchiveDisplayOptions
{
    public $param_list_long  = array( 'ppage','sorder', 'view', 'sstatus', 'scategory', 'keyword', 'speriod');
    public $session_param_name = "pbv_display_params_all";

    public function set_filtering_active()
    {
        if ( !empty($this->filter_params_view['status']) ||
             !empty($this->filter_params_view['scategory']) ||
             !empty($this->filter_params_view['keyword']) ||
             !empty($this->filter_params_view['speriod'])
            ) {
            $this->filtering_active = true;
        }
    }

}
