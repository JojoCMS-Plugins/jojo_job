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
 * @author  Tom Dale <tom@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$table = 'job';
$o = 1;

$default_td[$table]['td_displayfield'] = "CONCAT(jb_title, ' - ', jb_location)";
$default_td[$table]['td_parentfield'] = '';
$default_td[$table]['td_rolloverfield'] = 'date';
$default_td[$table]['td_orderbyfields'] = 'date DESC, jb_title';
$default_td[$table]['td_topsubmit'] = 'yes';
$default_td[$table]['td_filter'] = 'yes';
$default_td[$table]['td_deleteoption'] = 'yes';
$default_td[$table]['td_menutype'] = 'tree';
$default_td[$table]['td_categoryfield'] = 'category';
$default_td[$table]['td_categorytable'] = 'jobcategory';
$default_td[$table]['td_group1'] = '';
$default_td[$table]['td_help'] = 'Job Listings are managed from here. Depending on the exact configuration, the most recent 5 jobs may be shown on the homepage or sidebar, or they may be listed only on the jobs page. All Jobs have their own "full info" page, which has a unique URL for the search engines. This is based on the title of the job, so please do not change the title of a job unless absolutely necessary, as the PageRank of the job may suffer. The system will comfortably take many hundreds of jobs, but you may want to manually delete anything that is no longer relevant, or correct.';
$default_td[$table]['td_golivefield'] = 'jb_livedate';
$default_td[$table]['td_expiryfield'] = 'jb_expirydate';
$default_td[$table]['td_languagefield'] = 'language';
$default_td[$table]['td_plugin'] = 'Jojo_job';

