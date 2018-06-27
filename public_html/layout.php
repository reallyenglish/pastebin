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
 
echo "<?xml version=\"1.0\" encoding=\"".$charset_code[$charset]['http']."\"?>\n";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $page['title'] ?></title>
<meta name="ROBOTS" content="NOARCHIVE"/>
<link rel="stylesheet" type="text/css" media="screen" href="/pastebin.css?ver=6" />

<?php if (isset($page['post']['codecss']))
{
	echo '<style type="text/css">';
	echo $page['post']['codecss'];
	echo '</style>';
}
?>
<script type="text/javascript" src="/pastebin.js?ver=7"></script>
</head>


<body onload="initPastebin()">
<div style="display:none;">
<h1 style="display: none;">codebin - collaborative debugging</h1>
<p style="display: none;">codebin is a collaborative debugging tool allowing you to share
and modify code snippets while chatting on IRC, IM or a message board.</p>
<p style="display: none;">This site is developed to XHTML and CSS2 W3C standards.  
If you see this paragraph, your browser does not support those standards and you 
need to upgrade.  Visit <a href="http://www.webstandards.org/upgrade/" target="_blank">WaSP</a>
for a variety of options.</p>
</div>

<div id="titlebar"><a href="/"><?php
	echo $page['title'];
?></a>
</div>



<div id="menu">

<?php if ($is_admin){
 
        //TODO - roll this into the classes
        $count=0;
	$bullets="";
        $dir=$_SERVER['DOCUMENT_ROOT'].'/../abuse/';
        $d=dir($dir);
        while (false !== ($entry = $d->read())) 
        {
            if ($entry[0]!='.')
            {
		$pid=$entry;
                //does post exist? 
                $file=$_SERVER['DOCUMENT_ROOT'].'/../posts/'.substr($pid,0,1);
 		$file.='/'.substr($pid,1,2);
		$file.='/'.substr($pid,3,2);
 		$file.='/'.substr($pid,5,2);
		$file.='/'.$pid;

                if (file_exists($file))
                {
                    $bullets.= '<li><a href="/'.$pid.'">'.$pid.'</a></li>';
                    $count++;
                }
 		else
		{
		    unlink($dir.$entry);
		}
            
            }
        }
        $d->close();

	echo '<h1>'.t('Abuse').' ('.$count.')</h1><ul>';
        echo $bullets;

	if ($count==0)
		echo '<li>no abuse reports</li>';
        echo '</ul>';

}


?>


<?php echo '<h1>'.t('Recent Posts').'</h1>'?>

<ul>
<?php  
	foreach($page['recent'] as $idx=>$entry)
	{
		if ($entry['pid']==$pid)
			$cls=" class=\"highlight\"";
		else
			$cls="";
			
		echo "<li{$cls}><a href=\"{$entry['url']}\">";
		echo $entry['posttitle2'];
		echo "</a><br/>{$entry['agefmt']}</li>\n";
	}

	echo "<li><a rel=\"nofollow\" href=\"{$CONF['this_script']}\">".t('Make new post').'</a></li>';
?>
</ul>

<?php if (!isset($_GET['search'])) { ?>


<?php } ?>

<h1>News</h1>

<?php

echo "<p>";
echo t('For news and feedback see my <a title="View codebin related posts on my blog" href="http://www.nigeltodman.com/">blog</a>.');
echo "</p>";

?>

<?php

echo '<h1>'.t('Credits').'</h1><p>';
	
	echo t('Original pastebin developed by <a href="http://blog.dixo.net/about/">Paul Dixon</a>, 2002-2007 (<a href="https://github.com/lordelph/pastebin">GitHub</a>)<br><br>Forked by <a href="http://www.nigeltodman.com">Nigel Todman</a>, 2017 (<a href="https://github.com/Veritas83/pastebin">GitHub</a>)<br><br>');
	echo t('<font size=1>Ver: '.$ver.'</font>');
?>



</div>


<div id="content">

<br/>
<br/>

<?php

///////////////////////////////////////////////////////////////////////////////
// show processing errors
//
if (!empty($pastebin->errors))
{
	echo '<h1>'.t('Errors').'</h1><ul>';
	foreach($pastebin->errors as $err)
	{
		echo "<li>$err</li>";
	}
	echo "</ul>";
	echo "<hr />";
}

