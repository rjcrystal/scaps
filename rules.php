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
$taintable=array 
(
	'$_POST',
	'$_GET',
	'T_FUNCTION',
	'$_REQUEST'
);//defines which elements to look for in a code
$ctrlr= array (
	'T_IF', 
	'T_SWITCH',
	'T_ELSEIF',
);// used to define flow or loop controlling elements  
?>