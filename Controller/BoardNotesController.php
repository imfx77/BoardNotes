<?php

namespace Kanboard\Plugin\BoardNotes\Controller;

use Kanboard\Controller\BaseController;

class BoardNotesController extends BaseController
{
    private function resolveUserId()
    {
        $user_id = ''; // init empty string
        $use_cached = $this->request->getStringParam('use_cached');

        if (!empty($use_cached) && isset($_SESSION['cached_user_id'])) // use cached
        {
            $user_id = $_SESSION['cached_user_id'];
        }

        if (empty($user_id)) // try get param from URL
        {
            $user_id = $this->request->getStringParam('user_id');
        }

        if (empty($user_id)) // as last resort get the current user
        {
            $user_id = $this->getUser()['id'];
        }

        $_SESSION['cached_user_id'] = $user_id;

        return $user_id;
    }

    private function resolveProject($user_id)
    {
        $project_id = $this->request->getIntegerParam('project_cus_id');
        if (empty($project_id))
        {
            $project_id = $this->request->getIntegerParam('project_id');
        }
        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        // search requested project VS access
        foreach($projectsAccess as $projectAccess)
        {
            if ($projectAccess['project_id'] == $project_id) break;
        }

        // if we didn't find the requested project, switch by default to the first one (i.e. General custom)
        if ($projectAccess['project_id'] != $project_id)
        {
            $projectAccess = $projectsAccess[0];
        }

        if ($projectAccess['is_custom'])
        {
            // assemble a fake custom project
            return array("id" => $project_id, "name" => $projectAccess['project_name'], "is_custom" => true);
        }
        else
        {
            // get all the data of existing project and mark it as NOT custom
            $project = $this->boardNotesModel->boardNotesGetProjectById($project_id);
            $project['is_custom'] = false;
            return $project;
        }
    }

