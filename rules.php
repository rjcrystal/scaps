<?php
$ign = array
(
	'T_BAD_CHARACTER',
	'T_DOC_COMMENT',
	'T_COMMENT',
	'T_WHITESPACE',
	'T_OPEN_TAG',
	'T_CLOSE_TAG'
); //which elements to ignore  
$exptbl= array (
'mysql_query',
'mysqli_query',
'echo',
'print',
'eval',
'popen',
'assert',
'include',
'include_once',
'require',
'require_once'
);// exploitable functions which can be
$taintable=array 
(
	'$_POST',
	'$_GET',
	'$_SESSION',
	'T_FUNCTION'
);//defines which elements to look for in a code
$ctrlr= array (

	'T_IF', 
	'T_SWITCH',
	'T_ELSEIF',
);// used to define flow or loop controlling elements  
$fcon= array (
		'T_INCLUDE',
		'T_INCLUDE_ONCE',
		'T_REQUIRE',
		'T_REQUIRE_ONCE'
);// used to detect if files are connected /included
$list = array ();
$warnfunc = array();
?>