if (!empty($page['delete_message']))
{
	echo "<h1>{$page['delete_message']}</h1><br/>";
}

if (isset($_REQUEST["diff"]))
{
	
	$newpid=$pastebin->cleanPostId($_REQUEST['diff']);
	
	$newpost=$pastebin->getPost($newpid);
	if (count($newpost))
	{
		$oldpost=$pastebin->getPost($newpost['parent_pid']);	
		if (count($oldpost))
		{
			$page['pid']=$newpid;
			$page['current_format']=$newpost['format'];
			$page['editcode']=$newpost['code'];
			$page['posttitle']='';
	
			//echo "<div style=\"text-align:center;border:1px red solid;padding:5px;margin-bottom:5px;\">Diff feature is in BETA! If you have feedback, send it to lordelph at gmail.com</div>";
			
			echo "<h1>";
			printf(t('Difference between<br/>modified post %s by %s on %s and<br/>'.
				'original post %s by %s on %s'),
				"<a href=\"".$pastebin->getPostUrl($newpost['pid'])."\">{$newpost['pid']}</a>",
				$newpost['poster'],
				$newpost['postdate'],
				'<a href="'.$pastebin->getPostUrl($oldpost['pid'])."\">{$oldpost['pid']}</a>",
				$oldpost['poster'],
				$oldpost['postdate']);
				
			echo "<br/>";	
			
			echo t('Show');
			echo " <a title=\"".t('Don\'t show inserted or changed lines')."\" style=\"padding:1px 4px 3px 4px;\" id=\"oldlink\" href=\"javascript:showold()\">".t('old version')."</a> | ";
			echo "<a title=\"".t('Don\'t show lines removed from old version')."\" style=\"padding:1px 4px 3px 4px;\" id=\"newlink\" href=\"javascript:shownew()\">".t('new version')."</a> | ";
			echo "<a title=\"".t('Show both insertions and deletions')."\"  style=\"background:#880000;padding:1px 4px 3px 4px;\" id=\"bothlink\" href=\"javascript:showboth()\">".t('both versions')."</a> ";
			echo "</h1>";
			
			$newpost['code']=preg_replace('/^'.$CONF['highlight_prefix'].'/m', '', $newpost['code']);
			$oldpost['code']=preg_replace('/^'.$CONF['highlight_prefix'].'/m', '', $oldpost['code']);
			
			$a1=explode("\n", $newpost['code']);
			$a2=explode("\n", $oldpost['code']);
			
			$diff=new Diff($a2,$a1, 1);
			
			echo "<table cellpadding=\"0\" cellspacing=\"0\" class=\"diff\">";
			echo "<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td></tr>";
			echo $diff->output;
			echo "</table>";
		}
		
	}
	
	
}

///////////////////////////////////////////////////////////////////////////////
// show a post
//

if (isset($_GET['help']))
	$page['posttitle']="";
	
