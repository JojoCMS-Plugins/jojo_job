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

class Jojo_Plugin_Jojo_job extends Jojo_Plugin
{

    /* Gets $num items sorted by startdate (asc) for use on homepages and sidebars */
    static function getItems($num=false, $start = 0, $categoryid='all', $sortby='jb_date desc', $exclude=false, $include=false, $filled=false) {
        global $page;
        $now = time();
        $language = _MULTILANGUAGE ? (!empty($page->page['pg_language']) ? $page->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en')) : '';
        if (is_array($categoryid)) {
             $categoryquery = " AND category IN ('" . implode("','", $categoryid) . "')";
        } else {
            $categoryquery = is_numeric($categoryid) ? " AND category = '$categoryid'" : '';
        }
        /* if calling page is an job, Get current job, exclude from the list and up the limit by one */
        $exclude = ($exclude && Jojo::getOption('job_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_job' && Jojo::getFormData('id')) ? Jojo::getFormData('id') : '';
        if ($num && $exclude) $num++;
        $query  = "SELECT i.*, c.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_language, pg_livedate, pg_expirydate";
        $query .= " FROM {job} i";
        $query .= " LEFT JOIN {jobcategory} c ON (i.category=c.jobcategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid)";
        $query .= " WHERE 1" . $categoryquery;
        $query .= (_MULTILANGUAGE && $categoryid == 'all' && $include != 'alllanguages') ? " AND (pg_language = '$language')" : '';
        $query .= $num ? " ORDER BY $sortby LIMIT $start,$num" : '';
        $items = Jojo::selectQuery($query);
        $items = self::cleanItems($items, $exclude, $include, $filled);
        if (!$num)  $items = self::sortItems($items, $sortby);
        return $items;
    }

    static function getItemsById($ids = false, $sortby='jb_date desc') {
        $query  = "SELECT i.*, c.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_language";
        $query .= " FROM {job} i";
        $query .= " LEFT JOIN {jobcategory} c ON (i.category=c.jobcategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid)";
        $query .=  is_array($ids) ? " WHERE jobid IN ('". implode("',' ", $ids) . "')" : " WHERE jobid=$ids";
        $items = Jojo::selectQuery($query);
        $items = self::cleanItems($items);
        $items = is_array($ids) ? self::sortItems($items, $sortby) : $items[0];
        return $items;
    }

    /* clean items for output */
    static function cleanItems($items, $exclude=false, $include=false, $filled=false) {
        global $_USERGROUPS;
        $now    = time();
        foreach ($items as $k=>&$i){
            $pagedata = Jojo_Plugin_Core::cleanItems(array($i), $include);
            if (!$pagedata || $i['jb_livedate']>$now || (!empty($i['jb_expirydate']) && $i['jb_expirydate']<$now) || (!empty($i['jobid']) && $i['jobid']==$exclude)  || (!empty($i['jb_url']) && $i['jb_url']==$exclude) || (!$filled && $i['filled'])) {
                unset($items[$k]);
                continue;
            }
            $i['id']           = $i['jobid'];
            $i['title']        = htmlspecialchars($i['jb_title'], ENT_COMPAT, 'UTF-8', false);
            $i['seotitle']        = isset($i['jb_seotitle']) ? htmlspecialchars($i['jb_seotitle'], ENT_COMPAT, 'UTF-8', false): $i['title'];
            $i['description']        = htmlspecialchars($i['jb_desc'], ENT_COMPAT, 'UTF-8', false);
            // Snip for the index description
            $i['bodyplain'] = array_shift(Jojo::iExplode('[[snip]]', $i['jb_body']));
            /* Strip all tags and template include code ie [[ ]] */
            $i['bodyplain'] = preg_replace('/\[\[.*?\]\]/', '',  trim(strip_tags($i['bodyplain'])));
            $i['location']    = htmlspecialchars($i['jb_location'], ENT_COMPAT, 'UTF-8', false);
            $i['ref']    = htmlspecialchars($i['jb_ref'], ENT_COMPAT, 'UTF-8', false);
            $i['image'] = !empty($i['jb_image']) ? 'jobs/' . $i['jb_image'] : '';
            $i['url']          = self::getUrl($i['jobid'], $i['jb_url'], $i['jb_title'], $i['language'], $i['category']);
            $i['dateadded'] = strftime( Jojo::getOption('job_dateformat', '%d %b'), $i['date']);
            $i['pagetitle'] = !empty($i['pg_menutitle']) ? htmlspecialchars($i['pg_menutitle'], ENT_COMPAT, 'UTF-8', false) : htmlspecialchars($i['pg_title'], ENT_COMPAT, 'UTF-8', false);
            $i['pageurl']   = (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($i['pg_language'], true) : '') . (!empty($i['pg_url']) ? $i['pg_url'] : $i['pageid'] . '/' .  Jojo::cleanURL($i['pg_title'])) . '/';
            $i['plugin']     = 'jojo_job';
            unset($items[$k]['jb_body_code']);
        }
        return $items;
    }

    /* sort items for output */
    static function sortItems($items, $sortby=false) {
        if ($sortby) {
            $order = "livedate";
            $reverse = false;
            switch ($sortby) {
              case "jb_livedate desc":
                $order="livedate";
                $reverse = true;
                break;
              case "jb_date desc":
                $order="date";
                $reverse = true;
                break;
              case "jb_title asc":
                $order="title";
                break;
            }
            usort($items, array('Jojo_Plugin_Jojo_job', $order . 'sort'));
            $items = $reverse ? array_reverse($items) : $items;
        }
        return $items;
    }

    private static function livedatesort($a, $b)
    {
         if ($a['jb_livedate']) {
            return strnatcasecmp($a['jb_livedate'],$b['jb_livedate']);
         }
    }

    private static function datesort($a, $b)
    {
         if ($a['date']) {
            return strnatcasecmp($a['date'],$b['date']);
         }
    }

    private static function titlesort($a, $b)
    {
         if ($a['title']) {
            return strnatcasecmp($a['title'],$b['title']);
         }
    }

    static function getUrl($id=false, $url=false, $title=false, $language=false, $category=false )
    {
        if (_MULTILANGUAGE) {
            $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
            $multilangstring = Jojo::getMultiLanguageString($language, false);
        }
        /* URL specified */
        if (!empty($url)) {
            $fullurl = (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix('', $category) . '/' . $url . '/';
            return $fullurl;
         }
        /* ID + title specified */
        if ($id && !empty($title)) {
            $fullurl = (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix('', $category) . '/' . $id . '/' .  Jojo::cleanURL($title) . '/';
          return $fullurl;
        }
        /* use the ID to find either the URL or title */
        if ($id) {
            $item = Jojo::selectRow("SELECT jb_title, language, category FROM {job} WHERE jobid = ?", array($id));
             if ($item) {
                return self::getUrl($id, '', $item['jb_title'], $item['language'], $item['category']);
            }
         }
        /* No item matching the ID supplied or no ID supplied */
        return false;
    }

    function _getContent()
    {
        global $_USERGROUPS, $_USERID, $smarty;
        $content = array();
        
        if (_MULTILANGUAGE) {
            $language = !empty($this->page['pg_language']) ? $this->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
            $multilangstring = Jojo::getMultiLanguageString($language, false);
            $smarty->assign('multilangstring', $multilangstring);
        }
        /* Are we looking at an job or the index? */
        $id = Jojo::getFormData('id',        0);
        $url       = Jojo::getFormData('url',      '');
        $action    = Jojo::getFormData('action',   '');
        $pageid = $this->page['pageid'];
        $categorydata =  Jojo::selectRow("SELECT * FROM {jobcategory} WHERE pageid = ?", $pageid);
        $categorydata['type'] = isset($categorydata['type']) ? $categorydata['type'] : 'normal';
        if ($categorydata['type']=='index') {
            $categoryid = 'all';
        } elseif ($categorydata['type']=='parent') {
            $childcategories = Jojo::selectQuery("SELECT jobcategoryid FROM {page} p  LEFT JOIN {jobcategory} c ON (c.pageid=p.pageid) WHERE pg_parent = ? AND pg_link = 'jojo_plugin_jojo_job'", $pageid);
            foreach ($childcategories as $c) {
                $categoryid[] = $c['jobcategoryid'];
            }
            $categoryid[] = $categorydata['jobcategoryid'];
        } else {
            $categoryid = $categorydata['jobcategoryid'];
        }
        $sortby = $categorydata ? $categorydata['sortby'] : '';
        
        $jobs = self::getItems('', '', $categoryid, $sortby, '', '', $filled=true);

        if ($action == 'rss') {
            $rssfields = array(
                'pagetitle' => $this->page['pg_title'],
                'pageurl' => _SITEURL . '/' . (_MULTILANGUAGE ? $multilangstring : '') . $this->page['pg_url'] . '/',
                'title' => 'title',
                'body' => 'jb_body',
                'url' => 'url',
                'date' => 'date',
                'datetype' => 'unix'
            );
            $items = array_slice($jobs, 0, Jojo::getOption('rss_num_items', 15));
            Jojo::getFeed($items, $rssfields);
        }

        if ($id || !empty($url)) {

            /* find the current, next and previous */
            $job = '';
            $prevjob = array();
            $nextjob = array();
            $next = false;
            foreach ($jobs as $a) {
                if (!empty($url) && $url==$a['jb_url']) {
                    $job = $a;
                    $next = true;
               } elseif ($id==$a['id']) {
                    $job = $a;
                    $next = true;
                } elseif ($next==true) {
                    $nextjob = $a;
                     break;
                } else {
                    $prevjob = $a;
                }
            }

            /* If the job can't be found, return a 404 */
            if (!$job) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            }

            /* Get the specific job */
            $id = $job['id'];
            $job['datecloses'] = strftime('%d %B %Y', $job['jb_expirydate']);

            /* If this an application for a job? */
            if (Util::getFormData('submit')) {
                /* Yes, create email */
                /* Check for form injection attempts */
                Jojo::noFormInjection();
                /* Setup Mailer */
                require_once _PLUGINDIR . '/jojo_job/external/swift/swift_required.php';
                $transport = Swift_SmtpTransport::newInstance(Jojo::getOption('smtp_mail_host'), Jojo::getOption('smtp_mail_port'))
                  ->setUsername(Jojo::getOption('smtp_mail_user'))
                  ->setPassword(Jojo::getOption('smtp_mail_pass'));
                $mailer = Swift_Mailer::newInstance($transport);
                
                /* Send message to person registering */
                $message = Swift_Message::newInstance()
                  ->setSubject('Job application on '.Jojo::getOption('sitetitle').' website')
                  ->setFrom(array(
                        Jojo::either($job['jb_contact'], _CONTACTADDRESS, _WEBMASTERADDRESS) => Jojo::getOption('sitetitle')
                        ))
                  ->setTo(array(
                        Jojo::getFormData('form_Email') => Jojo::getFormData('form_Name')
                        ))
                  ->setBody( 'Thank you for your application. We endeavour to respond within 24 hours during the business week.');
                $result = $mailer->send($message);
                
                /* Send message to admin */
                $body_text = "";
                $body_text .=  $job['title'] . ' - ' . $job['location'] . "\n";
                $body_text .=  !empty($job['ref']) ? $job['ref'] . "\n\n" : "\n";
                $body_text .= "Name: " . Jojo::getFormData('form_Name') . "\n";
                $body_text .= "Phone:      " . Jojo::getFormData('form_Phone') . "\n";
                $body_text .= "Email:      " . Jojo::getFormData('form_Email') . "\n";
                $body_text .= "Message:      " . Jojo::getFormData('form_Message') . "\n";
                $message = Swift_Message::newInstance()
                  ->setSubject('Job application on '.Jojo::getOption('sitetitle').' website')
                  ->setTo(array(
                        Jojo::either($job['jb_contact'], _CONTACTADDRESS, _WEBMASTERADDRESS) => Jojo::getOption('sitetitle')
                        ))
                  ->setFrom(array(
                        Jojo::getFormData('form_Email') => Jojo::getFormData('form_Name')
                        ))
                  ->setBody($body_text);
                $filename = isset($_FILES['jb_FILE_cv']['name']) ? $_FILES['jb_FILE_cv']['name'] : '';
                if ($filename) {
                  $message->attach(Swift_Attachment::fromPath($_FILES['jb_FILE_cv']['tmp_name'])->setFilename($filename));
                }
                $result = $mailer->send($message);
 
                if ($result) {
                    $smarty->assign('message', 'Thank you for your application. We endeavour to respond within 24 hours during the business week.');
                }
            }

            /* calculate the next and previous jobs */
            if (Jojo::getOption('job_next_prev') == 'yes') {
                if (!empty($nextjob)) {
                    $smarty->assign('nextjob', $nextjob);
                }
                if (!empty($prevjob)) {
                    $smarty->assign('prevjob', $prevjob);
                }
            }

            /* Ensure the tags class is available */
            if (class_exists('Jojo_Plugin_Jojo_Tags')) {
                /* Split up tags for display */
                $tags = Jojo_Plugin_Jojo_Tags::getTags('jojo_job', $id);
                $smarty->assign('tags', $tags);

                /* generate tag cloud of tags belonging to this job */
                $job_tag_cloud_minimum = Jojo::getOption('job_tag_cloud_minimum');
                if (!empty($job_tag_cloud_minimum) && ($job_tag_cloud_minimum < count($tags))) {
                    $itemcloud = Jojo_Plugin_Jojo_Tags::getTagCloud('', $tags);
                    $smarty->assign('itemcloud', $itemcloud);
                }
                /* get related jobs if tags plugin installed and option enabled */
                $numrelated = Jojo::getOption('job_num_related');
                if ($numrelated) {
                    $related = Jojo_Plugin_Jojo_Tags::getRelated('jojo_job', $id, $numrelated, 'jojo_job'); //set the last argument to 'jojo_job' to restrict results to only jobs
                    $smarty->assign('related', $related);
                }
            }

             /* Add job breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = $job['title'];
            $breadcrumb['rollover']           = $job['description'];
            $breadcrumb['url']                = $job['url'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;

            /* Assign job content to Smarty */
            $smarty->assign('job', $job);

            /* Prepare fields for display */
            $content['title']            = $job['title'] . (!empty($job['location'])  ? ' - ' . $job['location'] : '' );
            $content['seotitle']         = Jojo::either($job['jb_seotitle'], $job['title']) . (!empty($job['location'])  ? ' - ' . $job['location'] : '' ) . (!empty($job['category'])  ? ' | ' . $job['category'] : '' );
            $content['breadcrumbs']      = $breadcrumbs;
            if (!empty($job['jb_metadesc'])) {
                $content['meta_description'] = $job['jb_metadesc'];
            } else {
                $meta_description_template = Jojo::getOption('job_meta_description', '[job], on [site] - [desc]');
                $content['meta_description'] = str_replace(array('[job]', '[site]', '[desc]'), array($job['title'], Jojo::getOption('sitetitle'), (!empty($job['description']) ? $job['description'] : substr($job['bodyplain'], 0, 100) . '...')), $meta_description_template);
            }
            $content['metadescription']  = $content['meta_description'];
        } else {
            /* Job index section */
            if (empty($jobs)) {
                $smarty->assign('nojob', Jojo::getOption('jobs_no_jobs', 'There are currently no upcoming jobs, please check back later.'));
            } else {
                $pagenum = Jojo::getFormData('pagenum', 1);
                if ($pagenum[0] == 'p') {
                    $pagenum = substr($pagenum, 1);
                }
                $smarty->assign('job','');    
                /* get number of jobs for pagination */
                $jobsperpage = Jojo::getOption('jobsperpage', 40);
                $start = ($jobsperpage * ($pagenum-1));
                $numjobs = count($jobs);
                $numpages = ceil($numjobs / $jobsperpage);
                /* calculate pagination */
                if ($numpages == 1) {
                    $pagination = '';
                } elseif ($numpages == 2 && $pagenum == 2) {
                    $pagination = sprintf('<a href="%s/p1/">previous...</a>', (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix('job', $categoryid) );
                } elseif ($numpages == 2 && $pagenum == 1) {
                    $pagination = sprintf('<a href="%s/p2/">more...</a>', (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix('job', $categoryid) );
                } else {
                    $pagination = '<ul>';
                    for ($p=1;$p<=$numpages;$p++) {
                        $url = (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix('job', $categoryid) . '/';
                        if ($p > 1) {
                            $url .= 'p' . $p . '/';
                        }
                        if ($p == $pagenum) {
                            $pagination .= '<li>&gt; Page '.$p.'</li>'. "\n";
                        } else {
                            $pagination .= '<li>&gt; <a href="'.$url.'">Page '.$p.'</a></li>'. "\n";
                        }
                    }
                    $pagination .= '</ul>';
                }
                $smarty->assign('pagination', $pagination);
                $smarty->assign('pagenum', $pagenum);

                /* get job content and assign to Smarty */
                $jobs = array_slice($jobs, $start, $jobsperpage);
                $smarty->assign('jobs', $jobs);
            }
            /* clear the meta description to avoid duplicate content issues */
            $content['metadescription'] = '';
        }

        $content['content'] = $smarty->fetch('jojo_job.tpl');
        return $content;
    }


    static function getPluginPages($for=false, $language=false)
    {
        $items =  Jojo::selectQuery("SELECT c.*, p.pageid, pg_title, pg_url, pg_language, pg_livedate, pg_expirydate, pg_status, pg_sitemapnav, pg_xmlsitemapnav  FROM {jobcategory} c LEFT JOIN {page} p ON (c.pageid=p.pageid) ORDER BY pg_language, pg_parent");
        // use core function to clean out any pages based on permission, status, expiry etc
        $items =  Jojo_Plugin_Core::cleanItems($items, $for);
        foreach ($items as $k=>&$i){
            if ($language && $i['pg_language']!=$language) {
                unset($items[$k]);
                continue;
            }
        }
        return $items;
    }

    static function _getPrefix($for='job', $categoryid=false) {
        $cacheKey = $for;
        $cacheKey .= ($categoryid) ? $categoryid : 'false';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        /* Cache some stuff */
        $res = Jojo::selectRow("SELECT p.pageid, pg_title, pg_url FROM {page} p LEFT JOIN {jobcategory} c ON (c.pageid=p.pageid) WHERE `jobcategoryid` = '$categoryid'");
        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . $res['pg_title'];
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }

    static function getPrefixById($id=false) {
        if ($id) {
            $data = Jojo::selectRow("SELECT category FROM {job} WHERE jobid = ?", array($id));
            if ($data) {
                $prefix = self::_getPrefix('', $data['category']);
                return $prefix;
            }
        }
        return false;
    }

    function getCorrectUrl()
    {
        global $page;
        $language  = $page->page['pg_language'];
        $id = Jojo::getFormData('id',     0);
        $url       = Jojo::getFormData('url',    '');
        $action    = Jojo::getFormData('action', '');
        $pagenum   = Jojo::getFormData('pagenum', 1);

        $data = Jojo::selectRow("SELECT jobcategoryid FROM {jobcategory} WHERE pageid=?", $page->page['pageid']);
        $categoryid = !empty($data['jobcategoryid']) ? $data['jobcategoryid'] : '';

        if ($pagenum[0] == 'p') {
            $pagenum = substr($pagenum, 1);
        }

        $correcturl = self::getUrl($id, $url, null, $language, $categoryid);
        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }

        /* index with pagination */
        if ($pagenum > 1) return parent::getCorrectUrl() . 'p' . $pagenum . '/';

        if ($action == 'rss') return parent::getCorrectUrl() . 'rss/';

        /* job index - default */
        return parent::getCorrectUrl();
    }

    static public function isUrl($uri)
    {
        $prefix = false;
        $getvars = array();
        /* Check the suffix matches and extract the prefix */
       if ($uribits = Jojo_Plugin::isPluginUrl($uri)) {
            $prefix = $uribits['prefix'];
            $getvars = $uribits['getvars'];
        } else {
            return false;
        }
        /* Check the prefix matches */
        if ($res = self::checkPrefix($prefix)) {
            /* If full uri matches a prefix it's an index page so ignore it and let the page plugin handle it */
            if (self::checkPrefix(trim($uri, '/'))) return false;
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if a prefix is an job prefix
     */
    static public function checkPrefix($prefix)
    {
        static $_prefixes, $categories;
        if (!isset($categories)) {
            /* Initialise cache */
            $categories = array(false);
            $categories = array_merge($categories, Jojo::selectAssoc("SELECT jobcategoryid, jobcategoryid as jobcategoryid2 FROM {jobcategory}"));
            $_prefixes = array();
        }
        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }
        /* Check everything */
        foreach($categories as $category) {
            $testPrefix = self::_getPrefix('job', $category);
            $_prefixes[$testPrefix] = true;
            if ($testPrefix == $prefix) {
                /* The prefix is good */
                return true;
            }
        }
        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }


    // Sync the category data over to the page table
    static function admin_action_after_save_jobcategory() {
        if (!Jojo::getFormData('fm_pageid', 0)) {
            // no pageid set for this category (either it's a new category or maybe the original page was deleted)
            self::sync_category_to_page();
       }
    }

    // Sync the category data over from the page table
    static function admin_action_after_save_page() {
        if (strtolower(Jojo::getFormData('fm_pg_link',    ''))=='jojo_plugin_jojo_job') {
           self::sync_page_to_category();
       }
    }

    static function sync_category_to_page() {
        // Get the category id (if an existing category being saved where the page has been deleted)
        $catid = Jojo::getFormData('fm_jobcategoryid', 0);
        if (!$catid) {
        // no id because this is a new category - shouldn't really be done this way, new categories should be added by adding a new page
            $cats = Jojo::selectQuery("SELECT jobcategoryid FROM {jobcategory} ORDER BY jobcategoryid");
            // grab the highest id (assumes this is the newest one just created)
            $cat = array_pop($cats);
            $catid = $cat['jobcategoryid'];
        }
        // add a new hidden page for this category and make up a title
            $newpageid = Jojo::insertQuery(
            "INSERT INTO {page} SET pg_title = ?, pg_link = ?, pg_url = ?, pg_parent = ?, pg_status = ?",
            array(
                'Orphaned jobs',  // Title
                'jojo_plugin_jojo_job',  // Link
                'orphaned-jobs',  // URL
                0,  // Parent - don't do anything smart, just put it at the top level for now
                'hidden' // hide new page so it doesn't show up on the live site until it's been given a proper title and url
            )
        );        
        // If we successfully added the page, update the category with the new pageid
        if ($newpageid) {
            jojo::updateQuery(
                "UPDATE {jobcategory} SET pageid = ? WHERE jobcategoryid = ?",
                array(
                    $newpageid,
                    $catid
                )
            );
       }
    return true;
    }

    static function sync_page_to_category() {
        // Get the list of categories and the page id if available
        $categories = jojo::selectAssoc("SELECT pageid AS id, pageid FROM {jobcategory}");
        $pageid = Jojo::getFormData('fm_pageid', 0);
        // if it's a new page it won't have an id in the form data, so get it from the title
        if (!$pageid) {
           $title = Jojo::getFormData('fm_pg_title', 0);
           $page =  Jojo::selectRow("SELECT pageid, pg_url FROM {page} WHERE pg_title= ? AND pg_link = ? AND pg_language = ?", array($title, Jojo::getFormData('fm_pg_link', ''), Jojo::getFormData('fm_pg_language', '')));
           $pageid = $page['pageid'];
        }
        // no category for this page id
        if (!count($categories) || !isset($categories[$pageid])) { 
            jojo::insertQuery("INSERT INTO {jobcategory} (pageid) VALUES ('$pageid')");
        }
        return true;
    }

    public static function sitemap($sitemap)
    {
        global $page;
        /* See if we have any job sections to display and find all of them */
        $indexes =  self::getPluginPages('sitemap');
        if (!count($indexes)) {
            return $sitemap;
        }
        
        if (Jojo::getOption('job_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the jobs section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = self::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

        $limit = 15;
        $jobsperpage = Jojo::getOption('jobsperpage', 40);
         /* Make sitemap trees for each jobs instance found */
        foreach($indexes as $k => $i){
            /* Set language */
            $language = (_MULTILANGUAGE && !empty($i['pg_language'])) ? $i['pg_language'] : '';
            $multilangstring = Jojo::getMultiLanguageString($language, false);
            /* Set category */
            $categoryid = $i['jobcategoryid'];
            $sortby = $i['sortby'];

            /* Create tree and add index and feed links at the top */
            $jobtree = new hktree();
            $indexurl = (_MULTILANGUAGE ? $multilangstring : '' ) . self::_getPrefix('job', $categoryid) . '/';
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $jobtree->addNode('index', 0, $i['pg_title'], $indexurl);
               $parent = 'index';
            }

            $jobs = self::getItems('', '', $categoryid, $sortby);
            $n = count($jobs);

            /* Trim items down to first page and add to tree*/
            $jobs = array_slice($jobs, 0, $jobsperpage);
            foreach ($jobs as $a) {
                $jobtree->addNode($a['id'], $parent, $a['title'], $a['url']);
            }

            /* Get number of pages for pagination */
            $numpages = ceil($n / $jobsperpage);
            /* calculate pagination */
            if ($numpages > 1) {
                for ($p=2; $p <= $numpages; $p++) {
                    $url = $indexurl .'p' . $p .'/';
                    $nodetitle = $i['pg_title'] . '  - page '. $p;
                    $jobtree->addNode('p' . $p, $parent, $nodetitle, $url);
                }
            }
            /* Add RSS link for the plugin page */
           $jobtree->addNode('rss', $parent, $i['pg_title'] . ' RSS Feed', $indexurl . 'rss/');

            /* Check for child pages of the plugin page */
            foreach (Jojo::selectQuery("SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_parent = '" . $i['pageid'] . "' AND pg_sitemapnav = 'yes'") as $c) {
                    if ($c['pg_url']) {
                        $jobtree->addNode($c['pageid'], $parent, $c['pg_title'], (_MULTILANGUAGE ? $multilangstring : '') . $c['pg_url'] . '/');
                    } else {
                        $jobtree->addNode($c['pageid'], $parent, $c['pg_title'], (_MULTILANGUAGE ? $multilangstring : '') . $c['pageid']  . '/' .  Jojo::cleanURL($c['pg_title']) . '/');
                    }
            }

            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix('job', $categoryid) . '/';
                $sitemap['pages']['tree'] = self::_sitemapAddInplace($sitemap['pages']['tree'], $jobtree->asArray(), $url);
            } else {
                if (_MULTILANGUAGE) {
                    $mldata = Jojo::getMultiLanguageData();
                    $lclanguage = $mldata['longcodes'][$language];
                }
                /* Add to the end */
                $sitemap["jobs$k"] = array(
                    'title' => $i['pg_title'] . ( _MULTILANGUAGE ? ' (' . ucfirst($lclanguage) . ')' : ''),
                    'tree' => $jobtree->asArray(),
                    'order' => 3 + $k,
                    'header' => '',
                    'footer' => '',
                    );
            }
        }
        return $sitemap;
    }

    static function _sitemapAddInplace($sitemap, $toadd, $url)
    {
        foreach ($sitemap as $k => $t) {
            if ($t['url'] == $url) {
                $sitemap[$k]['children'] = $toadd;
            } elseif (isset($sitemap[$k]['children'])) {
                $sitemap[$k]['children'] = self::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    static function _sitemapRemoveSelf($tree)
    {
        static $urls;

        if (!is_array($urls)) {
            $urls = array();
            $indexes =  self::getPluginPages('sitemap');
            if (count($indexes)==0) {
               return $tree;
            }
            foreach($indexes as $key => $i){
                $language = (_MULTILANGUAGE && !empty($i['pg_language'])) ? $i['pg_language'] : '';
                $multilangstring = Jojo::getMultiLanguageString($language, false);
                $urls[] = (_MULTILANGUAGE ? $multilangstring : '')  . self::_getPrefix('job', $i['jobcategoryid']) . '/';
                $urls[] = (_MULTILANGUAGE ? $multilangstring : '')  . self::_getPrefix('job', $i['jobcategoryid']) . '/rss/';
            }
        }

        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = self::_sitemapRemoveSelf($t['children']);
            }
        }
        return $tree;
    }

    /**
    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds job pages
     */
    static function xmlsitemap($sitemap)
    {
        /* Get jobs from database */
        $jobs = self::getjobs('', '', 'all', '', '', 'alllanguages');
        $now = time();
        $indexes =  self::getPluginPages('xmlsitemap');
        $ids=array();
        foreach ($indexes as $i) {
            $ids[$i['jobcategoryid']] = true;
        }
        /* Add jobs to sitemap */
        foreach($jobs as $k => $a) {
            // strip out jobs from expired pages
            if (!isset($ids[$a['category']])) {
                unset($jobs[$k]);
                continue;
            }
            $url = _SITEURL . '/'. $a['url'];
            $lastmod = '';
            $priority = 0.6;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }
        /* Return sitemap */
        return $sitemap;
    }

    /**
     * Remove Snip
     * Removes any [[snip]] tags leftover in the content before outputting
     */
    static function removesnip($data)
    {
        $data = str_ireplace('[[snip]]','',$data);
        return $data;
    }

   /**
     * RSS Icon filter
     * Places the RSS feed icon in the head of the document, sitewide
     */
    static function rssicon($data)
    {
        global $page;
        $link = Jojo::getOption('rss_external_url');
        if ($link) {
            $data['jobs'] =  $link;
        }
        /* add RSS feeds for each page */
        $categories =  self::getPluginPages('', (_MULTILANGUAGE ? $page->page['pg_language'] : ''));
        foreach ($categories as $c) {
            $prefix =  self::_getPrefix('', $c['jobcategoryid']) . '/rss/';
            if ($prefix && $c['rsslink']) {
                $data[$c['pg_title']] = _SITEURL . '/' .  (_MULTILANGUAGE ? Jojo::getMultiLanguageString($c['pg_language'], false) : '') . $prefix;
            }
        }
        return $data;
    }


    /**
     * Site Search
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        $searchfields = array(
            'plugin' => 'jojo_job',
            'table' => 'job',
            'idfield' => 'jobid',
            'languagefield' => 'language',
            'primaryfields' => 'jb_title, jb_location',
            'secondaryfields' => 'jb_title, jb_desc, jb_body, jb_location',
        );
        $rawresults =  Jojo_Plugin_Jojo_search::searchPlugin($searchfields, $keywords, $language, $booleankeyword_str=false);
        $data = $rawresults ? self::getItemsById(array_keys($rawresults)) : '';
        if ($data) {
            foreach ($data as $result) {
                $result['relevance'] = $rawresults[$result['id']]['relevance'];
                $result['body'] = $result['bodyplain'];
                $result['title'] = $result['title'] . (!empty($result['location'])  ? ' - ' . $result['location'] : '' );
                $result['type'] = $result['pagetitle'];
                $result['tags'] = isset($rawresults[$result['id']]['tags']) ? $rawresults[$result['id']]['tags'] : '';
                $results[] = $result;
            }
        }
        /* Return results */
        return $results;
    }

/*
* Tags
*/
    static function getTagSnippets($ids)
    {
        $snippets = self::getItemsById($ids);
        return $snippets;
    }
}