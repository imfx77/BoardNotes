<?php

namespace Kanboard\Plugin\BoardNotes;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    const NAME = 'BoardNotes';

    public function initialize()
    {
        //HELPER
        $this->helper->register('translationsExportToJSHelper', '\Kanboard\Plugin\BoardNotes\Helper\TranslationsExportToJSHelper');

        //HOOKS
        $this->template->hook->attach('template:dashboard:sidebar', 'BoardNotes:dashboard/sidebar');
        $this->template->hook->attach('template:project:dropdown', 'BoardNotes:project/dropdown');
        $this->template->hook->attach('template:project-header:view-switcher', 'BoardNotes:project/header');

        // ROUTES
        $this->route->addRoute('boardnotes/:project_id', 'BoardNotesController', 'ShowProject', 'BoardNotes');
        $this->route->addRoute('boardnotes/:project_id/:use_cached', 'BoardNotesController', 'ShowProject', 'BoardNotes');
        $this->route->addRoute('boardnotes/:project_id/user/:user_id', 'BoardNotesController', 'ShowProject', 'BoardNotes');
        $this->route->addRoute('dashboard/:user_id/boardnotes', 'BoardNotesController', 'ShowDashboard', 'BoardNotes');
        $this->route->addRoute('dashboard/:user_id/boardnotes/:tab_id', 'BoardNotesController', 'ShowDashboard', 'BoardNotes');
    }

    public function onStartup()
    {
        $path = __DIR__ . '/Locale';
        $language = $this->languageModel->getCurrentLanguage();
        $filename = implode(DIRECTORY_SEPARATOR, array($path, $language, 'translations.php'));

        if (file_exists($filename)) {
            Translator::load($language, $path);
        } else {
            Translator::load('en_US', $path);
        }
    }

    public function getClasses()
    {
        return array(
            'Plugin\BoardNotes\Model' => array(
                'BoardNotesModel'
            )
        );
    }

    public function getPluginName()
    {
        return self::NAME;
    }

    public function getPluginAuthor()
    {
        return 'Im[F(x)]';
    }

    public function getPluginVersion()
    {
        return '0.0.6';
    }

    public function getPluginDescription()
    {
        return t('BoardNotes_PLUGIN_DESCRIPTION');
    }

    public function getPluginHomepage()
    {
        return '';
    }
}
