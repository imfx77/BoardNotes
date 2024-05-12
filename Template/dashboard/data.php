<style>
.mainholderDashboard {
    width: 750px;
    font: 16px 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #4D4D4D;
    -webkit-font-smoothing: antialiased;
    position: relative;
    font-weight: 300;
}

.mainholderMobileDashboard {
    width: auto;
    font: 16px 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #4D4D4D;
    -webkit-font-smoothing: antialiased;
    position: relative;
    font-weight: 300;
}

.sidebar {
    max-width: 50%;
    min-width: 350px;
}

.hrTabs {
    border: 1px solid;
    width: 100%;
}
</style>

<?= $this->asset->js('plugins/BoardNotes/Assets/js/tabs.js') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/load_dashboard.js') ?>

<div id="myNotesHeader" class="page-header"><h2>
<?= t('BoardNotes_DASHBOARD_MY_NOTES')?> > <?= t('BoardNotes_DASHBOARD_ALL_TAB') ?>
</h2></div>

<section id="mainholderDashboard" class="mainholderDashboard sidebar-container">

<div id="tabs" class="sidebar">

    <?= $this->render('BoardNotes:dashboard/tabs', array(
         'projectsAccess' => $projectsAccess,
         'user_id' => $user_id,
    )) ?>

</div>

<div id="content" class="sidebar-content">

<?php

$project = array('id' => 0, 'name' => t('BoardNotes_DASHBOARD_ALL_TAB'));
if ($tab_id > 0) {
    $projectAccess = $projectsAccess[$tab_id - 1];
    $project = array('id' => $projectAccess['project_id'],
                     'name' => $projectAccess['project_name'],
                     'is_custom' => $projectAccess['is_custom']);
}

?>

<?= $this->render('BoardNotes:project/data', array(
    'projectsAccess' => $projectsAccess,
    'project' => $project,
    'project_id' => $project['id'],
    'user' => $user,
    'user_id' => $user_id,
    'is_refresh' => false,
    'is_dashboard_view' => 1,
    'data' => $data,
    'categories' => $categories,
    'columns' => $columns,
    'swimlanes' => $swimlanes,
)) ?>

</div>

</section>

<?php

// tabId (hidden reference for tabs)
print '<div class="hideMe" id="tabId"';
print ' data-tab="' . $tab_id  . '"';
print ' data-project="' . $project['id'] . '"';
print '></div>';

