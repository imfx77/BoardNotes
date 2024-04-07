let _BoardNotes_Report_ = {}; // namespace

_BoardNotes_Report_.prepareDocument = function() {
    $('.noteTitleInput').hide();

    optionShowCategoryColors = ($('#session_vars').attr('data-optionShowCategoryColors') == 'true') ? true : false;

    // category colors
    $('.catLabel').each(function() {
        var id = $(this).attr('data-id');
        var project_id = $(this).attr('data-project');
        var category = $(this).html();
        updateCategoryColors(project_id, id, category, category)
    });

    refreshCategoryColors();
}

$( document ).ready( _BoardNotes_Report_.prepareDocument );
