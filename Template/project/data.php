<?php

// export translations to JS
print $this->render('BoardNotes:translations/export_to_js');

if (!$is_refresh) { // load CSS and JS only once per project !!!
    print $this->asset->css('plugins/BoardNotes/Assets/css/style.css');
    print $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js');
    print $this->asset->js('plugins/BoardNotes/Assets/js/load_project.js');
    print $this->asset->js('plugins/BoardNotes/Assets/js/load_report.js');
    print $this->asset->js('plugins/BoardNotes/Assets/js/load_stats.js');
}

//----------------------------------------

if (!$is_refresh && !$is_dashboard_view) {
  // show project header only when initially viewing notes from project
  print $this->projectHeader->render($project, 'BoardNotesController', 'boardNotesShowProject', false, 'BoardNotes');
}

//----------------------------------------

$readonlyNotes = ($project_id == 0);

$tab_id = 1;
$projectsTabsById = array();
foreach($projectsAccess as $projectAccess) {
    $projectsTabsById[ $projectAccess['project_id'] ] = array('tab_id' => $tab_id, 'name' => $projectAccess['project_name']);
    $tab_id++;
}
//----------------------------------------

$listCategoriesById = '';
$mapCategoryColorByName = array();
if(!empty($categories)) {
  foreach($categories as $cat) {
    // list by id
    $listCategoriesById .= '<option value="';
    $listCategoriesById .= $cat['id'];
    $listCategoriesById .= '">';
    $listCategoriesById .= $cat['name'];
    $listCategoriesById .= '</option>';
    // map color by name
    $mapCategoryColorByName[ $cat['name'] ] = $cat['color_id'];
    // category color hidden reference
    if (!$is_refresh) { // generate only once per project !!!
      print '<div id="category-';
      print $cat['name'];
      print '" data-color="';
      print $cat['color_id'];
      print '" class="hideMe">';
      print '</div>';
    }
  }
}

$listColumnsById = '';
if(!empty($columns)) {
  foreach($columns as $col) {
    $listColumnsById .= '<option value="';
    $listColumnsById .= $col['id'];
    $listColumnsById .= '">';
    $listColumnsById .= $col['title'];
    $listColumnsById .= '</option>';
  }
}

$listSwimlanesById = '';
if(!empty($swimlanes)) {
  foreach($swimlanes as $swim) {
    $listSwimlanesById .= '<option value="';
    $listSwimlanesById .= $swim['id'];
    $listSwimlanesById .= '">';
    $listSwimlanesById .= $swim['name'];
    $listSwimlanesById .= '</option>';
  }
}

//----------------------------------------

if (!$is_refresh) { // print only once per project !!!
    print '<div align="center">';
    print '<section class="mainholder" id="mainholderP';
    print $project_id;
    print '">';

    print '<div align="left" id="result';
    print $project_id;
    print '">';
}

//----------------------------------------
// ACTUAL CONTENT BEGINS HERE !!!
// it shall be regenerated both on initial page load and on every refresh
//----------------------------------------

    // evaluate optionShowCategoryColors option from session
    if (!array_key_exists('boardnotesShowCategoryColors', $_SESSION)) $_SESSION['boardnotesShowCategoryColors'] = false;
    $optionShowCategoryColors = $_SESSION['boardnotesShowCategoryColors'];
    // evaluate optionSortByState option from session
    if (!array_key_exists('boardnotesSortByState', $_SESSION)) $_SESSION['boardnotesSortByState'] = false;
    $optionSortByState = $_SESSION['boardnotesSortByState'];

    // session_vars (hidden reference for options)
    print '<div id="session_vars';
    print '" data-optionShowCategoryColors="';
    print $optionShowCategoryColors ? 'true' : 'false';
    print '" data-optionSortByState="';
    print $optionSortByState ? 'true' : 'false';
    print '" class="hideMe">';
    print '</div>';

//----------------------------------------

    print '<ul id="sortableRef';
    print $project_id;
    print '" class="sortableRef">';

//----------------------------------------

    print '<li id="item-0" class="ui-state-default liNewNote" data-id="0" data-project="';
    print $project_id;
    print '">';

if ($readonlyNotes) {
    print '<label class="labelNewNote">' . t('BoardNotes_PROJECT_OVERVIEW_MODE_TITLE') . '</label> ' . t('BoardNotes_PROJECT_OVERVIEW_MODE_TEXT');
} else {
    print '<label class="labelNewNote" for="textinput">' . t('BoardNotes_PROJECT_CREATE_NEW_NOTE') . '</label>';
}

    print '<div class="containerNoWrap containerFloatRight disableDblClickPropagation">';

