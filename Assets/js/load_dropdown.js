/**
 * @author  Im[F(x)]
 */

class _TodoNotes_Dropdown_ {

//------------------------------------------------
static #refreshStatsWidget() {
    // refresh ONLY if the opened dropdown has stats widget and it is visible
    if ( ($(".BoardNotes_ProjectDropdown_StatsWidget").length === 2) &&
         ($(".BoardNotes_ProjectDropdown_StatsWidget:last").is(":visible")) ) {

        const project_id = $(".BoardNotes_ProjectDropdown_StatsWidget:last").attr('data-project');

        // don't cache ajax or content won't be fresh
        $.ajaxSetup ({
          cache: false
        });
        const loadUrl = '/?controller=BoardNotesController&action=RefreshStatsWidget&plugin=BoardNotes'
                    + '&stats_project_id=' + project_id;
        $(".BoardNotes_ProjectDropdown_StatsWidget:last").html(_TodoNotes_Translations_.msgLoadingSpinner).load(loadUrl,
            function() {
                _TodoNotes_Statuses_.expandStatusAliases();
            });
    }

    // re-schedule
    setTimeout(function() {
        _TodoNotes_Dropdown_.#refreshStatsWidget();
    }, 15 * 1000); // 15 sec
}

//------------------------------------------------
static prepareDocument() {
    _TodoNotes_Translations_.initialize();

    _TodoNotes_Statuses_.expandStatusAliases();

    _TodoNotes_Dropdown_.#refreshStatsWidget();
}

//------------------------------------------------

} // class _TodoNotes_Dropdown_

//////////////////////////////////////////////////
$( document ).ready( _TodoNotes_Dropdown_.prepareDocument );