// Category Field
$default_fd[$table]['category'] = array(
        'fd_name' => "Page",
        'fd_type' => "dblist",
        'fd_options' => "jobcategory",
        'fd_default' => "0",
        'fd_size' => "20",
        'fd_help' => "The page the Job belongs to",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

//Job ID
$field = 'jobid';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'readonly';
$default_fd[$table][$field]['fd_help'] = 'A unique ID, automatically assigned by the system';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Title
$field = 'jb_title';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_size'] = '80';
$default_fd[$table][$field]['fd_help'] = 'Title of the Job. This will be used for the URL, headings and titles. Because the URL is based on this field, avoid changing this if possible.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

// SEO Title Field
$default_fd[$table]['jb_seotitle'] = array(
        'fd_name' => "SEO Title",
        'fd_type' => "text",
        'fd_options' => "seotitle",
        'fd_size' => "80",
        'fd_help' => "Title of the Job - it may be worth including your search phrase at the beginning of the title to improve rankings for that phrase.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

//Location
$field = 'jb_location';
$default_fd[$table][$field]['fd_name'] = 'Job Location';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '80';
$default_fd[$table][$field]['fd_help'] = 'eg Auckland';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Reference Code
$field = 'jb_ref';
$default_fd[$table][$field]['fd_name'] = 'Job Ref #';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '80';
$default_fd[$table][$field]['fd_help'] = '';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';


//Date
$field = 'date';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'unixdate';
$default_fd[$table][$field]['fd_default'] = 'now';
$default_fd[$table][$field]['fd_help'] = 'Date the job was published (defaults to Today)';
$default_fd[$table][$field]['fd_mode'] = 'standard';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Description
$field = 'jb_desc';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_size'] = '80';
$default_fd[$table][$field]['fd_help'] = 'A one sentence description of the job. Used for rollover text on links, which enhances usability';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Body Code
$field = 'jb_body_code';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_name'] = 'Body Copy';
$default_fd[$table][$field]['fd_type'] = 'texteditor';
$default_fd[$table][$field]['fd_options'] = 'jb_body';
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = 'The body of the job in either HTML or BBCode.';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Body
$field = 'jb_body';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'hidden';
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = 'The body of the job in HTML. Try to summarise the job in the first paragraph as this will be used for the snippet';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Contact Email
$field = 'jb_contact';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_name'] = 'Email to';
$default_fd[$table][$field]['fd_type'] = 'email';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size'] = '80';
$default_fd[$table][$field]['fd_help'] = 'The contact email address for the job';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Contact Details
$field = 'jb_contactdetails';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'textarea';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_rows'] = '7';
$default_fd[$table][$field]['fd_cols'] = '40';
$default_fd[$table][$field]['fd_help'] = 'The contact address for the job';
$default_fd[$table][$field]['fd_mode'] = 'advanced';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

//Online submission
$field = 'jb_submission';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'radio';
$default_fd[$table][$field]['fd_default'] = 'yes';
$default_fd[$table][$field]['fd_name'] = 'Include CV submit form';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_help'] = 'Display a form for submitting a CV online';
$default_fd[$table][$field]['fd_mode'] = 'basic';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

// Sticky Field
$default_fd[$table]['jb_sticky'] = array(
        'fd_name' => "Sticky",
        'fd_type' => "radio",
        'fd_options' => "1:yes\n0:no",
        'fd_default' => "0",
        'fd_help' => "Keep this article visible and display first even when more recent ones are available",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

//Image
$field = 'jb_image';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'fileupload';
$default_fd[$table][$field]['fd_help'] = 'An image for the job, if  available';
$default_fd[$table][$field]['fd_mode'] = 'standard';
$default_fd[$table][$field]['fd_tabname'] = 'Content';

// URL Field
$default_fd[$table]['jb_url'] = array(
        'fd_name' => "URL",
        'fd_type' => "internalurl",
        'fd_size' => "20",
        'fd_help' => "A customized URL - leave blank to create a URL from the title of the job",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// META Description Field
$default_fd[$table]['jb_metadesc'] = array(
        'fd_name' => "META Description",
        'fd_type' => "textarea",
        'fd_options' => "metadescription",
        'fd_rows' => "4",
        'fd_cols' => "60",
        'fd_help' => "A META Description for the job. By default, a meta description is auto-generated, but hand-written descriptions are always better. This is a recommended field.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Language Field
$default_fd['job']['language'] = array(
        'fd_name' => "Language/Country",
        'fd_type' => "dblist",
        'fd_options' => "lang_country",
        'fd_default' => "en",
        'fd_size' => "20",
        'fd_help' => "The language or country section this belongs to",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );


//TAGS TAB
$o = 1;

//Tags
$field = 'jb_tags';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_name'] = 'Tags';
$default_fd[$table][$field]['fd_type'] = 'tag';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_options'] = 'jojo_job';
$default_fd[$table][$field]['fd_tabname'] = 'Tags';
$default_fd[$table][$field]['fd_help'] = 'A list of words describing the job';
$default_fd[$table][$field]['fd_mode'] = 'standard';

//SCHEDULING TAB
$o = 1;
//Go Live Date
$field = 'jb_livedate';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_name'] = 'Go Live Date';
$default_fd[$table][$field]['fd_type'] = 'unixdate';
$default_fd[$table][$field]['fd_default'] = '';
$default_fd[$table][$field]['fd_help'] = 'The job will not appear on the site until this date';
$default_fd[$table][$field]['fd_mode'] = 'standard';
$default_fd[$table][$field]['fd_tabname'] = 'Scheduling';

//Expiry Date
$field = 'jb_expirydate';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_name'] = 'Expiry Date';
$default_fd[$table][$field]['fd_type'] = 'unixdate';
$default_fd[$table][$field]['fd_default'] = '';
$default_fd[$table][$field]['fd_help'] = 'The page will be removed from the site after this date';
$default_fd[$table][$field]['fd_mode'] = 'standard';
$default_fd[$table][$field]['fd_tabname'] = 'Scheduling';


/* Job Categories */

$table = 'jobcategory';
$o=0;
$default_td[$table] = array(
        'td_name' => "jobcategory",
        'td_primarykey' => "jobcategoryid",
        'td_displayfield' => "pageid",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_help' => "Job page options are managed from here.",
        'td_plugin' => "Jojo_job",
    );


/* Content Tab */

// categoryid Field
$default_fd[$table]['jobcategoryid'] = array(
        'fd_name' => "id",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// PageID Field
$default_fd[$table]['pageid'] = array(
        'fd_name' => "Page",
        'fd_type' => "dbpluginpagelist",
        'fd_options' => "jojo_plugin_jojo_job",
        'fd_readonly' => "1",
        'fd_default' => "1",
        'fd_help' => "The page on the site used for this category.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// PageURL Field
$default_fd[$table]['jc_url'] = array(
        'fd_name' => "URL",
        'fd_type' => "internalurl",
        'fd_readonly' => "1",
        'fd_help' => "The page on the site used for this category.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );


// Sortby Field
$default_fd[$table]['sortby'] = array(
        'fd_name' => "Sortby",
        'fd_type' => "radio",
        'fd_options' => "jb_title asc:Title\njb_date desc:Job Date\njb_livedate desc:Go Live Date",
        'fd_default' => "jb_date desc",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );


// Thumbnail sizing Field
$default_fd[$table]['thumbnail'] = array(
        'fd_name' => "Thumbnail sizing",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "s150",
        'fd_help' => "image thumbnail sizing in index eg: 150x200, h200, v4000",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Show Rss link Field
$default_fd[$table]['rsslink'] = array(
        'fd_name' => "Publish to Rss",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );
