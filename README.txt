# Basecamp Cache

## What is Basecamp Cache?

 Basecamp Cache is a system to locally cache all Basecamp data to a MySQL database. The benefits of using a local 
 MySQL cache are you can do more complex queries in a much quicker fashion. Due to the current structure of 
 Basecamp's XML files, cross relating data can be time prohibitive.
 
 An example case of this would be something similar to the following; return a list of all projects that have more 
 than 5 uncompleted todo items where the last updated date is greater than 2 weeks ago. To do this with the current 
 Basecamp API it would require downloading all the todo list XML files, all the todo list XML files, and all project
 XML files. You then could parse this in the language of your choice and return the dataset. With a MySQL cache you 
 can simply write one query to link this data and return a result in less than 100ms.
 
## How does it work?

  The way Basecamp Cache works is it will download all XML data and directly dump this information to the database. 
  Depending on the size of your Basecamp account an entire update could take an excess of 2 hours running a single 
  PHP script constantly. We recommend running the system on staggered cron jobs that update as often as needed. 
  Basecamp Cache has been tested in a production environment with an excess of 60,000 rows and 500,000 fields.
  
## How do I use it?
  
  Short version:

     1. Download Basecamp PHP API <http://code.google.com/p/basecamp-php-api> and copy files into the "includes" folder. 
     2. Run install script in the "install" folder (install/index.html). 
     3. Update the BasecampCache.php file header with login info and use.

  Long Version:

  Note: This requires the Basecamp PHP API <http://code.google.com/p/basecamp-php-api> Basecamp.class.php and 
  RestRequest.class.php files to be located in the "includes" folder.

  To setup the database you will first need to run the install script (install/index.html), located in the install 
  folder. Alternatively you can execute the SQL statements in the bccache.sql file either from importing the file 
  or via a GUI like PHPMyAdmin. Once the database has been initialized you will need to manually update the login 
  info in the header of BasecampCache.php. Once this has been completed you can update the database tables by running
  the BasecampCache.php file and passing the op argument to update the corresponding table (BasecampCache.php?op=OPCODE).
  All OPCODEs are listed below
  
	  * projects
	  * people
	  * companies
	  * todolists
	  * todoitems
	  * time
	  * comments-todo
	  * comments-messages
	  * comments-miletones
	  * messages
	  * milestones

  To automate this you can also setup cron with wget to execute these on a schedule. An example cron setup is shown below
  - for more information consult a cron reference - http://adminschoice.com/crontab-quick-reference provides a concise 
  overview on how cron works.
  
	0 */1 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=projects
	10 */1 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=people
	20 */1 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=companies
	20 */2 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=todolists
	30 */3 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=todoitems
	10 */3 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=time
	0 */3 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=comments-todo
	20 */3 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=comments-messages
	40 */3 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=comments-milestones
	30 */2 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=messages
	50 */1 * * * /usr/bin/wget -O - -q -t 1 /BasecampCache/BasecampCache.php?op=milestones
	
  There is also a "logs" table provided to determine the last updated time for any data type.
  
### Sponsorship

  This plugin is sponsored by Bizzibiz. Bizzibiz provides digital marketing
  services for small and medium business. Visit Bizzbiz at http://bizzibiz.com

### License
    
    Copyright 2011 Bizzibiz.
    
    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at
    
       http://www.apache.org/licenses/LICENSE-2.0
    
    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
    		

### 3rd Party Licenses

This project uses code originally written by Binny V Abraham (<http://www.bin-co.com/php/scripts/xml2array>). The 
inclusion of this notice does not imply endorsement nor promotion by Binny V Abraham.


    Copyright (c) 2004-2007, Binny V Abraham

	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without modification, are permitted 
	provided that the following conditions are met:
	
	Redistributions of source code must retain the above copyright notice, this list of conditions 
	and the following disclaimer.Redistributions in binary form must reproduce the above copyright 
	notice, this list of conditions and the following disclaimer in the documentation and/or other 
	materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS 
	OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL 
	DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
	DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
	IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT 
	OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    		
	    		

