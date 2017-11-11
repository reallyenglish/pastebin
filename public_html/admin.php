<?php
/**
 * Project: Codebin (Fork of Pastebin)
 * ver: v0.0.1-r04 11/11/2017 2:16:41 AM
 * 
 * Codebin Collaboration Tool
 * http://scans.vts-tech.org/
 *
 * This file copyright (C) 2017 Nigel Todman (nigel@nigeltodman.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the Affero General Public License 
 * Version 1 or any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * Affero General Public License for more details.
 * 
 * You should have received a copy of the Affero General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
 
 
///////////////////////////////////////////////////////////////////////////////
// includes
//
require_once('pastebin/config.inc.php');

if (isset($_POST['pass']) && ($_POST['pass']==$CONF['admin']))
{
	$dom=explode('.', $_SERVER['HTTP_HOST']);
	while (count($dom)>2)
		array_shift($dom);
	$base='.'.implode('.',$dom);
	
	
	
	setcookie("admin", md5($_POST['pass']), time()+86400*365, "/", $base);
	$_COOKIE['admin']=md5($_POST['pass']);
}

if (isset($_COOKIE['admin']))
{
	echo "You are logged in";
}
else
{
?>
	<form method="post" action="admin.php">
	<label for="pass">Password</label>
	<input type="password" id="pass" name="pass" value=""/>
	<input type="submit" name="logon" pass="logon"/>
	</form>
<?php	
}