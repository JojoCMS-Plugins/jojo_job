<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Harvey Kane <code@ragepank.com>
 * Copyright 2007 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <code@gardyneholt.co.nz>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

/* Jobs */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='jojo_plugin_jojo_job'");
if (count($data) == 0) {
    echo "jojo_jobs: Adding <b>Jobs</b> Page to menu<br />";
    $jobpageid = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Jobs', pg_link='jojo_plugin_jojo_job', pg_url='jobs'");
    Jojo::insertQuery("INSERT INTO {jobcategory} SET pageid=?", array($jobpageid));
}

/* Edit Jobs */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/job'");
if (count($data) == 0) {
    echo "jojo_jobs: Adding <b>Edit Jobs</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Edit Jobs', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/job', pg_parent=". JOJO::clean($_ADMIN_CONTENT_ID).", pg_order=2");
}

/* Edit Job Categories */
$data = Jojo::selectRow("SELECT * FROM {page} WHERE pg_url='admin/edit/jobcategory'");
if (count($data) == 0) {
    $parent = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url='admin/edit/job'");
    echo "Jojo_Plugin_Jojo_job: Adding <b>Job Page Options</b> Page to Edit Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Job Categories', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/jobcategory', pg_parent=?, pg_order=3", $parent['pageid']);
}

/* Ensure there is a folder for uploading job images */
$res = Jojo::RecursiveMkdir(_DOWNLOADDIR . '/jobs');
if ($res === true) {
    echo "jojo_jobs: Created folder: " . _DOWNLOADDIR . '/jobs';
} elseif($res === false) {
    echo 'jojo_jobs: Could not automatically create ' .  _DOWNLOADDIR . '/jobs' . 'folder on the server. Please create this folder and assign 777 permissions.';
}
