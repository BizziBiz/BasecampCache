<?php
require_once('Basecamp.class.php');
require_once('RestRequest.class.php');
set_time_limit(0);

class BasecampCache {
	private $bcObject;
	private $mysqlObj;
	private $mysql = array();
	
	public function __construct($bcUrl, $bcUsr, $bcPass, $mysqlHost, $mysqlUsr, $mysqlPass, $mysqlDb){
		$this->bcObject = new Basecamp($bcUrl, $bcUsr, $bcPass); 
		$this->mysql['usr'] = $mysqlUsr;
		$this->mysql['pass'] = $mysqlPass;
		$this->mysql['host'] = $mysqlHost;
		$this->mysql['db'] = $mysqlDb;
	}
	
	public function updateProjects(){
		$projects = $this->bcObject->getProjects();
		$projects = $this->xml2array($projects['body']);
		
		$this->openDB();
		foreach($projects['projects']['project'] as $project){
			if(count($project['announcement']) == 0){
				$project['announcement'] = '';
			}
			if(count($project['last-changed-on']) == 0){
				$project['last-changed-on'] = '';
			}
		
			$project = $this->addslashes_array($project);
			mysql_query('INSERT INTO projects VALUES('.$project['id'].',"'.$project['announcement'].'","'.$project['created-on'].'","'.$project['last-changed-on'].'","'.$project['name'].'","'.$project['status'].'",'.$project['company']['id'].',"'.$project['company']['name'].'") on duplicate key update announcement="'.$project['announcement'].'", created_on="'.$project['created-on'].'", last_changed_on="'.$project['last-changed-on'].'", name="'.$project['name'].'", status="'.$project['status'].'", company_id='.$project['company']['id'].', company_name="'.$project['company']['name'].'"', $this->mysqlObj) or die(mysql_error());
		}
		$this->closeDB();
	}
	
	public function updatePeople(){
		$people = $this->bcObject->getPeople();
		$people = $this->xml2array($people['body']);
		$this->openDB();
		
		foreach($people['people']['person'] as $person){
			$person = $this->addslashes_array($person);
			mysql_query('INSERT INTO people VALUES('.$person['id'].','.$person['client-id'].','.$person['company-id'].',"'.$person['created-at'].'","'.$person['deleted'].'","'.$person['first-name'].'","'.$person['last-name'].'","'.$person['user_name'].'","'.$person['administrator'].'","'.$person['email-address'].'") ON DUPLICATE KEY UPDATE client_id='.$person['client-id'].', company_id='.$person['company-id'].', created_at="'.$person['created-at'].'", deleted="'.$person['deleted'].'", first_name="'.$person['first-name'].'", last_name="'.$person['last-name'].'", user_name="'.$person['user-name'].'", administrator="'.$person['administrator'].'", email_address="'.$person['email-address'].'"', $this->mysqlObj) or die(mysql_error());
		}
		$this->closeDB();
	}
	
	public function updateCompanies(){
		$companies = $this->bcObject->getCompanies();
		$companies = $this->xml2array($companies['body']);
		$this->openDB();
		
		foreach($companies['companies']['company'] as $company){
			$company = $this->addslashes_array($company);
			mysql_query('INSERT INTO companies VALUES('.$company['id'].',"'.$company['name'].'") ON DUPLICATE KEY UPDATE name="'.$company['name'].'"', $this->mysqlObj) or die(mysql_error());
			
		}
		
		$this->closeDB();
	}
	
