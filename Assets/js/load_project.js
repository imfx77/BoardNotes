class _BoardNotes_Project_ {

//------------------------------------------------
static prepareDocument() {
    _BoardNotes_.optionShowCategoryColors = ($("#session_vars").attr('data-optionShowCategoryColors') == 'true') ? true : false;
    _BoardNotes_.optionSortByStatus = ($("#session_vars").attr('data-optionSortByStatus') == 'true') ? true : false;
    _BoardNotes_.optionShowAllDone = ($("#session_vars").attr('data-optionShowAllDone') == 'true') ? true : false;

    var project_id = $("#refProjectId").attr('data-project');
    var user_id = $("#refProjectId").attr('data-user');
    var isMobile = _BoardNotes_.isMobile();
    var readonlyNotes = (project_id == 0); // Overview Mode

    // notes reordering is disabled when explicitly sorted by Status
    if (!_BoardNotes_.optionSortByStatus) {
        $(".sortableList").each(function() {
            var sortable_project_id = $(this).attr('data-project');
            var sortable_notes_number = $("#nrNotes-P" + sortable_project_id).attr('data-num');

            $("#sortableList-P" + sortable_project_id).sortable({
                placeholder: "ui-state-highlight",
                items: 'li.liNote', // exclude the NewNote
                cancel: '.disableEventsPropagation',
                update: function() {
                    // handle notes reordering
                    var order = $(this).sortable('toArray');
                    order = order.join(",");
                    var regex = new RegExp('item-', 'g');
                    order = order.replace(regex, '');
                    order = order.split(',');
                    _BoardNotes_.sqlUpdatePosition(sortable_project_id, user_id, order, sortable_notes_number);
                }
            });

            if (isMobile) {
                // bind explicit reorder handles for mobile
                $("#sortableList-P" + sortable_project_id).sortable({
                    handle: ".sortableHandle",
                });
            }
        });

        if (isMobile) {
            // show explicit reorder handles for mobile
            $(".sortableHandle").removeClass( 'hideMe' );
        }
    }

    if(isMobile) {
        // choose mobile view
        $("#mainholderP" + project_id).removeClass( 'mainholder' ).addClass( 'mainholderMobile' );

        // show all Save buttons
        if (!readonlyNotes ) { // if NOT in Overview Mode
            $(".saveNewNote").removeClass( 'hideMe' );
            $(".noteSave").removeClass( 'hideMe' );
        }
    }

    _BoardNotes_Translations_.initialize();

    _BoardNotes_Project_.resizeDocument();

    _BoardNotes_.refreshCategoryColors();
    _BoardNotes_.refreshSortByStatus();
    _BoardNotes_.refreshShowAllDone();

    // prepare method for dashboard view if embedded
    if (typeof _BoardNotes_Dashboard_ !== 'undefined') {
        _BoardNotes_Dashboard_.prepareDocument();
    }

    // force render all KB elements
    KB.render();

    setTimeout(function() {
        _BoardNotes_.showTitleInputNewNote();
    }, 100);
}

//------------------------------------------------
static resizeDocument() {
    _BoardNotes_.adjustAllNotesPlaceholders();
    _BoardNotes_.adjustAllNotesTitleInputs();
    setTimeout(function() {
        _BoardNotes_.adjustScrollableContent();
    }, 100);
}

//------------------------------------------------

} // class _BoardNotes_Project_

//////////////////////////////////////////////////
window.onresize = _BoardNotes_Project_.resizeDocument;
$( document ).ready( _BoardNotes_Project_.prepareDocument );
