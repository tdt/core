<?php
// ----------------------------------------------------------------------------------
// RDFDBUtils : Logout
// ----------------------------------------------------------------------------------

/** 
 * Destroys all session data
 * 
 * @version $Id$
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/

session_start();
session_unset();
session_destroy();
//session_write_close();
header("Location: index.php");
?>