if (!empty($page['post']['posttitle']))
{
		echo "<h1>{$page['post']['posttitle']}";
		if (strlen($page['post']['parent_pid']))
		{
			echo ' (';
			printf(t("modification of post by %s"),
				"<a href=\"{$page['post']['parent_url']}\" title=\"".t('view original post')."\">{$page['post']['parent_poster']}</a>");
			
			echo " <a href=\"{$page['post']['parent_diffurl']}\" title=\"".t('compare differences')."\">".t('view diff')."</a>)";
		}
		
		echo "<br/>";
		
		if (isset($page['post']['ip']) && $is_admin)
		{
			echo "<a title=\"whois lookup\" href=\"http://whois.domaintools.com/{$page['post']['ip']}\">{$page['post']['ip']}</a> ";
		}
		
		//echo "<a href=\"#\" onclick=\"gotoURL('{$page['post']['spamurl']}')\" title=\"".t('report spam')."\">".t('report spam')."</a> | ";
		
		echo "<a href=\"#\" onclick=\"showSpamForm()\" title=\"".t('report spam')."\">".t('report abuse')."</a> | ";
		
		
		
		if ($page['can_erase'])
		{
			echo "<a href=\"{$page['post']['deleteurl']}\" title=\"".t('delete post')."\">".t('delete post')."</a> | ";
		}
		
		
		
		
		$followups=count($page['post']['followups']);
		if ($followups)
		{
			echo t('View followups from ');
			$sep="";
			foreach($page['post']['followups'] as $idx=>$followup)
			{
				echo $sep."<a title=\"posted {$followup['postfmt']}\" href=\"{$followup['followup_url']}\">{$followup['poster']}</a>";
				$sep=($idx<($followups-2))?", ":(' '.t('and').' ');	
			}
			
			echo " | ";
		}
		
		if ($page['post']['parent_pid']>0)
		{
			echo "<a href=\"{$page['post']['parent_diffurl']}\" title=\"".t('compare differences')."\">".t('diff')."</a> | ";
		} 
		
		echo "<a href=\"{$page['post']['downloadurl']}\" title=\"".t('download file')."\">".t('download')."</a> | ";
		
		echo "<span id=\"copytoclipboard\"></span>";
		
		echo "<a href=\"/\" title=\"".t('make new post')."\">".t('new post')."</a>";
		
		echo "</h1>";

#abuse reports

if ($is_admin)
{

   $abusefile=$_SERVER['DOCUMENT_ROOT'].'/../abuse/'.$page['post']['pid'];
   if (file_exists($abusefile))
   {
       $abuse=file_get_contents($abusefile);
       echo '<div style="background:#ffffaa;padding:5px;">';
       echo "<pre>$abuse</pre>";
       echo '</div>';
   }


}		
		
		echo '<div id="spamform" style="display:none">';
		echo '<form method="post" action="'.$page['post']['pid'].'">';
		echo '<input  type="hidden" id="spam_pid" name="pid" value="'.$page['post']['pid'].'">';
		echo '<input  type="hidden" id="processabuse" name="processabuse" value="1">';
		
		echo '<p>'.t('Please indicate why this post is abusive, and provide any other useful information.').'</p>';

		echo '<input type="radio" name="abuse" value="spam" id="abuse_spam">';
		echo '<label for="abuse_spam">'.t('Spam / advertising / junk').'</label><br>';
		
		echo '<input type="radio" name="abuse" value="personal" id="abuse_personal">';
		echo '<label for="abuse_personal">'.t('Personal details').'</label><br>';
		
		echo '<input type="radio" name="abuse" value="proprietary" id="abuse_proprietary">';
		echo '<label for="abuse_proprietary">'.t('Proprietary code').'</label><br>';
		
		echo '<input checked="checked" type="radio" name="abuse" value="other" id="abuse_other">';
		echo '<label for="abuse_other">'.t('Other').'</label><br><br>';
		
		echo '<label for="comments">'.t('comments (optional)').'</label><br>';
		echo '<textarea style="width:350px" id="comments" name="comments" rows="2" cols="30"></textarea><br><br>';
		
		echo '<label for="sender">'.t('email (optional)').'</label><br>';
		echo '<input  style="width:350px" type="text" id="sender" name="sender"><br><br>';
		
				
		echo '<input type="submit" name="reportspam" value="'.t('send abuse report').'">';
		echo '</form>';
		echo '</div>';
		
		
		
}
if (isset($page['post']['pid']))
{
	echo "<div class=\"syntax\">".$page['post']['codefmt']."</div>";
	echo "<br /><b>".t('Submit a correction or amendment below')." (<a href=\"{$CONF['this_script']}\">".t('click here to make a fresh posting')."</a>)</b><br/>";
	echo t('After submitting an amendment, you\'ll be able to view the differences between the old and new posts easily').'.';
}	