	public function updateTodoLists(){
		$this->openDB();
		$result = mysql_query('SELECT id FROM projects');
		$count = 0;
		
		while($row = mysql_fetch_array($result)){
			if($count > 495){
				sleep(10);
				$count = 0;
			}else{
				$count++;
			}
			$todoLists = $this->bcObject->getTodoListsForProject($row['id']);
			$todoLists = $this->xml2array($todoLists['body']);
			
			if(isset($todoLists['todo-lists']['todo-list']['id'])){ // only 1 list
					$todo = $todoLists['todo-lists']['todo-list'];
					$todo = $this->addslashes_array($todo);
					if(count($todo['milestone-id']) == 0){
						$todo['milestone-id'] = 0;
					}
					if(count($todo['description']) == 0){
						$todo['description'] = '';
					}
					mysql_query('INSERT INTO todo_lists VALUES('.$todo['id'].','.$todo['milestone-id'].','.$todo['project-id'].',"'.$todo['name'].'","'.$todo['description'].'","'.$todo['private'].'",'.$todo['completed-count'].','.$todo['uncompleted-count'].',"'.$todo['complete'].'") ON DUPLICATE KEY UPDATE milestone_id='.$todo['milestone-id'].', project_id='.$todo['project-id'].', name="'.$todo['name'].'", description="'.$todo['description'].'", private="'.$todo['private'].'", completed_count='.$todo['completed-count'].', uncompleted_count='.$todo['uncompleted-count'].', complete="'.$todo['complete'].'"', $this->mysqlObj) or die(mysql_error());
			}else if(count($todoLists['todo-lists']['todo-list']) == 0){
				// no record
			}else{
				foreach($todoLists['todo-lists']['todo-list'] as $todo){
					$todo = $this->addslashes_array($todo);
					if(count($todo['milestone-id']) == 0){
						$todo['milestone-id'] = 0;
					}
					if(count($todo['description']) == 0){
						$todo['description'] = '';
					}
					mysql_query('INSERT INTO todo_lists VALUES('.$todo['id'].','.$todo['milestone-id'].','.$todo['project-id'].',"'.$todo['name'].'","'.$todo['description'].'","'.$todo['private'].'",'.$todo['completed-count'].','.$todo['uncompleted-count'].',"'.$todo['complete'].'") ON DUPLICATE KEY UPDATE milestone_id='.$todo['milestone-id'].', project_id='.$todo['project-id'].', name="'.$todo['name'].'", description="'.$todo['description'].'", private="'.$todo['private'].'", completed_count='.$todo['completed-count'].', uncompleted_count='.$todo['uncompleted-count'].', complete="'.$todo['complete'].'"', $this->mysqlObj) or die(mysql_error());
				}
			}
		}
		$this->closeDB();
	}
	
