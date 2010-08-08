<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_job
 */

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_job'       => 'Jobs - Listing and View',
        );

/* Register URI handlers */
Jojo::registerURI(null, 'Jojo_Plugin_Jojo_job', 'isUrl');

/* Job RSS icon filter */
Jojo::addFilter('rssicon', 'rssicon', 'jojo_job');

/* Sitemap filter */
Jojo::addFilter('jojo_sitemap', 'sitemap', 'jojo_job');

/* XML Sitemap filter */
Jojo::addFilter('jojo_xml_sitemap', 'xmlsitemap', 'jojo_job');

/* Search Filter */
if (class_exists('Jojo_Plugin_Jojo_search')) {
    Jojo::addFilter('jojo_search', 'search', 'jojo_job');
}
/* Content Filter */
Jojo::addFilter('content', 'removesnip', 'jojo_job');

/* capture the button press in the admin section */
Jojo::addHook('admin_action_after_save_page', 'admin_action_after_save_page', 'jojo_job');
Jojo::addHook('admin_action_after_save_jobcategory', 'admin_action_after_save_jobcategory', 'jojo_job');

$_options[] = array(
    'id'          => 'job_tag_cloud_minimum',
    'category'    => 'Jobs',
    'label'       => 'Minimum tags to form cloud',
    'description' => 'On the job pages, a tag cloud will be formed from tags if this number of tags is met (otherwise a plain text list of tags is shown). Set to zero to always use the plain text list.',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id' => 'job_dateformat',
    'category' => 'Jobs',
    'label' => 'Date Format',
    'description' => 'Date display format',
    'type' => 'text',
    'default' => '%d %b',
    'options' => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_show_date',
    'category'    => 'Jobs',
    'label'       => 'Show Date on posts',
    'description' => 'Shows the publish date at the top of each job page',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'jobsperpage',
    'category'    => 'Jobs',
    'label'       => 'Jobs per page on index',
    'description' => 'The number of jobs to show on the Jobs index page before paginating',
    'type'        => 'integer',
    'default'     => '40',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_next_prev',
    'category'    => 'Jobs',
    'label'       => 'Show Next / Previous links',
    'description' => 'Show a link to the next and previous job at the top of each job page',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id' => 'jobs_no_jobs',
    'category' => 'Jobs',
    'label' => 'No Jobs text',
    'description' => 'Text to display when there are no jobs to display - something brief but encouraging.',
    'type' => 'textarea',
    'default' => 'We have no advertised vacancies at present, but as new opportunities arise on a regular basis we are always looking for talented people.  If you are interested, refer to the Contact page to make an enquiry.',
    'options' => '',
    'plugin' => 'jojo_job'
);

$_options[] = array(
    'id' => 'jobs_use_tags',
    'category' => 'Jobs',
    'label' => 'Display Tags',
    'description' => 'If YES, Job listings will display a job\'s tags at the bottom of the listing. Only really useful to users if there are many jobs and they are well tagged.',
    'type' => 'radio',
    'default' => 'no',
    'options' => 'yes,no',
    'plugin' => 'jojo_job'
);

$_options[] = array(
    'id' => 'jobs_external_rss',
    'category' => 'Jobs',
    'label' => 'External Jobs RSS URL',
    'description' => 'The external URL (eg Feedburner) for the Jobs RSS feed (leave blank to use the Jojo defaults)',
    'type' => 'text',
    'default' => '',
    'options' => '',
    'plugin' => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_rss_num_jobs',
    'category'    => 'Jobs',
    'label'       => 'Number of RSS jobs',
    'description' => 'The number of jobs to be displayed in the RSS feed (more jobs will use more bandwidth))',
    'type'        => 'integer',
    'default'     => '15',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_full_rss_description',
    'category'    => 'Jobs',
    'label'       => 'Full Job RSS Description',
    'description' => 'If YES, a full copy of the job is provided in the RSS feed. If NO, the RSS feed only includes content before the snip filter tag.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_rss_truncate',
    'category'    => 'Jobs',
    'label'       => 'Job RSS default truncation',
    'description' => 'If Full Description is set to No above, truncate jobs with no embedded snip filter tag to this length',
    'type'        => 'integer',
    'default'     => '800',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_feed_source_link',
    'category'    => 'Jobs',
    'label'       => 'Append source link to RSS feed',
    'description' => 'Appends a source link to the bottom of the job in the RSS feed. This is to ensure scraper sites are providing a link back to the original job.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id' => 'sitedesc',
    'category' => 'Jobs',
    'label' => 'Site description',
    'description' => 'A one sentence unique description of what the site is about. Included in RSS feeds.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_job'
);


$_options[] = array(
    'id'          => 'job_last_updated',
    'category'    => 'System',
    'label'       => 'Jobs last updated',
    'description' => 'The timestamp of when the last update was made to a job.',
    'type'        => 'integer',
    'default'     => '1',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_num_sidebar_jobs',
    'category'    => 'Jobs',
    'label'       => 'Number of job teasers to show in the sidebar',
    'description' => 'The number of jobs to be displayed as snippets in a teaser box on other pages - set to 0 to disable',
    'type'        => 'integer',
    'default'     => '3',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_sidebar_randomise',
    'category'    => 'Jobs',
    'label'       => 'Randmomise selection of teasers out of',
    'description' => 'Pick the sidebar jobs from a larger group, shuffle them, and then slice them back to the original number so that sidebar content is more dynamic  - set to 0 to disable',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_sidebar_categories',
    'category'    => 'Jobs',
    'label'       => 'Job teasers by category',
    'description' => 'Generate sidebar list from all jobs and also create a list from each category',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_sidebar_exclude_current',
    'category'    => 'Jobs',
    'label'       => 'Exclude current job from list',
    'description' => 'Exclude the job from the sidebar list when on that jobs page',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_inplacesitemap',
    'category'    => 'Jobs',
    'label'       => 'Jobs sitemap location',
    'description' => 'Show artciles as a separate list on the site map, or in-place on the page list',
    'type'        => 'radio',
    'default'     => 'separate',
    'options'     => 'separate,inplace',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_meta_description',
    'category'    => 'Jobs',
    'label'       => 'Dynamic job meta description',
    'description' => 'A dynamically built meta description template to use for jobs, which will assist with SEO. Variables to use are [job], [site].',
    'type'        => 'textarea',
    'default'     => '[job], an job on [site] - Read all about [job] here.',
    'options'     => '',
    'plugin'      => 'jojo_job'
);

$_options[] = array(
    'id'          => 'job_sticky',
    'category'    => 'Jobs',
    'label'       => 'Sticky Jobs',
    'description' => 'Make sticky jobs stay on top of sidebar lists.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_job'
);
