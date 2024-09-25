<?php

/**
 * Sqlite schema
 * @package Kanboard\Plugin\TodoNotes\Schema
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Schema;

use PDO;

const VERSION = 1;


//////////////////////////////////////////////////
//  VERSION = 1
//////////////////////////////////////////////////

//------------------------------------------------
// v.1 schema routines
//------------------------------------------------
function version_1(PDO $pdo)
{
    // create+insert+index custom projects
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_custom_projects (
                    id INTEGER PRIMARY KEY,
                    owner_id INTEGER NOT NULL DEFAULT 0,
                    position INTEGER,
                    project_name TEXT
                )');
    $pdo->exec('INSERT INTO todonotes_custom_projects
                    (owner_id, position, project_name)
                    VALUES (0, 1, "Global Notes")
                ');
    $pdo->exec('INSERT INTO todonotes_custom_projects
                    (owner_id, position, project_name)
                    VALUES (0, 2, "Global TODO")
                ');
    $pdo->exec('CREATE INDEX todonotes_custom_projects_owner_ix ON todonotes_custom_projects(owner_id)');
    $pdo->exec('CREATE INDEX todonotes_custom_projects_position_ix ON todonotes_custom_projects(position)');

    // create+index sharing permissions
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_sharing_permissions (
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    permissions INTEGER NOT NULL
                )');
    $pdo->exec('CREATE INDEX todonotes_sharing_permissions_project_ix ON todonotes_sharing_permissions(project_id)');
    $pdo->exec('CREATE INDEX todonotes_sharing_permissions_user_ix ON todonotes_sharing_permissions(user_id)');

    // create+insert+index entries
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_entries (
                    id INTEGER PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    position INTEGER,
                    is_active INTEGER,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_notified INTEGER,
                    last_notified INTEGER,
                    flags_notified INTEGER,
                    date_restored INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_entries
                    (project_id, user_id, position, is_active, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored)
                    VALUES (0, 0, 0, -1, 0, 0, 0, 0, 0, 0)
                ');
    $pdo->exec('CREATE INDEX todonotes_entries_project_ix ON todonotes_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_user_ix ON todonotes_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_position_ix ON todonotes_entries(position)');
    $pdo->exec('CREATE INDEX todonotes_entries_active_ix ON todonotes_entries(is_active)');
    $pdo->exec('CREATE INDEX todonotes_entries_created_ix ON todonotes_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_entries_modified_ix ON todonotes_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_entries_notified_ix ON todonotes_entries(date_notified)');
    $pdo->exec('CREATE INDEX todonotes_entries_last_notified_ix ON todonotes_entries(last_notified)');
    $pdo->exec('CREATE INDEX todonotes_entries_restored_ix ON todonotes_entries(date_restored)');

    // create+insert+index archive entries
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_archive_entries (
                    id INTEGER PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_notified INTEGER,
                    last_notified INTEGER,
                    date_archived INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_archive_entries
                    (project_id, user_id, date_created, date_modified, date_notified, last_notified, date_archived)
                    VALUES (0, 0, 0, -1, 0, 0, 0)
                ');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_project_ix ON todonotes_archive_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_user_ix ON todonotes_archive_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_created_ix ON todonotes_archive_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_modified_ix ON todonotes_archive_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_notified_ix ON todonotes_archive_entries(date_notified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_last_notified_ix ON todonotes_archive_entries(last_notified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_archived_ix ON todonotes_archive_entries(date_archived)');

    // create+index webpn subscriptions
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_webpn_subscriptions (
                    endpoint TEXT NOT NULL PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    subscription TEXT NOT NULL
                )');
    $pdo->exec('CREATE INDEX todonotes_webpn_subscriptions_user_ix ON todonotes_webpn_subscriptions(user_id)');
}

//------------------------------------------------
// v.1 reindex routines
//------------------------------------------------
function Reindex_Rename_OldTables_1(PDO $pdo)
{
    $pdo->exec('ALTER TABLE todonotes_custom_projects RENAME TO todonotes_custom_projects_OLD');
    $pdo->exec('ALTER TABLE todonotes_sharing_permissions RENAME TO todonotes_sharing_permissions_OLD');
    $pdo->exec('ALTER TABLE todonotes_entries RENAME TO todonotes_entries_OLD');
    $pdo->exec('ALTER TABLE todonotes_archive_entries RENAME TO todonotes_archive_entries_OLD');
}

function Reindex_AddAndUpdate_OldProjectIds_1(PDO $pdo)
{
    $pdo->exec('ALTER TABLE todonotes_custom_projects_OLD ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_custom_projects_OLD SET old_project_id = id');
    
    $pdo->exec('ALTER TABLE todonotes_sharing_permissions_OLD ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_sharing_permissions_OLD SET old_project_id = project_id');

    $pdo->exec('ALTER TABLE todonotes_entries_OLD ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_entries_OLD SET old_project_id = project_id');
    
    $pdo->exec('ALTER TABLE todonotes_archive_entries_OLD ADD old_project_id INTEGER');
    $pdo->exec('UPDATE todonotes_archive_entries_OLD SET old_project_id = project_id');
}

function Reindex_CreateAndInsert_NewShrunkCustomProjects_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE todonotes_custom_projects (
                    id INTEGER PRIMARY KEY,
                    owner_id INTEGER NOT NULL DEFAULT 0,
                    position INTEGER,
                    project_name TEXT
                )');
    $pdo->exec('INSERT INTO todonotes_custom_projects
				    (owner_id, position, project_name)
                    SELECT owner_id, position, project_name
				    FROM todonotes_custom_projects_OLD
				');

    $pdo->exec('CREATE TABLE todonotes_custom_projects_REINDEX (
                    id INTEGER PRIMARY KEY,
                    old_project_id INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_custom_projects_REINDEX
				    (old_project_id)
                    SELECT old_project_id
				    FROM todonotes_custom_projects_OLD
				');
}

function Reindex_CrossUpdate_ReindexedProjectIds_1(PDO $pdo)
{
    $pdo->exec('UPDATE todonotes_sharing_permissions_OLD
                    SET project_id = -
                        (SELECT tProjects.id FROM todonotes_custom_projects_REINDEX tProjects
                        WHERE todonotes_sharing_permissions_OLD.old_project_id = -tProjects.old_project_id)
                    WHERE EXISTS (SELECT tProjects.id FROM todonotes_custom_projects_REINDEX tProjects
                        WHERE todonotes_sharing_permissions_OLD.old_project_id = -tProjects.old_project_id)
                ');
    $pdo->exec('UPDATE todonotes_entries_OLD
                    SET project_id = -
                        (SELECT tProjects.id FROM todonotes_custom_projects_REINDEX tProjects
                        WHERE todonotes_entries_OLD.old_project_id = -tProjects.old_project_id)
                    WHERE EXISTS (SELECT tProjects.id FROM todonotes_custom_projects_REINDEX tProjects
                        WHERE todonotes_entries_OLD.old_project_id = -tProjects.old_project_id)
                ');
    $pdo->exec('UPDATE todonotes_archive_entries_OLD
                    SET project_id = -
                        (SELECT tProjects.id FROM todonotes_custom_projects_REINDEX tProjects
                        WHERE todonotes_archive_entries_OLD.old_project_id = -tProjects.old_project_id)
                    WHERE EXISTS (SELECT tProjects.id FROM todonotes_custom_projects_REINDEX tProjects
                        WHERE todonotes_archive_entries_OLD.old_project_id = -tProjects.old_project_id)
                ');
}

function Reindex_CreateAndInsert_NewShrunkSharingPermissions_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE todonotes_sharing_permissions (
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    permissions INTEGER NOT NULL
                )');
    $pdo->exec('INSERT INTO todonotes_sharing_permissions
                    (project_id, user_id, permissions)
                    SELECT project_id, user_id, permissions
                    FROM todonotes_sharing_permissions_OLD
                    WHERE project_id <> 0 AND user_id > 0 AND permissions > 0
                ');
}

function Reindex_CreateAndInsert_NewShrunkEntries_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE todonotes_entries (
                    id INTEGER PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    position INTEGER,
                    is_active INTEGER,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_notified INTEGER,
                    last_notified INTEGER,
                    flags_notified INTEGER,
                    date_restored INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_entries
                    (project_id, user_id, position, is_active, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored)
                    VALUES (0, 0, 0, -1, 0, 0, 0, 0, 0, 0)
                ');
    $pdo->exec('INSERT INTO todonotes_entries
                    (project_id, user_id, position, is_active, title, category, description, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored)
                    SELECT project_id, user_id, position, is_active, title, category, description, date_created, date_modified, date_notified, last_notified, flags_notified, date_restored
                    FROM todonotes_entries_OLD
                    WHERE project_id <> 0 AND user_id > 0 AND position > 0 AND is_active >= 0
                ');
}

function Reindex_CreateAndInsert_NewShrunkArchiveEntries_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS todonotes_archive_entries (
                    id INTEGER PRIMARY KEY,
                    project_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    title TEXT,
                    category TEXT,
                    description TEXT,
                    date_created INTEGER,
                    date_modified INTEGER,
                    date_notified INTEGER,
                    last_notified INTEGER,
                    date_archived INTEGER
                )');
    $pdo->exec('INSERT INTO todonotes_archive_entries
                    (project_id, user_id, date_created, date_modified, date_notified, last_notified, date_archived)
                    VALUES (0, 0, 0, -1, 0, 0, 0)
                ');
    $pdo->exec('INSERT INTO todonotes_archive_entries
                    (project_id, user_id, title, category, description, date_created, date_modified, date_notified, last_notified, date_archived)
                    SELECT project_id, user_id, title, category, description, date_created, date_modified, date_notified, last_notified, date_archived
                    FROM todonotes_archive_entries_OLD
                    WHERE project_id <> 0 AND user_id > 0 AND date_modified > 0 AND date_archived > 0
                ');
}

function Reindex_Drop_OldTables_1(PDO $pdo)
{
    $pdo->exec('DROP TABLE todonotes_custom_projects_REINDEX');
    
    $pdo->exec('DROP TABLE todonotes_custom_projects_OLD');
    $pdo->exec('DROP TABLE todonotes_sharing_permissions_OLD');
    $pdo->exec('DROP TABLE todonotes_entries_OLD');
    $pdo->exec('DROP TABLE todonotes_archive_entries_OLD');
}

function Reindex_RecreateIndices_CustomProjects_1(PDO $pdo)
{
    $pdo->exec('CREATE INDEX todonotes_custom_projects_owner_ix ON todonotes_custom_projects(owner_id)');
    $pdo->exec('CREATE INDEX todonotes_custom_projects_position_ix ON todonotes_custom_projects(position)');
}

function Reindex_RecreateIndices_SharingPermissions_1(PDO $pdo)
{
    $pdo->exec('CREATE INDEX todonotes_sharing_permissions_project_ix ON todonotes_sharing_permissions(project_id)');
    $pdo->exec('CREATE INDEX todonotes_sharing_permissions_user_ix ON todonotes_sharing_permissions(user_id)');
}

function Reindex_RecreateIndices_Entries_1(PDO $pdo)
{
    $pdo->exec('CREATE INDEX todonotes_entries_project_ix ON todonotes_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_user_ix ON todonotes_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_entries_position_ix ON todonotes_entries(position)');
    $pdo->exec('CREATE INDEX todonotes_entries_active_ix ON todonotes_entries(is_active)');
    $pdo->exec('CREATE INDEX todonotes_entries_created_ix ON todonotes_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_entries_modified_ix ON todonotes_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_entries_notified_ix ON todonotes_entries(date_notified)');
    $pdo->exec('CREATE INDEX todonotes_entries_last_notified_ix ON todonotes_entries(last_notified)');
    $pdo->exec('CREATE INDEX todonotes_entries_restored_ix ON todonotes_entries(date_restored)');
}

function Reindex_RecreateIndices_ArchiveEntries_1(PDO $pdo)
{
    $pdo->exec('CREATE INDEX todonotes_archive_entries_project_ix ON todonotes_archive_entries(project_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_user_ix ON todonotes_archive_entries(user_id)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_created_ix ON todonotes_archive_entries(date_created)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_modified_ix ON todonotes_archive_entries(date_modified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_notified_ix ON todonotes_archive_entries(date_notified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_last_notified_ix ON todonotes_archive_entries(last_notified)');
    $pdo->exec('CREATE INDEX todonotes_archive_entries_archived_ix ON todonotes_archive_entries(date_archived)');
}

//////////////////////////////////////////////////