	public function updateTodoItems($limit=NULL, $offset=NULL){
		$this->openDB();
		$result = mysql_query('SELECT id FROM todo_lists');
		$ids = array();
		$count = 0;
		
		while($row = mysql_fetch_array($result)){
			$ids[] = $row['id'];
		}
			
		if(isset($limit) && isset($offset)){
			$ids = array_slice($ids, $offset, $limit);
		}
		
	
		foreach($ids as $id){
			if($count > 495){
				sleep(10);
				$count = 0;
			}else{
				$count++;
			}
			$todoItems = $this->bcObject->getTodoItemsForList($id);
			$todoItems = $this->xml2array($todoItems['body']);
			
			// to fix the issues with printing
			echo ' ';
			
			if(isset($todoItems['todo-items']['todo-item']['id'])){ // one item
				$todo = $todoItems['todo-items']['todo-item'];				
				$todo = $this->addslashes_array($todo);
				if(count($todo['completer-id']) == 0){
					$todo['completer-id'] = 0;
				}
				if(count($todo['responsible-party-id']) == 0){
					$todo['responsible-party-id'] = 0;
				}
				if(count($todo['completer-id']) == 0){
					$todo['completer-id'] = 0;
				}
				
				if(count($todo['commented-at']) == 0){
					$todo['commented-at'] = '';
				}
				if(count($todo['due-at']) == 0){
					$todo['due-at'] = '';
				}
				mysql_query('INSERT INTO todo_items VALUES('.$todo['id'].','.$todo['comments-count'].',"'.$todo['commented-at'].'","'.$todo['completed'].'","'.$todo['completed-at'].'",'.$todo['completer-id'].',"'.$todo['content'].'","'.$todo['created-at'].'","'.$todo['due-at'].'",'.$todo['responsible-party-id'].','.$todo['todo-list-id'].',"'.$todo['created-on'].'") ON DUPLICATE KEY UPDATE comments_count='.$todo['comments-count'].', commented_at="'.$todo['commented-at'].'", completed="'.$todo['completed'].'", completed_at="'.$todo['completed-at'].'", completer_id='.$todo['completer-id'].', content="'.$todo['content'].'", created_at="'.$todo['created_-t'].'", due_at="'.$todo['due-at'].'", responsible_party_id='.$todo['responsible-party-id'].', todo_list_id='.$todo['todo-list-id'].', created_on="'.$todo['created-on'].'"', $this->mysqlObj) or die(mysql_error());	
			}else if(count($todoItems['todo-items']['todo-item']) == 0){
				// no items
			}else{
				foreach($todoItems['todo-items']['todo-item'] as $todo){
					$todo = $this->addslashes_array($todo);
					if(count($todo['completer-id']) == 0){
						$todo['completer-id'] = 0;
					}
					if(count($todo['responsible-party-id']) == 0){
						$todo['responsible-party-id'] = 0;
					}
					if(count($todo['completer-id']) == 0){
						$todo['completer-id'] = 0;
					}
					
					if(count($todo['commented-at']) == 0){
						$todo['commented-at'] = '';
					}
					if(count($todo['due-at']) == 0){
						$todo['due-at'] = '';
					}
					
					mysql_query('INSERT INTO todo_items VALUES('.$todo['id'].','.$todo['comments-count'].',"'.$todo['commented-at'].'","'.$todo['completed'].'","'.$todo['completed-at'].'",'.$todo['completer-id'].',"'.$todo['content'].'","'.$todo['created-at'].'","'.$todo['due-at'].'",'.$todo['responsible-party-id'].','.$todo['todo-list-id'].',"'.$todo['created-on'].'") ON DUPLICATE KEY UPDATE comments_count='.$todo['comments-count'].', commented_at="'.$todo['commented-at'].'", completed="'.$todo['completed'].'", completed_at="'.$todo['completed-at'].'", completer_id='.$todo['completer-id'].', content="'.$todo['content'].'", created_at="'.$todo['created-at'].'", due_at="'.$todo['due-at'].'", responsible_party_id='.$todo['responsible-party-id'].', todo_list_id='.$todo['todo-list-id'].', created_on="'.$todo['created-on'].'"', $this->mysqlObj) or die(mysql_error());	
				}
			}
		}
		
		$this->closeDB();
	
	}
	
	public function updateMessages(){
		$this->openDB();
		$result = mysql_query('SELECT id FROM projects');
		$count = 0;
		
		// to fix the issues with printing
		echo ' ';
		
		while($row = mysql_fetch_array($result)){
			if($count > 495){
				sleep(10);
				$count = 0;
			}else{
				$count++;
			}
			
			$msgs = $this->bcObject->getMessagesForProject($row['id']); 
			$msgs = $this->xml2array($msgs['body']);
			
			if(isset($msgs['posts']['post']['body'])){
			
			}else if(!isset($msgs['posts']['post'])){
				// no results
			}else{
				foreach($msgs['posts']['post'] as $msg){
					$msg = $this->addslashes_array($msg);
					if(count($msg['milestone-id']) == 0){
						$msg['milestone-id'] = 0;
					}
					mysql_query('INSERT INTO messages VALUES('.$msg['id'].','.$msg['attachments-count'].','.$msg['author-id'].',"'.$msg['body'].'","'.$msg['commented-at'].'",'.$msg['comments-count'].',"'.$msg['display-body'].'",'.$msg['from-client'].','.$msg['milestone-id'].',"'.$msg['posted-on'].'","'.$msg['private'].'",'.$msg['project-id'].',"'.$msg['title'].'","'.$msg['author-name'].'") ON DUPLICATE KEY UPDATE attachments_count='.$msg['attachments-count'].', author_id='.$msg['author-id'].', body="'.$msg['body'].'", commented_at="'.$msg['commented-at'].'", comments_count='.$msg['comments-count'].', display_body="'.$msg['display-body'].'", from_client='.$msg['from-client'].', milestone_id='.$msg['milestone-id'].', posted_on="'.$msg['posted-on'].'", private="'.$msg['private'].'", project_id='.$msg['project-id'].', title="'.$msg['title'].'", author_name="'.$msg['author-name'].'"', $this->mysqlObj) or die(mysql_error());
				}	
			}
		}
		
		
		$this->closeDB();
	}
	
