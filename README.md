SCAPS
====

Static source php code analyser for Security vulnerablitites 
it is something like rips but a different approach.
Scaps works in four steps 

Step 0 : tokenize the code present in file so that we can understand the code easily, the whole tokenized code is stored in array $main 

Step 1 : remove the comments, open close and other unwanted tags, if you wanna know which tags specifically check the $ign array rules.php  

Step 2 : find user defined functions, and superglobals because thats the only data source except database, that user can modify 
so filter out all $_GET, $_POST, $_SESSION, etc all these variables are stored with their name,key,line number and type in $list array  

Step 3 : find where data from superglobal goes  

Step 4 : track any vulnerable functions specifed in exploitconfig.php file, having variables derived from superglobals or direct usage of superglobals 
