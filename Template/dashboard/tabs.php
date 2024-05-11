<ul>
<?php

$num = 0;

$isAdmin = $this->user->isAdmin();

// Add a default tab that denotes none project and all notes
//----------------------------------------
print '<li class="singleTab" id="singleTab' .  $num . '"';
print ' data-id="' . $num . '"';
print ' data-project="0"';
print '>';
print $this->url->link(
    t('BoardNotes_DASHBOARD_ALL_TAB'),
    'BoardNotesController',
    'boardNotesShowAll',
    array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $num)
);

//----------------------------------------
print '<div class="containerNoWrap containerFloatRight">';

// buttons for ALL tab
//----------------------------------------
//print '<div class="containerNoWrap containerFloatRight">';

// reindexing is available to Admins ONLY!
print '<button id="reindexNotesAndLists"';
print $isAdmin
    ? ' class="toolbarButton buttonToggled reindexNotesAndLists"'
    : ' class="toolbarButton buttonDisabled reindexNotesAndLists"';
print $isAdmin
    ? ' title="' . t('BoardNotes_DASHBOARD_REINDEX') . '"'
    : ' title="' . t('BoardNotes_DASHBOARD_REINDEX') . ' ' . t('BoardNotes_DASHBOARD_ADMIN_ONLY') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<i class="fa fa-fw fa-recycle" aria-hidden="true"></i>';
print '</button>';

print '<button id="customListNew"';
print ' class="toolbarButton customListNew"';
print ' title="' . t('BoardNotes_DASHBOARD_NEW_CUSTOM_LIST') . '"';
print ' data-user="' . $user_id . '"';
print '>';
print '<a><i class="fa fa-fw fa-wpforms" aria-hidden="true"></i></a>';
print '</button>';

//print '<button class="toolbarSeparator">&nbsp;</button>';
//print '</div>'; // containerNoWrap containerFloatRight

// stats widget for ALL tab
//----------------------------------------
//print '<br>';
print $this->render('BoardNotes:widgets/stats', array(
     'stats_project_id' => 0,
));

print '</div>'; // containerFloatRight
//----------------------------------------

print '</li>';
$num++;

$separatorPlacedCustomGlobal = false;
$separatorPlacedCustomPrivate = false;
$separatorPlacedRegular = false;

// Loop through all projects
//----------------------------------------
foreach ($projectsAccess as $o) {
    // separator for custom GLOBAL lists
    if (!$separatorPlacedCustomGlobal && $o['is_custom'] && $o['is_global']) {
        print '<hr class="hrTabs">';
        $separatorPlacedCustomGlobal = true;
    }

    // separator for custom PRIVATE lists
    if (!$separatorPlacedCustomPrivate && $o['is_custom'] && !$o['is_global']) {
        print '<hr class="hrTabs">';
        $separatorPlacedCustomPrivate = true;
    }

    // separator for regular projects
    if (!$separatorPlacedRegular && !$o['is_custom']) {
        print '<hr class="hrTabs">';
        $separatorPlacedRegular = true;
    }

    //----------------------------------------
    print '<li class="singleTab" id="singleTab';
    print $num;
    print '" data-id="';
    print $num;
    print '" data-project="';
    print $o['project_id'];
    print '">';
    print $this->url->link(
        $o['project_name'],
        'BoardNotesController',
        'boardNotesShowAll',
        array('plugin' => 'BoardNotes', 'user_id' => $user_id, 'tab_id' => $num)
    );

    //----------------------------------------
    print '<div class="containerNoWrap containerFloatRight">';

    // buttons for single tabs
    //----------------------------------------
    //print '<div class="containerNoWrap containerFloatRight">';

    if ($o['is_custom']) {
        if ($o['is_global']) {
            // managing custom GLOBAL lists is available to Admins ONLY!
            //----------------------------------------
            print '<button id="customListRenameP' . $o['project_id'] . '"';
            print $isAdmin
                ? ' class="toolbarButton buttonToggled customListRename"'
                : ' class="toolbarButton buttonDisabled customListRename"';
            print $isAdmin
                ? ' title="' . t('BoardNotes_DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST') . '"'
                : ' title="' . t('BoardNotes_DASHBOARD_RENAME_CUSTOM_GLOBAL_LIST') . ' ' . t('BoardNotes_DASHBOARD_ADMIN_ONLY') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
            print '</button>';

            print '<button id="customListDelete-P' . $o['project_id'] . '"';
            print $isAdmin
                ? ' class="toolbarButton buttonToggled customListDelete"'
                : ' class="toolbarButton buttonDisabled customListDelete"';
            print $isAdmin
                ? ' title="' . t('BoardNotes_DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST') . '"'
                : ' title="' . t('BoardNotes_DASHBOARD_DELETE_CUSTOM_GLOBAL_LIST') . ' ' . t('BoardNotes_DASHBOARD_ADMIN_ONLY') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<i class="fa fa-trash-o" aria-hidden="true"></i>';
            print '</button>';
            //----------------------------------------
        } else {
            // managing custom PRIVATE lists is available to each user for their owned lists
            //----------------------------------------
            print '<button id="customListRenameP' . $o['project_id'] . '"';
            print ' class="toolbarButton customListRename"';
            print ' title="' . t('BoardNotes_DASHBOARD_RENAME_CUSTOM_PRIVATE_LIST') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<a><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            print '</button>';

            print '<button id="customListDelete-P' . $o['project_id'] . '"';
            print ' class="toolbarButton customListDelete"';
            print ' title="' . t('BoardNotes_DASHBOARD_DELETE_CUSTOM_PRIVATE_LIST') . '"';
            print ' data-project="' . $o['project_id'] . '"';
            print ' data-user="' . $user_id . '"';
            print '>';
            print '<a><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
            print '</button>';
            //----------------------------------------
        }
    } else {
        // shortcut buttons for regular projects ONLY
        //----------------------------------------
        print '<button id="gotoProjectBoard-P' . $o['project_id'] . '"';
        print ' class="toolbarButton gotoProjectBoard"';
        print ' title="' . t('Board') . ' ⇗' . '"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('th', '', 'BoardViewController', 'show', array('project_id' => $o['project_id']), false, 'view-board', t('Board') . ' ⇗');
        print '</button>';

        print '<button id="gotoProjectTasks-P' . $o['project_id'] . '"';
        print ' class="toolbarButton gotoProjectTasks"';
        print ' title="' . t('List') . ' ⇗' . '"';
        print ' data-project="' . $o['project_id'] . '"';
        print ' data-user="' . $user_id . '"';
        print '>';
        print $this->url->icon('list', '', 'TaskListController', 'show', array('project_id' => $o['project_id']), false, 'view-listing', t('List') . ' ⇗');
        print '</button>';
        //----------------------------------------
    }

    //print '<button class="toolbarSeparator">&nbsp;</button>';

    //print '</div>'; // containerNoWrap containerFloatRight

    // stats widget for single tabs
    //----------------------------------------
    //print '<br>';
    print $this->render('BoardNotes:widgets/stats', array(
         'stats_project_id' => $o['project_id'],
    ));

    print '</div>'; // containerFloatRight
    //----------------------------------------

    print'</li>';
    $num++;
}

?>
</ul>