	public function updateMilestones(){
		$this->openDB();
		$result = mysql_query('SELECT id FROM projects');
		$count = 0;
		
		// to fix the issues with printing
		echo ' ';
		
		while($row = mysql_fetch_array($result)){
			if($count > 495){
				sleep(10);
				$count = 0;
			}else{
				$count++;
			}
			
			$miles = $this->bcObject->getMilestonesForProject($row['id']);  // EDIT HERE
			$miles = $this->xml2array($miles['body']);
			
			if(isset($miles['milestones']['milestone']['id'])){
				$mile = $miles['milestones']['milestone'];
				$mile = $this->addslashes_array($mile);
					
					if(count($mile['start-at']) == 0){
						$mile['start-at'] = '';
					}
					if(count($mile['commented-at']) == 0){
						$mile['commented-at'] = '';
					}
					if(count($mile['completer-id']) == 0){
						$mile['completer-id'] = 0;
					}
					if(count($mile['responsible-party-id']) == 0){
						$mile['responsible-party-id'] = 0;
					}
				
					mysql_query('INSERT INTO milestones VALUES('.$mile['id'].',"'.$mile['all-day'].'","'.$mile['commented-at'].'",'.$mile['comments-count'].',"'.$mile['completed'].'","'.$mile['completed-on'].'",'.$mile['completer-id'].',"'.$mile['created-on'].'",'.$mile['creator-id'].','.$mile['project-id'].','.$mile['responsible-party-id'].',"'.$mile['start-at'].'","'.$mile['title'].'","'.$mile['wants-notification'].'","'.$mile['type'].'","'.$mile['creator-name'].'","'.$mile['deadline'].'","'.$mile['completer-name'].'","'.$mile['responsible-party-name'].'") ON DUPLICATE KEY UPDATE all_day="'.$mile['all-day'].'", commented_at="'.$mile['commented-at'].'", comments_count='.$mile['comments-count'].', completed="'.$mile['complete'].'", completed_on="'.$mile['completed-on'].'", completer_id='.$mile['completer-id'].', created_on="'.$mile['created-on'].'", creator_id='.$mile['creator-id'].', project_id='.$mile['project-id'].', responsible_party_id='.$mile['responsible-party-id'].', start_at="'.$mile['start-at'].'", title="'.$mile['title'].'", wants_notification="'.$mile['wants-notification'].'", type="'.$mile['type'].'", creator_name="'.$mile['creator-name'].'", deadline="'.$mile['deadline'].'", completer_name="'.$mile['completer-name'].'", responsible_party_name="'.$mile['responsible-party-name'].'"', $this->mysqlObj) or die(mysql_error());
			}else if(!isset($miles['milestones']['milestone'])){
				// no results
			}else{
				foreach($miles['milestones']['milestone'] as $mile){
					$mile = $this->addslashes_array($mile);
					
					if(count($mile['start-at']) == 0){
						$mile['start-at'] = '';
					}
					if(count($mile['commented-at']) == 0){
						$mile['commented-at'] = '';
					}
					if(count($mile['completer-id']) == 0){
						$mile['completer-id'] = 0;
					}
					if(count($mile['responsible-party-id']) == 0){
						$mile['responsible-party-id'] = 0;
					}
				
					mysql_query('INSERT INTO milestones VALUES('.$mile['id'].',"'.$mile['all-day'].'","'.$mile['commented-at'].'",'.$mile['comments-count'].',"'.$mile['completed'].'","'.$mile['completed-on'].'",'.$mile['completer-id'].',"'.$mile['created-on'].'",'.$mile['creator-id'].','.$mile['project-id'].','.$mile['responsible-party-id'].',"'.$mile['start-at'].'","'.$mile['title'].'","'.$mile['wants-notification'].'","'.$mile['type'].'","'.$mile['creator-name'].'","'.$mile['deadline'].'","'.$mile['completer-name'].'","'.$mile['responsible-party-name'].'") ON DUPLICATE KEY UPDATE all_day="'.$mile['all-day'].'", commented_at="'.$mile['commented-at'].'", comments_count='.$mile['comments-count'].', completed="'.$mile['complete'].'", completed_on="'.$mile['completed-on'].'", completer_id='.$mile['completer-id'].', created_on="'.$mile['created-on'].'", creator_id='.$mile['creator-id'].', project_id='.$mile['project-id'].', responsible_party_id='.$mile['responsible-party-id'].', start_at="'.$mile['start-at'].'", title="'.$mile['title'].'", wants_notification="'.$mile['wants-notification'].'", type="'.$mile['type'].'", creator_name="'.$mile['creator-name'].'", deadline="'.$mile['deadline'].'", completer_name="'.$mile['completer-name'].'", responsible_party_name="'.$mile['responsible-party-name'].'"', $this->mysqlObj) or die(mysql_error());
				}
			}
		}		
		$this->closeDB();
	}
	
