--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `companies` (`id` int(11) unsigned NOT NULL auto_increment,`name` varchar(256) NOT NULL default '',PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=2665954 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `logs` (`id` int(11) unsigned NOT NULL auto_increment,`operation` varchar(256) default NULL,`timestamp` varchar(256) default NULL,`host` varchar(256) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=3709 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `message_comments`
--

DROP TABLE IF EXISTS `message_comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `message_comments` (`id` int(11) unsigned NOT NULL auto_increment,`author_id` int(11) default NULL,`body` blob,`commentable_id` int(11) default NULL,`created_at` varchar(256) default NULL,`project_id` int(11) default NULL,`author_name` varchar(256) default NULL,`attachments_count` int(11) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=132962506 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `messages` (`id` int(11) unsigned NOT NULL auto_increment,`attachments_count` int(11) default NULL,`author_id` int(11) default NULL,`body` blob,`commented_at` varchar(256) default NULL,`comments_count` int(11) default NULL,`display_body` blob,`from_client` int(11) default NULL,`milestone_id` int(11) default NULL,`posted_on` varchar(256) default NULL,`private` varchar(128) default NULL,`project_id` int(11) default NULL,`title` varchar(256) default NULL,`author_name` varchar(256) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=51195414 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `milestone_comments`
--

DROP TABLE IF EXISTS `milestone_comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `milestone_comments` (`id` int(11) unsigned NOT NULL auto_increment,`author_id` int(11) default NULL,`body` blob,`commentable_id` int(11) default NULL,`created_at` varchar(256) default NULL,`project_id` int(11) default NULL,`author_name` varchar(256) default NULL,`attachments_count` int(11) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=132550224 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `milestones`
--

DROP TABLE IF EXISTS `milestones`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `milestones` (`id` int(11) unsigned NOT NULL auto_increment,`all_day` varchar(128) default NULL,`commented_at` varchar(256) default NULL,`comments_count` int(11) default NULL,`completed` varchar(128) default NULL,`completed_on` varchar(256) default NULL,`completer_id` int(11) default NULL,`created_on` varchar(256) default NULL,`creator_id` int(11) default NULL,`project_id` int(11) default NULL,`responsible_party_id` int(11) default NULL,`start_at` varchar(256) default NULL,`title` varchar(256) default NULL,`wants_notification` varchar(128) default NULL,`type` varchar(128) default NULL,`creator_name` varchar(256) default NULL,`deadline` varchar(128) default NULL,`completer_name` varchar(256) default NULL,`responsible_party_name` varchar(256) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=24435255 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `people` (`id` int(11) unsigned NOT NULL auto_increment,`client_id` int(11) NOT NULL,`company_id` int(11) default NULL,`created_at` varchar(256) default '',`deleted` varchar(128) NOT NULL default '',`first_name` varchar(256) default '',`last_name` varchar(256) default '',`user_name` varchar(256) default NULL,`administrator` varchar(128) NOT NULL default '',`email_address` varchar(256) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=7963557 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `projects` (`id` int(11) unsigned NOT NULL auto_increment,`announcement` blob,`created_on` varchar(256) NOT NULL default '',`last_changed_on` varchar(256) NOT NULL default '',`name` varchar(256) NOT NULL default '',`status` varchar(128) NOT NULL default '',`company_id` int(11) NOT NULL,`company_name` varchar(256) NOT NULL default '',PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=7970992 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `time_log`
--

DROP TABLE IF EXISTS `time_log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `time_log` (`id` int(11) unsigned NOT NULL auto_increment,`person_id` int(11) default NULL,`project_id` int(11) default NULL,`todo_item_id` int(11) default NULL,`date` varchar(128) default NULL,`description` blob,`hours` varchar(128) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=44012696 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `todo_item_comments`
--

DROP TABLE IF EXISTS `todo_item_comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `todo_item_comments` (`id` int(11) unsigned NOT NULL auto_increment,`author_id` int(11) default NULL,`body` blob,`commentable_id` int(11) default NULL,`created_at` varchar(256) default NULL,`project_id` int(11) default NULL,`author_name` varchar(256) default NULL,`attachments_count` int(11) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=132998358 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `todo_items`
--

DROP TABLE IF EXISTS `todo_items`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `todo_items` (`id` int(11) unsigned NOT NULL auto_increment,`comments_count` int(11) default NULL,`commented_at` varchar(256) default NULL,`completed` varchar(128) default NULL,`completed_at` varchar(256) default NULL,`completer_id` int(11) default NULL,`content` blob,`created_at` varchar(256) default NULL,`due_at` varchar(256) default NULL,`responsible_party_id` int(11) default NULL,`todo_list_id` int(11) default NULL,`created_on` varchar(256) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=106319505 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `todo_lists`
--

DROP TABLE IF EXISTS `todo_lists`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `todo_lists` (`id` int(11) unsigned NOT NULL auto_increment,`milestone_id` int(11) default NULL,`project_id` int(11) default NULL,`name` varchar(256) default NULL,`description` varchar(256) default NULL,`private` varchar(128) default NULL,`completed_count` int(11) default NULL,`uncompleted_count` int(11) default NULL,`complete` varchar(128) default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=16073372 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
