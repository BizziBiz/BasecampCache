<?php
///////////////////////////////////////////////////////////////////////////
//                          Configuration Settings
///////////////////////////////////////////////////////////////////////////
// MySQL Info
define("MYSQLHOST", "");
define("MYSQLDB", "");
define("MYSQLUSR", "");
define("MYSQLPASS", "");

//Basecamp Info
define("BCURL", "");
define("BCUSR", "");
define("BCPASS", "");

// Other Variables
define("SHOWTIME", true); // displays generation time

///////////////////////////////////////////////////////////////////////////
//  
//  $bcCache->updateProjects();
//	   Updates All Projects
//
//  $bcCache->updatePeople();
//     Updates ALl People
//
//	$bcCache->updateCompanies();
//     Updates All Companies
//
//	$bcCache->updateTodoLists();
//     Updates All Todo Lists
//
//	$bcCache->updateTodoItems($limit, $offet);
//     Updates all todo items
//     Could be time intensive - limit and offset optional
//
//	$bcCache->updateTime();
//     Updates all time logs for the last 30 days
//
//  $bcCache->updateComments($resource);
//     Updates comments for each resource (up to 50) where resource is 'todo_items', 'messages', or 'milestones'
//     Could be time intensive
//
//  $bcCache->updateMessages();
//     Updates all messages
//
//  $bcCache->updateMilestones();
//     Updates all milestones
//
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
//               Do not edit anything below this line
///////////////////////////////////////////////////////////////////////////

	set_time_limit(0);
	require_once('includes/BasecampCache.class.php');

	$bcCache = new BasecampCache(BCURL, BCUSR, BCPASS, MYSQLHOST, MYSQLUSR, MYSQLPASS, MYSQLDB);
	$bcCache->addLog($_GET['op']);
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;

	switch($_GET['op']){
		case 'projects': 
			$bcCache->updateProjects();
			break;
		case 'people':
			$bcCache->updatePeople();
			break;
		case 'companies':
			$bcCache->updateCompanies();
			break;
		case 'todolists':
			$bcCache->updateTodoLists();
			break;
		case 'todoitems':
			$bcCache->updateTodoItems();
			break;
		case 'time':
			$bcCache->updateTime();
			break;
		case 'comments-todo':
			$bcCache->updateComments('todo_items');
			break;
		case 'comments-messages':
			$bcCache->updateComments('messages');
			break;
		case 'comments-milestones':
			$bcCache->updateComments('milestones');
			break;
		case 'messages':
			$bcCache->updateMessages();
			break;
		case 'milestones':
			$bcCache->updateMilestones();
			break;
	}
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	
	if(SHOWTIME){
		echo 'Page generated in '.$total_time.' seconds.'."\n";
	}
?>