	/*
	*  $type can be todo_items, posts, or milestones
	*/
	public function updateComments($type='todo_items'){
		$this->openDB();
		$result = NULL;
		$dbTable = NULL;
		$resource = NULL;
		
		switch($type){
			case 'todo_items':
				$result = mysql_query('SELECT id FROM todo_items');
				$dbTable = 'todo_item_comments';
				$resource = 'todo_items';
				break;
			case 'messages':
				$result = mysql_query('SELECT id FROM messages');
				$dbTable = 'message_comments';
				$resource = 'posts';
				break;
			case 'milestones':
				$result = mysql_query('SELECT id FROM milestones');
				$dbTable = 'milestone_comments';
				$resource = 'milestones';
				break;
		}

		$count = 0;
		
		// to fix the issues with printing
		echo ' ';
		
		while($row = mysql_fetch_array($result)){
			if($count > 495){
				sleep(10);
				$count = 0;
			}else{
				$count++;
			}
			
			$comm = $this->bcObject->getRecentCommentsForResource($resource,$row['id']);  // EDIT HERE
			$comm = $this->xml2array($comm['body']);
			
			if(isset($comm['comments']['comment']['body'])){ // 1 result
				$item = $comm['comments']['comment'];
				
				$item = $this->addslashes_array($item);
				if(count($item['commentable-id']) == 0){
					$item['commentable-id'] = 0;
				}
				if(count($item['project-id']) == 0){
					$item['project-id'] = 0;
				}
			
				mysql_query('INSERT INTO '.$dbTable.' VALUES('.$item['id'].','.$item['author-id'].',"'.$item['body'].'",'.$item['commentable-id'].',"'.$item['created-at'].'",'.$item['project-id'].',"'.$item['author-name'].'",'.$item['attachments-count'].') ON DUPLICATE KEY UPDATE author_id='.$item['author-id'].', body="'.$item['body'].'", commentable_id='.$item['commentable-id'].', created_at="'.$item['created-at'].'", project_id='.$item['project-id'].', author_name="'.$item['author-name'].'", attachments_count='.$item['attachments-count'], $this->mysqlObj) or die (mysql_error());
			}else if (!isset($comm['comments']['comment'])){
				// no result
			}else{
				foreach($comm['comments']['comment'] as $item){
					$item = $this->addslashes_array($item);
					if(count($item['commentable-id']) == 0){
						$item['commentable-id'] = 0;
					}
					if(count($item['project-id']) == 0){
						$item['project-id'] = 0;
					}
				
					mysql_query('INSERT INTO '.$dbTable.' VALUES('.$item['id'].','.$item['author-id'].',"'.$item['body'].'",'.$item['commentable-id'].',"'.$item['created-at'].'",'.$item['project-id'].',"'.$item['author-name'].'",'.$item['attachments-count'].') ON DUPLICATE KEY UPDATE author_id='.$item['author-id'].', body="'.$item['body'].'", commentable_id='.$item['commentable-id'].', created_at="'.$item['created-at'].'", project_id='.$item['project-id'].', author_name="'.$item['author-name'].'", attachments_count='.$item['attachments-count'], $this->mysqlObj) or die (mysql_error());
				}
			}
		}
		
		$this->closeDB();
	}
	
