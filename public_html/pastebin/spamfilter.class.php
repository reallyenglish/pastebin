<?php
/**
 * Project: Codebin (Fork of Pastebin)
 * ver: v0.0.1-r02 11/10/2017 4:19:52 AM
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
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.s
 */

/**
* Placeholder for your own spam rules
*/
class SpamFilter
{
	public function canPost($text)
	{
		return true;
	}

}