// exclude when readonlyNotes
if (!$readonlyNotes) {

    // Settings delete all done
    print '<button id="settingsDeleteAllDone" class="toolbarButton" title="' . t('BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';

    // add some space between button groups
    print '<div class="toolbarSeparator">&nbsp;</div>';

    // Settings stats
    print '<button id="settingsStats" class="toolbarButton" title="' . t('BoardNotes_PROJECT_NOTES_STATS') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';

    // Open report
    print '<button id="settingsReport" class="toolbarButton" title="' . t('BoardNotes_PROJECT_CREATE_REPORT') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-file-text-o" aria-hidden="true"></i>';
    print '</button>';

    // add some space between button groups
    print '<div class="toolbarSeparator">&nbsp;</div>';
} // end exclude

    // Collapse all
    print '<button id="settingsCollapseAll" class="toolbarButton" title="' . t('BoardNotes_PROJECT_COLLAPSE_ALL_NOTES') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-minus-square" aria-hidden="true"></i>';
    print '</button>';

    // Expand all
    print '<button id="settingsExpandAll" class="toolbarButton" title="' . t('BoardNotes_PROJECT_EXPAND_ALL_NOTES') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-plus-square" aria-hidden="true"></i>';
    print '</button>';

    // add some space between button groups
    print '<div class="toolbarSeparator">&nbsp;</div>';

    // Toggle sort by state
    print '<button id="settingsSortByState" class="toolbarButton" title="' . t('BoardNotes_PROJECT_TOGGLE_SORT_BY_STATE') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-sort" aria-hidden="true"></i>';
    print '</button>';

    // Toggle category colors
    print '<button id="settingsCategoryColors" class="toolbarButton" title="' . t('BoardNotes_PROJECT_TOGGLE_SORT_BY_CATEGORY') . '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-paint-brush" aria-hidden="true"></i>';
    print '</button>';

    print '</div>';

// exclude when readonlyNotes
if (!$readonlyNotes) {
    // Newline after heading and top settings
    print '<br>';

    print '<div class="containerNoWrap containerFloatRight disableDblClickPropagation">';

    // Show details button
    print '<button title="' . t('BoardNotes_PROJECT_NOTE_TOGGLE_DETAILS') . '" id="showDetailsNew" class="showDetailsNew toolbarButton" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>';

    // Save button
    print '<button class="hideMe saveNewNote toolbarButton" id="saveNewNote" title="' . t('BoardNotes_PROJECT_NOTE_SAVE') . '" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';

    print '</div>';

    // Input line
    print '<input id="inputNewNote';
    print $project_id;
    print '" name="inputNewNote" type="text" placeholder="' . t('BoardNotes_PROJECT_NEW_NOTE_TITLE_PLACEHOLDER');
    print '" title="' . t('BoardNotes_PROJECT_NOTE_TITLE_SAVE_HINT');
    print '" class="inputNewNote disableDblClickPropagation" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '">';

    // Detailed view
    print '<div id="noteDescriptionP';
    print $project_id;
    print '" data-id="0" class="hideMe details containerFloatClear noteDescriptionClass ui-corner-all">';
    print '<textarea id="textareaNewNote';
    print $project_id;
    print '" class="textareaNewNote disableDblClickPropagation"></textarea>';

    // Print category select menu
    print '<p class="categories disableDblClickPropagation">';
    print '<label for="cat">' . t('Category') . '</label><br>';
    print '<select name="cat" id="catP';
    print $project_id;
    print '" data-id="0" data-project="';
    print $project_id;
    print '" data-user="';
    print $user_id;
    print '" class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget">';
    print '<option selected="selected"></option>'; // Insert empty line for keeping non category by default
    print $listCategoriesById;
    print '</select>';
    print '</p>';

    print '</div>';
} // end exclude

    print '</li>';

//----------------------------------------

$num = "1";
$last_project_id = 0;
foreach($data as $u){
    if (!empty($project_id) && $u['project_id'] != $project_id) continue;

    if ($readonlyNotes && $last_project_id != $u['project_id']){
        $last_project_id = $u['project_id'];
        print '</ul>';

        print '<h2>';
        print $this->url->link($projectsTabsById[ $last_project_id ]['name'], 'BoardNotesController', 'boardNotesShowAll',
                                array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $projectsTabsById[ $last_project_id ]['tab_id']));
        print '</h2>';

        print '<ul id="sortableRef';
        print $last_project_id;
        print '" class="sortableRef">';
    }

    print '<li id="item';
    print '-';
    print $u['id']; 
    print '" class="ui-state-default liNote';
    if (!empty($u['category']) && array_key_exists($u['category'], $mapCategoryColorByName)) {
        $category_color = $mapCategoryColorByName[ $u['category'] ];
        if (!empty($category_color)) {
            print ' color-' . $category_color;
        }
    }
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '">';

    // Here goes the icon bar for all note buttons
    print '<div class="containerNoWrap containerFloatRight disableDblClickPropagation">';

    // explicit reorder handle for mobile
    print '<div class="hideMe sortableHandle"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>';

    // Show details button
    print '<button title="' . t('BoardNotes_PROJECT_NOTE_TOGGLE_DETAILS') . '" id="showDetails';
    print $u['project_id'];
    print '-';
    print $num;
    print '" class="showDetails toolbarButton" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>';

    // hide all the utility buttons when viewing notes as readonly
    // just allow for check/uncheck note
    if (!$readonlyNotes){
        // Delete button viewed (in detailed view)
        print '<button title="' . t('BoardNotes_PROJECT_NOTE_DELETE') . '" id="singleNoteDeleteP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" class="hideMe toolbarButton singleNoteDelete" data-id="';
        print $u['id'];
        print '" data-project="';
        print $u['project_id'];
        print '" data-user="';
        print $user_id;
        print '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';

        // Save button (in detailed view)
        print '<button title="' . t('BoardNotes_PROJECT_NOTE_SAVE') . '" id="singleNoteSaveP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" class="hideMe toolbarButton singleNoteSave" data-id="';
        print $num;
        print '" data-project="';
        print $u['project_id'];
        print '" data-user="';
        print $user_id;
        print '"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';

        // add some space between button groups
        print '<div class="toolbarSeparator">&nbsp;</div>';

        // Transfer button (in detailed view)
        print '<button title="' . t('BoardNotes_PROJECT_NOTE_MOVE_TO_PROJECT') . '" id="singleNoteTransferP';
        print $u['project_id'];
        print '-';
        print $num;
        print '" class="hideMe toolbarButton singleNoteTransfer" data-id="';
        print $num;
        print '" data-note="';
        print $u['id'];
        print '" data-project="';
        print $u['project_id'];
        print '" data-user="';
        print $user_id;
        print '"><i class="fa fa-exchange" aria-hidden="true"></i></button>';

        // custom notes projects obviously CANNOT create tasks from notes
        if (!$project['is_custom']) {
            // Add note to tasks table (in detailed view)
            print '<button title="' . t('BoardNotes_PROJECT_NOTE_CREATE_TASK') . '" id="singleNoteToTaskP';
            print $u['project_id'];
            print '-';
            print $num;
            print '" class="hideMe toolbarButton singleNoteToTask" data-id="';
            print $num;
            print '" data-note="';
            print $u['id'];
            print '" data-project="';
            print $u['project_id'];
            print '" data-user="';
            print $user_id;
            print '"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>';
        }
    }

    // Category label (in simple view)
    print '<label class="catLabel catLabelClickable';
    if (!empty($u['category']) && array_key_exists($u['category'], $mapCategoryColorByName)) {
        $category_color = $mapCategoryColorByName[ $u['category'] ];
        if (!empty($category_color)) {
            print ' color-' . $category_color;
        }
    }
    print '" title="' . t('Change category') . '" id="noteCatLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '">';
    print $u['category'];
    print '</label>';

    print '</div>';

    // Here goes the title row with checkbox
    print '<div class="containerNoWrap containerFloatLeft disableDblClickPropagation">';

    // Checkbox for done note
    print '<button title="' . t('BoardNotes_PROJECT_NOTE_SWITCH_STATE') . '" id="checkDone';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" class="checkDone"><i id="noteDoneCheckmarkP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $u['is_active'];
    print '" ';
        if($u['is_active'] == "2"){
            print 'class="fa fa-spinner fa-pulse" aria-hidden="true"></i>';
        }
        if($u['is_active'] == "1"){
            print 'class="fa fa-circle-thin" aria-hidden="true"></i>';
        }
        if($u['is_active'] == "0"){
            print 'class="fa fa-check" aria-hidden="true"></i>';
        }
    print '</button>';

    // Note title input - typing. Changes after submit to label below.
    print '<input ';
    if ($readonlyNotes) print 'disabled ';
    print 'title="' . t('BoardNotes_PROJECT_NOTE_TITLE_SAVE_HINT') . '" id="noteTitleInputP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" name="noteTitle';
    print $num;
    print '" class="hideMe noteTitle" value="';
    print $u['title'];
    print '">';

    // Note title label - visual. Changes on click to input
    print '<label ';
    if ($readonlyNotes) print 'data-disabled="true" ';
    print 'title="' . t('BoardNotes_PROJECT_NOTE_EDIT_HINT') . '" id="noteTitleLabelP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" type="text" placeholder="" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" name="noteTitleLabel';
    print $num;
    if($u['is_active'] == "0"){
        print '" class="noteTitleLabel noteTitle noteDoneDesignText" value="">';
    } else {
        print '" class="noteTitleLabel noteTitle" value="">';
    }
    print $u['title'];
    print '</label>';

    print '</div>';

    // Here goes the detailed view
    print '<div id="notePlaceholderDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" class="containerFloatClear hideMe">';
    print '&nbsp';
    print '</div>';

    print '<div id="noteDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '" ';
    print 'class="hideMe details containerFloatClear noteDescriptionClass ui-corner-all">';
    print '<textarea title="' . t('BoardNotes_PROJECT_NOTE_DESCR_SAVE_HINT') . '" ';
    if ($readonlyNotes) print 'disabled ';
    if($u['is_active'] == "0"){
        print '" class="disableDblClickPropagation textareaDescription noteDoneDesignTextarea"';
    } else {
        print '" class="disableDblClickPropagation textareaDescription"';
    }
    print ' id="noteTextareaDescriptionP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '">';
    print $u['description'];
    print '</textarea>';

    print '<p class="categories disableDblClickPropagation">';
    print '<label for="cat">' . t('Category') . '</label><br>';
    print '<select ';
    if ($readonlyNotes) print 'disabled ';
    print 'name="cat" class="catSelector ui-selectmenu-button ui-selectmenu-button-closed ui-corner-all ui-button ui-widget"';
    print ' id="catP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $u['project_id'];
    print '" data-user="';
    print $user_id;
    print '">';

    if ($readonlyNotes){
        // just preserve the existing category data from the note
        print '<option selected="selected">'.$u['category'].'</option>';
    }
    else{
        $emptyCatList = empty($listCategoriesById);
        $emptyCat = empty($u['category']);

        if ($emptyCatList || $emptyCat){ // If no categories available or none selected
            print '<option selected="selected"></option>'; // None category selected
        }
        if (!$emptyCat && !$emptyCatList){
            print '<option></option>'; // add an empty category option
            foreach($categories as $cat) { // detect the selected category
                if ($cat['name'] == $u['category']){
                    print '<option value="'.$cat['id'].'" selected="selected">';
                }else{
                    print '<option value="'.$cat['id'].'">';
                }
                print $cat['name'];
                print '</option>';
            }
        }
        if ($emptyCat && !$emptyCatList){
            print $listCategoriesById;
        }
    }

    print '</select>';
    print '</p>';

    print '</div>';

    // Project_id (hidden reference for each note)
    print '<div id="project_id';
    print $num;
    print '" data-id="';
    print $u['project_id'];
    print '" class="hideMe">';
    print '</div>';

    // Note_id (hidden reference for each note)
    print '<div id="note_idP';
    print $u['project_id'];
    print '-';
    print $num;
    print '" data-id="';
    print $u['id'];
    print '" class="hideMe">';
    print '</div>';

    print '</li>';

    // Id
    $num++;
}