	public function updateTime(){
		$this->openDB();
		$result = mysql_query('SELECT id FROM projects');
		
		$count = 0;
		
		while($row = mysql_fetch_array($result)){
			$i = 1;
			
			if($count > 245){
				sleep(10);
				$count = 0;
			}else{
				$count++;
			}

			while($this->updateProjectTime($row['id'], $i)){
				$i++;
			}
			echo ' ';
		}
		
		$this->closeDB();
	
	}

	/*
	*  DB connection must be open prior to using
	*/
	private function updateProjectTime($project, $page){
		 $time = $this->bcObject->getTimeEntriesForProject($project,$page);	 
		 $time = $this->xml2array($time['body']);
		 
		 if(isset($time['time-entries']['time-entry']['date'])){ // 1 item
		 	$time = $time['time-entries']['time-entry'];
		 	$time = $this->addslashes_array($time);
		 		
		 		if(count($time['person-id']) == 0){
					$time['person-id'] = 0;
				}
				if(count($time['project-id']) == 0){
					$time['project-id'] = 0;
				}
				if(count($time['todo-item-id']) == 0){
					$time['todo-item-id'] = 0;
				}
				if(count($time['description']) == 0){
					$time['description'] = '';
				}
		 		
		 		if(strtotime($time['date']) >= strtotime('-1 month')){
				 	mysql_query('INSERT INTO time_log VALUES('.$time['id'].','.$time['person-id'].','.$time['project-id'].','.$time['todo-item-id'].',"'.$time['date'].'","'.$time['description'].'","'.$time['hours'].'") ON DUPLICATE KEY UPDATE person_id='.$time['person-id'].', project_id='.$time['project-id'].', todo_item_id='.$time['todo-item-id'].', date="'.$time['date'].'", description="'.$time['description'].'", hours="'.$time['hours'].'"', $this->mysqlObj) or die(mysql_error());
			 	}
		 	return false;
		 }else if(!isset($time['time-entries']['time-entry'])){ // no items
		 	return false;
		 }else{
		 	$flag = false;
		 	$last = count($time['time-entries']['time-entry']);
		 	$tmp = $time['time-entries']['time-entry'][($last-1)]['date'];
		 	
		 	$time2 = NULL;
			if($page > 1){
			 	$time2 = $this->bcObject->getTimeEntriesForProject($project,$page-1);
			 	$time2 = $this->xml2array($time2['body']);
			}
			 
			if($time == $time2){  //what if duplicates?
				return false; 
			}
		 	
		 	if(strtotime($time['time-entries']['time-entry'][($last-1)]['date']) >= strtotime('-1 month')){
		 		$flag = true;
		 	}
		 	
		 	foreach($time['time-entries']['time-entry'] as $time){
		 		$time = $this->addslashes_array($time);
		 		
		 		if(count($time['person-id']) == 0){
					$time['person-id'] = 0;
				}
				if(count($time['project-id']) == 0){
					$time['project-id'] = 0;
				}
				if(count($time['todo-item-id']) == 0){
					$time['todo-item-id'] = 0;
				}
				if(count($time['description']) == 0){
					$time['description'] = '';
				}
		 		
		 		if(strtotime($time['date']) >= strtotime('-1 month')){
				 	mysql_query('INSERT INTO time_log VALUES('.$time['id'].','.$time['person-id'].','.$time['project-id'].','.$time['todo-item-id'].',"'.$time['date'].'","'.$time['description'].'","'.$time['hours'].'") ON DUPLICATE KEY UPDATE person_id='.$time['person-id'].', project_id='.$time['project-id'].', todo_item_id='.$time['todo-item-id'].', date="'.$time['date'].'", description="'.$time['description'].'", hours="'.$time['hours'].'"', $this->mysqlObj) or die(mysql_error());
			 	}
		 	}
		 	
		 	return $flag;
		 }
	}
	
	public function addLog($operation){
		$this->openDB();
		mysql_query('INSERT INTO logs VALUES(null,"'.$operation.'","'.time().'","'.$_SERVER['REMOTE_ADDR'].'")', $this->mysqlObj) or die(mysql_error());
		$this->closeDB();
	}
	
