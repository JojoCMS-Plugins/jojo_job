<?php

$table = 'job';
$query = "
    CREATE TABLE `job` (
      `jobid` int(11) NOT NULL auto_increment,
      `jb_url` varchar(255) NOT NULL default '',
      `jb_sticky` smallint(1) NOT NULL default '0',
      `jb_seotitle` varchar(255) NOT NULL default '',
      `jb_metadesc` varchar(255) NOT NULL default '',
      `jb_title` varchar(255) NOT NULL default '',
      `jb_location` varchar(255) NOT NULL default '',
      `jb_ref` varchar(255) NOT NULL default '',
      `jb_desc` varchar(255) NOT NULL default '',
      `jb_body` text NULL,
      `jb_body_code` text NULL,
      `category` int(11) NOT NULL default '0',
      `language` varchar(10) NOT NULL default '',
      `date` int(11) NOT NULL default '0',
      `filled` tinyint(1) NOT NULL default '0',
      `jb_image` varchar(255) NOT NULL default '',
      `jb_livedate` int(11) NOT NULL default '0',
      `jb_expirydate` int(11) NOT NULL default '0',
      `jb_contact` varchar(255) NOT NULL default '',
      `jb_contactdetails` text NULL,
      `jb_submission` enum('yes','no') NOT NULL,
      `jb_tags` text NULL,
      PRIMARY KEY  (`jobid`),
      FULLTEXT KEY `title` (`jb_title`),
      FULLTEXT KEY `body` (`jb_title`,`jb_desc`,`jb_body`),
      KEY `id` (`category`)
      KEY `id` (`language`)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci  AUTO_INCREMENT=1000;";

/* Convert mysql date format to unix timestamps and update other fields while we're at it*/
if (Jojo::tableExists($table) && Jojo::fieldExists($table, 'jb_date')) {
    date_default_timezone_set(Jojo::getOption('sitetimezone', 'Pacific/Auckland'));
    $items = Jojo::selectQuery("SELECT jobid, jb_date FROM {job}");
    Jojo::structureQuery("ALTER TABLE  {job} CHANGE  `jb_date`  `date` INT(11) NOT NULL DEFAULT '0'");
    foreach ($items as $k => $a) {
        if ($a['jb_date']!='0000-00-00') {
            $timestamp = Jojo::strToTimeUK($a['jb_date']);
        } else {
            $timestamp = 0;
        }
       Jojo::updateQuery("UPDATE {job} SET date=? WHERE jobid=?", array($timestamp, $a['jobid']));
    }
    Jojo::structureQuery("ALTER TABLE  {job} CHANGE  `jb_category`  `category` INT(11) NOT NULL DEFAULT '0'");
    Jojo::structureQuery("ALTER TABLE  {jobcategory} CHANGE  `jb_pageid`  `pageid` INT(11) NOT NULL DEFAULT '0'");
}

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_job: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_job: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);


$table = 'jobcategory';
$query = "
    CREATE TABLE {jobcategory} (
      `jobcategoryid` int(11) NOT NULL auto_increment,
      `sortby` enum('jb_title asc','jb_date desc','jb_livedate desc') NOT NULL default 'jb_date desc',
      `type` enum('normal','parent','index') NOT NULL default 'normal',
     `jc_url` varchar(255) NOT NULL default '',
     `pageid` int(11) NOT NULL default '0',
     `filledmessage` varchar(255) NOT NULL default '',
      `rsslink` tinyint(1) default '1',
      `thumbnail` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`jobcategoryid`),
      KEY `id` (`pageid`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_job: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_job: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);