if (isset($_GET['help']))
{
	h1('What is pastebin?');
	p('codebin is here to help you collaborate on debugging code snippets. '.
		'If you\'re not familiar with the idea, most people use it like this:');
	
	echo '<ul>';
	
	li('<a href="/">submit</a> a code fragment to pastebin, getting a url like http://pastebin.com/1234');
	li('paste the url into an IRC or IM conversation');
	li('someone responds by reading and perhaps submitting a modification of your code');
	li('you then view the modification, maybe using the built in diff tool to help locate the changes');
	
	
	echo '</ul>';


	h1('How can I view the differences between two posts?');	
	
	p('When you view a post, you have the opportunity of editing the text - '.
		'<strong>this creates a new post</strong>, but when you view it, you\'ll be given a '.
		'\'diff\' link which allows you to compare the changes between the old and the new version');	
	p('This is a powerful feature, great for seeing exactly what lines someone changed');
	
	
	h1('How can I delete a post?');	
	p('If you clicked the "remember me" checkbox when posting, you will be able to delete '.
	'post from the same computer you posted from - simply view the post and click the "delete post" link.');
	p('In other cases, contact us and we will delete it for you');
	
}
else if (isset($_GET['search']))
{
    $q="";
    if (isset($_GET['q']))
    {
        $q=htmlentities($_GET['q']);
    }

    ?>

<?php
}
else
{
?>
<form name="editor" method="post" action="<?php echo $CONF['this_script']?>">
<input type="hidden" name="parent_pid" value="<?php echo isset($page['post']['pid'])?$page['post']['pid']:'' ?>"/>

<br/> 
<?php

echo t('Syntax highlighting:').'<select name="format">';

//show the popular ones
foreach ($CONF['all_syntax'] as $code=>$name)
{
	if (in_array($code, $CONF['popular_syntax']))
	{
		$sel=($code==$page['current_format'])?"selected=\"selected\"":"";
		echo "<option $sel value=\"$code\">$name</option>";
	}
}

echo "<option value=\"text\">----------------------------</option>";

//show all formats
foreach ($CONF['all_syntax'] as $code=>$name)
{
	$sel=($code==$page['current_format'])?"selected=\"selected\"":"";
	if (in_array($code, $CONF['popular_syntax']))
		$sel="";
	echo "<option $sel value=\"$code\">$name</option>";
	
}
?>
</select><br/>
<br/>
<label for="posttitle2"><?php echo t('Post Title')?></label><br/>
<input type="text" maxlength="64" size="64" id="posttitle2" name="posttitle2" value="Untitled" /><br>
<?php printf(t('To highlight particular lines, prefix each line with %s'),$CONF['highlight_prefix']); 

$rows=isset($page['post']['editcode']) ? substr_count($page['post']['editcode'], "\n") : 0; 
$rows=min(max($rows,10),40);
?>
<br/>
<textarea id="code" class="codeedit" name="code2" cols="80" rows="<?php echo $rows ?>" onkeydown="return onTextareaKey(this,event)"><?php 
if (!empty($page['post']['editcode'])) {
	echo htmlentities($page['post']['editcode'], ENT_COMPAT,$CONF['htmlentity_encoding']);
}
?></textarea>

<div id="namebox">
	
<label for="poster"><?php echo t('Post Author')?></label><br/>
<input type="text" maxlength="24" size="24" id="poster" name="poster" value="<?php echo isset($page['poster'])?$page['poster']:'' ?>" />
<input type="submit" name="paste" value="<?php echo t('Post')?>"/>
<br />
<?php echo '<input type="checkbox" name="remember" value="1" '.$page['remember'].' />'.t('Remember me so that I can delete my post'); ?>

</div>


<div id="expirybox">


<div id="expiryradios">
<label><?php echo t('How long should your post be retained?') ?></label><br/>

<input type="radio" id="expiry_day" name="expiry" value="d" <?php if ($page['expiry']=='d') echo 'checked="checked"'; ?> />
<label id="expiry_day_label" for="expiry_day"><?php echo t('a day') ?></label>

<input type="radio" id="expiry_month" name="expiry" value="m" <?php if ($page['expiry']=='m') echo 'checked="checked"'; ?> />
<label id="expiry_month_label" for="expiry_month"><?php echo t('a month') ?></label>

<input type="radio" id="expiry_forever" name="expiry" value="f" <?php if ($page['expiry']=='f') echo 'checked="checked"'; ?> />
<label id="expiry_forever_label" for="expiry_forever"><?php echo t('forever') ?></label>
</div>

<div id="expiryinfo"></div>
	
</div>

<div id="email">
<input type="text" size="8" name="email" value="" />
</div>

<div id="end"></div>

</form>
<?php 
} 
?>

</div>
</body>
</html>