	private function addslashes_array($input_arr){
	    if(is_array($input_arr)){
	        $tmp = array();
	        foreach ($input_arr as $key1 => $val){
	            $tmp[$key1] = $this->addslashes_array($val);
	        }
	        return $tmp;
	    }else{
	        return addslashes($input_arr);
	    }
	}
	
	private function openDB(){
		$this->mysqlObj = mysql_connect($this->mysql['host'], $this->mysql['usr'], $this->mysql['pass']) or die;
		mysql_select_db($this->mysql['db'], $this->mysqlObj);
	}
	
	private function closeDB(){
		mysql_close($this->mysqlObj);
	}
	
	// Redistributed under the PSD License per http://www.bin-co.com/license.php
	// Originally from http://www.bin-co.com/php/scripts/xml2array/
	private function xml2array($contents, $get_attributes=1, $priority = 'tag') { 
	    if(!$contents) return array(); 
	
	    if(!function_exists('xml_parser_create')) { 
	        //print "'xml_parser_create()' function not found!"; 
	        return array(); 
	    } 
	
	    //Get the XML parser of PHP - PHP must have this module for the parser to work 
	    $parser = xml_parser_create(''); 
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
	    xml_parse_into_struct($parser, trim($contents), $xml_values); 
	    xml_parser_free($parser); 
	
	    if(!$xml_values) return;//Hmm... 
	
	    //Initializations 
	    $xml_array = array(); 
	    $parents = array(); 
	    $opened_tags = array(); 
	    $arr = array(); 
	
	    $current = &$xml_array; //Refference 
	
	    //Go through the tags. 
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
	    foreach($xml_values as $data) { 
	        unset($attributes,$value);//Remove existing values, or there will be trouble 
	
	        //This command will extract these variables into the foreach scope 
	        // tag(string), type(string), level(int), attributes(array). 
	        extract($data);//We could use the array by itself, but this cooler. 
	
	        $result = array(); 
	        $attributes_data = array(); 
	         
	        if(isset($value)) { 
	            if($priority == 'tag') $result = $value; 
	            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	        } 
	
	        //Set the attributes too. 
	        if(isset($attributes) and $get_attributes) { 
	            foreach($attributes as $attr => $val) { 
	                if($priority == 'tag') $attributes_data[$attr] = $val; 
	                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
	            } 
	        } 
	
	        //See tag status and do the needed. 
	        if($type == "open") {//The starting of the tag '<tag>' 
	            $parent[$level-1] = &$current; 
	            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
	                $current[$tag] = $result; 
	                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
	                $repeated_tag_index[$tag.'_'.$level] = 1; 
	
	                $current = &$current[$tag]; 
	
	            } else { //There was another element with the same tag name 
	
	                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
	                    $repeated_tag_index[$tag.'_'.$level]++; 
	                } else {//This section will make the value an array if multiple tags with the same name appear together
	                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	                    $repeated_tag_index[$tag.'_'.$level] = 2; 
	                     
	                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
	                        unset($current[$tag.'_attr']); 
	                    } 
	
	                } 
	                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
	                $current = &$current[$tag][$last_item_index]; 
	            } 
	
	        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
	            //See if the key is already taken. 
	            if(!isset($current[$tag])) { //New Key 
	                $current[$tag] = $result; 
	                $repeated_tag_index[$tag.'_'.$level] = 1; 
	                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 
	
	            } else { //If taken, put all things inside a list(array) 
	                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 
	
	                    // ...push the new element into that array. 
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
	                     
	                    if($priority == 'tag' and $get_attributes and $attributes_data) { 
	                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
	                    } 
	                    $repeated_tag_index[$tag.'_'.$level]++; 
	
	                } else { //If it is not an array... 
	                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	                    $repeated_tag_index[$tag.'_'.$level] = 1; 
	                    if($priority == 'tag' and $get_attributes) { 
	                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                             
	                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
	                            unset($current[$tag.'_attr']); 
	                        } 
	                         
	                        if($attributes_data) { 
	                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
	                        } 
	                    } 
	                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
	                } 
	            } 
	
	        } elseif($type == 'close') { //End of tag '</tag>' 
	            $current = &$parent[$level-1]; 
	        } 
	    } 
	     
	    return($xml_array); 
	}

}

?>