    private function boardNotesShowProject_Internal($is_refresh)
    {
        $user = $this->getUser();
        $user_id = $this->resolveUserId();

        if ($is_refresh)
        {
            $project = $this->resolveProject($user_id);
        }
        else
        {
            $project = $this->getProject();
            $project['is_custom'] = false;
        }
        $project_id = $project['id'];

        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

    	if ($project['is_custom'])
    	{
            $categories = $this->boardNotesModel->boardNotesGetAllCategories();
    	}
    	else
    	{
            $categories = $this->boardNotesModel->boardNotesGetCategories($project_id);
    	}
        $columns = $this->boardNotesModel->boardNotesGetColumns($project_id);
    	$swimlanes = $this->boardNotesModel->boardNotesGetSwimlanes($project_id);

        if (!array_key_exists('boardnotesSortByState', $_SESSION)) $_SESSION['boardnotesSortByState'] = false;
        $doSortByState = $_SESSION['boardnotesSortByState'];
        $data = $this->boardNotesModel->boardNotesShowProject($project_id, $user_id, $doSortByState);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/data', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,

            'is_refresh' => $is_refresh,
            'is_dashboard_view' => 0,
        )));
    }

    public function boardNotesShowProject()
    {
        $this->boardNotesShowProject_Internal(false);
    }

    public function boardNotesRefreshProject()
    {
        $this->boardNotesShowProject_Internal(true);
    }

    public function boardNotesRefreshTabs()
    {
        $user_id = $this->resolveUserId();
        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:dashboard/tabs', array(
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,
        )));
    }

    public function boardNotesRefreshStatsWidget()
    {
        $stats_project_id = $this->request->getIntegerParam('stats_project_id');
        return $this->response->html($this->helper->layout->app('BoardNotes:widgets/stats', array(
            'stats_project_id' => $stats_project_id,
        )));
    }

    public function boardNotesShowAll()
    {
        $user = $this->getUser();
        $user_id = $this->resolveUserId();

        $tab_id = $this->request->getIntegerParam('tab_id');
        if (empty($tab_id))
        {
            $tab_id = 0;
        }

        $projectsAccess = $this->boardNotesModel->boardNotesGetAllProjectIds($user_id);

    	if ($tab_id > 0 && !$projectsAccess[$tab_id - 1]['is_custom'])
    	{
    	    $project_id = $projectsAccess[$tab_id - 1]['project_id'];
            $categories = $this->boardNotesModel->boardNotesGetCategories($project_id);
            $columns  = $this->boardNotesModel->boardNotesGetColumns($project_id);
            $swimlanes  = $this->boardNotesModel->boardNotesGetSwimlanes($project_id);
    	}
    	else
    	{
            $categories = $this->boardNotesModel->boardNotesGetAllCategories();
        	$columns  = array();
        	$swimlanes  = array();
    	}

        if (!array_key_exists('boardnotesSortByState', $_SESSION)) $_SESSION['boardnotesSortByState'] = false;
        $doSortByState = $_SESSION['boardnotesSortByState'];
        $data = $this->boardNotesModel->boardNotesShowAll($projectsAccess, $user_id, $doSortByState);

        return $this->response->html($this->helper->layout->dashboard('BoardNotes:dashboard/data', array(
            'title' => t('Notes overview for %s', $this->helper->user->getFullname($user)),
            'user' => $user,
            'user_id' => $user_id,
            'projectsAccess' => $projectsAccess,

            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
            'data' => $data,
        )));
    }

    public function boardNotesToggleSessionOption()
    {
        $session_option = $this->request->getStringParam('session_option');
        if (empty($session_option)) return false;

        // toggle options are expected to be boolean i.e. to only have values of 'true' of 'false'
        if (   !array_key_exists($session_option, $_SESSION)    // key not exist
            || !is_bool($_SESSION[$session_option]) )           // value not bool
        {
            // set initial value
            $_SESSION[$session_option] = false;
            return true;
        }

        // toggle option
        $_SESSION[$session_option] = !$_SESSION[$session_option];
        return true;
    }

    public function boardNotesGetLastModifiedTimestamp()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $validation = $this->boardNotesModel->boardNotesGetLastModifiedTimestamp($project_id, $user_id);

        $lastTimestamp = (!$validation) ? 0 : $validation['date_modified'];
        print($lastTimestamp);

        return $validation;
    }

    public function boardNotesDeleteNote()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $note_id = $this->request->getStringParam('note_id');

        $validation = $this->boardNotesModel->boardNotesDeleteNote($project_id, $user_id, $note_id);
        return $validation;
    }

    public function boardNotesDeleteAllDoneNotes()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $validation = $this->boardNotesModel->boardNotesDeleteAllDoneNotes($project_id, $user_id);
        return $validation;
    }

    public function boardNotesAddNote()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

    	$is_active = $this->request->getStringParam('is_active'); // Not needed when new is added
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');

    	$validation = $this->boardNotesModel->boardNotesAddNote($project_id, $user_id, $is_active, $title, $description, $category);
        return $validation;
    }

    public function boardNotesTransferNote()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

    	$note_id = $this->request->getStringParam('note_id');
    	$target_project_id = $this->request->getStringParam('target_project_id');

        $validation = $this->boardNotesModel->boardNotesTransferNote($project_id, $user_id, $note_id, $target_project_id);
        return $validation;
    }

    public function boardNotesUpdateNote()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

    	$note_id = $this->request->getStringParam('note_id');

    	$is_active = $this->request->getStringParam('is_active');
    	$title = $this->request->getStringParam('title');
    	$description = $this->request->getStringParam('description');
    	$category = $this->request->getStringParam('category');

        $validation = $this->boardNotesModel->boardNotesUpdateNote($project_id, $user_id, $note_id, $is_active, $title, $description, $category);
        print $validation ? time() : 0;

        return $validation;
    }

    public function boardNotesStats()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $statsData = $this->boardNotesModel->boardNotesStats($project_id, $user_id);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/stats', array(
            //'title' => t('Stats'),
            'statsData' => $statsData
        )));
    }

    public function boardNotesToTask()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $task_title = $this->request->getStringParam('task_title');
        $task_description = $this->request->getStringParam('task_description');
        $category_id = $this->request->getStringParam('category_id');
        $column_id = $this->request->getStringParam('column_id');
        $swimlane_id = $this->request->getStringParam('swimlane_id');

        $task_id = $this->taskCreationModel->create(array(
            'project_id'  => $project_id,
            'creator_id'  => $user_id,
            'owner_id'    => $user_id,
            'title'       => $task_title,
            'description' => $task_description,
            'category_id' => $category_id,
            'column_id' => $column_id,
            'swimlane_id' => $swimlane_id,
        ));

    	return $this->response->html($this->helper->layout->app('BoardNotes:project/post', array(
            //'title' => t('Post'),
            'task_id' => $task_id,
            'project_name' => $this->projectModel->getById($project_id)["name"],
            'user_name' => $this->userModel->getById($user_id)["username"],
            'task_title' => $task_title,
            'task_description' => $task_description,
            'category' => $this->categoryModel->getNameById($category_id),
            'column' => $this->columnModel->getColumnTitleById($column_id),
            'swimlane' => $this->swimlaneModel->getNameById($swimlane_id),
        )));
    }

    public function boardNotesUpdatePosition()
    {
    	$user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        $notePositions = $this->request->getStringParam('order');
        $nrNotes = $this->request->getStringParam('nrNotes');

        $validation = $this->boardNotesModel->boardNotesUpdatePosition($project_id, $user_id, $notePositions, $nrNotes);
        return $validation;
    }

    public function boardNotesReport()
    {
        $user_id = $this->resolveUserId();
        $project = $this->resolveProject($user_id);
        $project_id = $project['id'];

        if (!array_key_exists('boardnotesSortByState', $_SESSION)) $_SESSION['boardnotesSortByState'] = false;
        $doSortByState = $_SESSION['boardnotesSortByState'];
        $category = $this->request->getStringParam('category');
        $data = $this->boardNotesModel->boardNotesReport($project_id, $user_id, $doSortByState, $category);

        return $this->response->html($this->helper->layout->app('BoardNotes:project/report', array(
            'title' => $project['name'], // rather keep the project name as title
            'project' => $project,
            'project_id' => $project_id,
            'user_id' => $user_id,
            'data' => $data,
        )));
    }
}