print '</ul>';

//----------------------------------------

// hidden reference for number of notes
print '<div id="nrNotes" class="hideMe"';
print ' data-id="';
$num = --$num;
print $num;
print '"></div>';

// hidden reference for project_id and user_id of the currently active page
print '<div id="refProjectId" class="hideMe"';
print ' data-project="';
print $project_id;
print '" data-user="';
print $user_id;
print '" data-timestamp="';
print time();
print '"></div>';

print '<span id="boardnotesBusyIcon" class="boardnotesBusyIcon hideMe">';
print '&nbsp;<i class="fa fa-refresh fa-spin" title="' . t('BoardNotes_PROJECT_NOTE_BUSY_ICON_HINT') . '"></i></span>';


//----------------------------------------
// ACTUAL CONTENT ENDS HERE !!!
// all sections below must NOT be appended again on refresh
//----------------------------------------

if (!$is_refresh) { // print only once per project !!!
    print '</div>'; // id='result'
}

//----------------------------------------

if (!$is_refresh) { // print only once per project !!!

  print '<div class="hideMe" id="dialogDeleteAllDone" title="' . t('BoardNotes_PROJECT_DELETE_ALL_DONE_NOTES') . '">';
  print '<p style="white-space: pre-wrap;">';
  print t('BoardNotes_DELETEALLDONE_DIALOG_MSG');
  print '</p>';
  print '</div>';

  print '<div class="hideMe" id="dialogStats" title="' . t('BoardNotes_PROJECT_NOTES_STATS') . '">';
  print '<div id="dialogStatsInside"></div>';
  print '</div>';

  //---------------------------------------------

  print '<div class="hideMe" id="dialogToTaskP'.$project_id.'" title="' . t('BoardNotes_PROJECT_NOTE_CREATE_TASK') . '">';

  print '<div id="dialogToTaskParams">';

  print '<label for="listCatToTask">' . t('Category') . ' : &nbsp;</label>';
  print '<select name="listCatToTask" id="listCatToTaskP';
  print $project_id;
  print '">';
  // Only allow blank select if there's other selectable options
  if (!empty($listCategoriesById)){
    print '<option></option>';
  }
  print $listCategoriesById;
  print '</select>';
  print '<br>';

  print '<label for="listColToTask">' . t('Column') . ' : &nbsp;</label>';
  print '<select name="listColToTask" id="listColToTaskP';
  print $project_id;
  print '">';
  print $listColumnsById;
  print '</select>';
  print '<br>';

  print '<label for="listSwimToTask">' . t('Swimlane') . ' : &nbsp;</label>';
  print '<select name="listSwimToTask" id="listSwimToTaskP';
  print $project_id;
  print '">';
  print $listSwimlanesById;
  print '</select>';
  print '<br>';

  print '<input type="checkbox" checked name="removeNote" id="removeNoteP';
  print $project_id;
  print '">';
  print '<label for="removeNote">&nbsp;&nbsp;' . t('BoardNotes_CREATETASK_CHECKBOX_REMOVE_TASK') . '</label>';

  print '</div>';

  print '<div id="deadloading" class="hideMe"></div>';
  print '</div>';

  //---------------------------------------------

  print '<div class="hideMe" id="dialogTransferP'.$project_id.'" title="' . t('BoardNotes_PROJECT_NOTE_MOVE_TO_PROJECT') . '">';

  print '<label for="listNoteProject">' . t('BoardNotes_TRANSFERNOTE_DIALOG_TARGET_PROJECT') . ' : &nbsp&nbsp;</label>';
  print '<select name="listNoteProject" id="listNoteProjectP';
  print $project_id;
  print '">';
  foreach($projectsTabsById as $key => $projectTab) {
    if ($key != $project_id){
      print '<option value="';
      print $key;
      print '">';
      print $projectTab['name'];
      print '</option>';
    }
  }
  print '</select>';
  print '<br><br>';
  print '<p style="white-space: pre-wrap;">';
  print t('BoardNotes_TRANSFERNOTE_DIALOG_MSG');
  print '</p>';

  print '</div>';

  //---------------------------------------------

  print '<div class="hideMe" id="dialogReportP'.$project_id.'" title="' . t('BoardNotes_PROJECT_CREATE_REPORT') . '">';
  print '<div id="">';
  print '<label for="reportCat">' . t('BoardNotes_REPOSR_DIALOG_CATEGORY_FILTER') . ' :</label><br>';
  print '<select name="reportCat" id="reportCatP';
  print $project_id;
  print '" data-project="';
  print $project_id;
  print '" data-user="';
  print $user_id;
  print '">';
  
  print '<option></option>'; // add an empty category option
  if (!empty($listCategoriesById)){
      print $listCategoriesById;
  }

  print '</select>';
  print '</div>';
  print '</div>';

  //---------------------------------------------

  print '</section>';
  print '</div>';

} // if (!$is_refresh)

?>
