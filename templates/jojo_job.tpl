{if $error}<div class="error">{$error}</div>{/if}
{if $message}<div class="message">{$message}</div>{/if}
{if $job}
{if $job.jb_image}<a href="{$job.url}" title="{$job.jb_title}"><img src="images/w230/jobs/{$job.jb_image}" class="right-image" style="margin-top:20px" alt="{$job.jb_title}" /></a>{/if}
{if $job.ref}<p class="note">Ref #: {$job.ref}</p>{/if}
{$job.jb_body}
  <p class="note">Added: {$job.dateadded} </p>
  <h3>Applications</h3>
 {if $job.jb_expirydate !=0} <p>Applications Close: {$job.datecloses}</p>{/if}
  <h4>Contact:</h4>
  <p>{$job.jb_contactdetails|nl2br}</p>
{if $job.jb_submission=='yes'}
  <h4>or apply online:</h4>
  <p class="note">* Name and Email are required fields</p>
<form name="jobform" method="post" action="" onsubmit="return checkme()" enctype="multipart/form-data"  class="contact-form">
<label for="form_Name">Name:</label>
<input type="text" class="textinput" size="46" name="form_Name" id="form_Name" value="{if $form_Name}{$form_Name}{/if}" />

 *<br />
<label for="form_Phone">Phone:</label>
<input type="text" class="textinput" size="46" name="form_Phone" id="form_Phone" value="{if $form_Phone}{$form_Phone}{/if}" /><span class="note"> (include area code)</span>
<br />
<label for="form_Email">Email:</label>
<input type="text" class="textinput" size="46" name="form_Email" id="form_Email" value="{if $form_Email}{$form_Email}{/if}" />
 *<br />
<label for="form_Message">Message:</label>

<textarea class="textinput" rows="6" cols="40" name="form_Message" id="form_Message">{if $form_Message}{$form_Message}{/if}</textarea>
 <br />
<label>Upload CV:</label><input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
<input type="file" name="jb_FILE_cv" id="jb_FILE_cv"  size="0" value="" title="" style="vertical-align:middle;"/><span class="note"> (max file size 2MB)</span><br />
<label style="display:none;clear:both;">Submit Application:</label><input type="submit" name="submit" value="Submit" style="margin-top:15px;margin-left:110px;clear:both;"/>
{/if}
<br style="clear:both" />

</form>

{if $tags}
    <p class="tags"><strong>Tags: </strong>
{if $itemcloud}
        {$itemcloud}
{else}
{foreach from=$tags item=tag}
        <a href="tags/{$tag.url}/">{$tag.cleanword}</a>
{/foreach}
    </p>
{/if}
{/if}
    <div id="article-bottomlinks">
        <p class="links">&lt;&lt; <a href="{if $pg_url}{$pg_url}/{else}{$pageid}/{$pg_title|strtolower}{/if}" title="{$pg_title}">{$pg_title}</a>&nbsp; {if $prevjob}&lt; <a href="{$prevjob.url}" title="Previous">{$prevjob.title}</a>{/if}{if $nextjob} | <a href="{$nextjob.url}" title="Next">{$nextjob.title}</a> &gt;{/if}</p>
    </div>

{elseif $jobs}
{if $pg_body && $pagenum==1}{$pg_body}{/if}
<p><img class="icon-image" src="images/feed-icon-14x14.png" alt="RSS Feed icon" /> Keep up to date with listings as they're added with our <a href="{$pg_url}/rss">{$pg_title} RSS Feed</a></p>
{foreach from=$jobs item=job}
<div class="joblisting">
{if $job.jb_image}<a href="{$job.url}" title="{$job.jb_title}"><img src="images/150/jobs/{$job.jb_image}" class="left-image-small" alt="{$job.jb_title}" /></a>{/if}
<h4><a href="{$job.url}" title="{$job.jb_title}">{$job.jb_title}</a>{if $job.location} - {$job.location}{/if}</h4>
  <p>{if $job.description}{$job.description}{else}{$job.bodyplain|truncate:400}{/if}</p>
  <p><a href="{$job.url}" title="View full listing" rel="nofollow">View full listing</a></p>
  <p class="date">Added: {$job.dateadded} <br />
  {if $job.jb_expirydate !=0}Applications Close: {$job.datecloses}{/if}</p>
</div>
{/foreach}
<div class="jobs-pagination">
{$pagination}
</div>

{else}

{$pg_body}
{if $nojob}<p>{$nojob}</p>{/if}
{/if}

<script type="text/javascript">
/*<![CDATA[*/
function checkme()
{literal}
{
  var errors=new Array();
  var i=0;
if (document.getElementById('form_Name').value == '') {errors[i++]='Name is a required field';}
  if (document.getElementById('form_Email').value == '') {errors[i++]='Email is a required field';}
   else if (!validateEmail(document.getElementById('form_Email').value)) {errors[i++]='Email is not a valid email format';}

  if (errors.length==0) {
    return(true);
  } else {
    alert(errors.join("\n"));
    return(false);
  }
}
{/literal}
/*]]>*/
</script>

