<?php
$ign = array
(
	'T_BAD_CHARACTER',
	'T_DOC_COMMENT',
	'T_COMMENT',
	'T_WHITESPACE',
	'T_OPEN_TAG',
	'T_CLOSE_TAG'
);

$incl= array(
'T_INCLUDE',
'T_INCLUDE_ONCE',
'T_REQUIRE',
'T_REQUIRE_ONCE' 
);
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
);
$taintable=array 
(
	'$_POST',
	'$_GET',
	'$_SESSION',
	'T_FUNCTION'
);
$list = array ();
$warnfunc = array();
?>