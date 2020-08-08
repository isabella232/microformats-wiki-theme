<?php
/**
 * MonoBook nouveau
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @addtogroup Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @addtogroup Skins
 */
class SkinMicroformats extends SkinTemplate {
	/** Using microformats. */
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'Microformats Wiki';
		$this->stylename = 'Microformats';
		$this->template  = 'MicroformatsTemplate';
	}
}

/**
 * @todo document
 * @addtogroup Skins
 */
class MicroformatsTemplate extends QuickTemplate {

    /**
     * Get list of category names
     */
    function getCategories() {
        global $wgOut;
        $cats = array();
                        
        foreach($wgOut->mCategoryLinks['normal'] as $id=>$link) {
            preg_match('/<a.+href=".+(Category:[A-Za-z_-]+).+".+>([A-Za-z _-]+)<\/a>/', $link, $matches);
            $category = $matches[1];
            $category_name = $matches[2];
            $cats[$category] = array('id'=>$category, 'name'=>$category_name);
        }
        return $cats;
    }
    
    
	/**
	 * Template filter callback for MonoBook skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgUser, $wgOut, $wgSitename;
		$skin = $wgUser->getSkin();

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();
		
		$css_dir = $this->data['stylepath'].'/'.$this->data['stylename'];
		$images_dir = $this->data['stylepath'].'/'.$this->data['stylename'].'/images';
		$scripts_dir = $this->data['stylepath'].'/'.$this->data['stylename'].'/scripts';
		
        $is_homepage = ("page-Main_Page" === $this->data['pageclass']);

        $no_action = (!isset($_GET['action']));
        $is_special_page = (stripos($this->data['thispage'], 'Special:') !== false);
        $is_user_page = (stripos($this->data['thispage'], 'User:') !== false);
        $is_content_page = ($no_action && !$is_special_page);
        $is_historypage = (isset($_GET['action']) && 'history' === $_GET['action']);
        $is_editpage = (isset($_GET['action']) && ('edit' === $_GET['action'] || 'submit' === $_GET['action']));
        
        $override_context = (isset($_GET['dontbesosmart']));

        $is_spec = false;
        $is_draft = false;
        $is_pattern = false;
        $is_brainstorm = false;

        $body_classes = array();
        $categories = $this->getCategories();    
        
        if(array_key_exists('Category:Specifications', $categories)) {
            $is_spec = true;
            $body_classes[] = "specification";
        }
        if(array_key_exists('Category:Draft_Specifications', $categories)) {
            $is_draft = true;
            $body_classes[] = "specification";
            $body_classes[] = "draft";
        }
        if(array_key_exists('Category:Patterns', $categories)) {
            $is_pattern = true;
            $body_classes[] = "pattern";
        }


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php 
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?> lang="<?php $this->text('lang') ?>">
<head>
	<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
	
	<?php $this->html('headlinks') ?>
	<link rel="stylesheet" type="text/css" href="<?php $this->text('stylepath') ?>/common/shared.css" />		
	<link rel="stylesheet" type="text/css" href="<?php echo $css_dir ?>/microformats.css"/>

<?php 
    if($this->data['pagecss']) { ?>
	<style type="text/css"><?php $this->html('pagecss'   ) ?></style>
<?php	}
	if($this->data['usercss'   ]) { ?>
	<style type="text/css"><?php $this->html('usercss'   ) ?></style>
<?php	}
	if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; 
 
    print Skin::makeGlobalVariablesScript( $this->data ); ?>

    <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"></script>
    <?php $this->html('headscripts') ?>
    <?php	if($this->data['jsvarurl'  ]) { ?>
    <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl'  ) ?>"><!-- site js --></script>
    <?php	} ?>
    <?php	if($this->data['userjs'    ]) { ?>
    <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
    <?php	}
            if($this->data['userjsprev']) { ?>
    <script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
    <?php	}
 
 ?>
	
	<title><?php $this->text('title'); echo " &middot; $wgSitename" ?></title>
	
</head>
	<!-- TODO: Template-dervied body class: spec and draft -->
<body class="<?php $this->text('pageclass') ?> <?php echo implode(' ', $body_classes) ?>">
    
    <div class="header">
    <?php 
        if($is_homepage && $no_action): ?>
        <h1 class="logo">
    <?php else: ?>
        <a class="logo" href="<?php echo $this->data['nav_urls']['mainpage']['href'] ?>">
    <?php endif; ?>
            <img src="<?php echo "$images_dir/logo.gif" ?>" alt="Microformats Wiki"/>
    <?php if($is_homepage && $no_action): ?>
        </h1>
    <?php else: ?>
        </a>
    <?php endif; ?>
    
        <ul class="nav">
            <li><a href="/" title="Microformats.org Home">Blog</a></li>
            <li class="active"><span>Wiki</span></li>
            <li><a href="/discuss" title="Microformats discussion lists">Discuss</a></li>
        </ul>
        
        <form action="<?php $this->text('searchaction') ?>" id="search">
            <fieldset>
            <label for="search-text"><?php $this->msg('search') ?></label>
    	    <input id="search-text" name="search" type="text"<?php echo $skin->tooltipAndAccesskey('search');
    			if( isset( $this->data['search'] ) ) {
    		?> value="<?php $this->text('search') ?>"<?php } ?> />
	        <input type="submit" name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('searcharticle') ?>"<?php echo $skin->tooltipAndAccesskey( 'search-go' ); ?> /> 
	        <input type="submit" name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg('searchbutton') ?>"<?php echo $skin->tooltipAndAccesskey( 'search-fulltext' ); ?> />
	        </fieldset>
		</form>
    </div>

    <div class="content<?php if($is_content_page) { echo " hentry"; } ?>">
    
	<?php if($this->data['sitenotice']) { ?>
	    <div id="siteNotice"><?php $this->html('sitenotice') ?></div>
	<?php } ?>


<?php
    if('Talk' == $this->data['nscanonical'] && !$override_context):
        include('Microformats/NoTalk.php');
     else:
?>
	    <h1 class="entry-title"><?php $this->data['displaytitle'] != "" ? $this->html('title') : $this->text('title') ?></h1>

		<p class="sub-title"><?php $this->html('subtitle') ?></p>

		<?php if($this->data['undelete']) { ?>
		    <div id="contentSub2"><?php $this->html('undelete') ?></div>
		<?php } ?>
		<?php if($this->data['newtalk'] ) { ?>
		    <div class="usermessage"><?php $this->html('newtalk')  ?></div>
		<?php } ?>
		<?php if($this->data['showjumplinks']) { ?>
		    <!-- TODO: Jump nav -->
		    <div id="jump-to-nav"><?php $this->msg('jumpto') ?>
		        <a href="#sidebar"><?php $this->msg('jumptonavigation') ?></a>, 
		        <a href="#search"><?php $this->msg('jumptosearch') ?></a>
		    </div>
		<?php } ?>
        
        <div class="entry-content">
		<?php $this->html('bodytext') ?>
		</div>	
		
		<?php if(count($categories) > 0) { ?>
	        <h2>Categories</h2>
		    <div id="category-links">
		        <ul>
		        <?php foreach($categories as $key=>$cat) { ?>
		            <li><a rel="tag" href="/wiki/<?php echo $key ?>">
		                <?php echo $cat['name']; ?>
		            </a></li>
		        <?php } ?>
		        </ul>
		    </div>		        
		<?php } ?>
		
        <?php if(isset($this->data['lastmod']) && $is_content_page):
                $date_str = str_replace(', at', '', str_replace('This page was last modified on', '', $this->data['lastmod']));
                $mdate = strtotime($date_str);
            ?>
            <div class="last-modified">
                <a rel="bookmark" class="url uid" href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href']) ?>"><?php 
                 $this->data['displaytitle']!=""?$this->html('title'):$this->text('title')
              ?></a>
                was last modified:
                <?php echo $this->mdate ?>
                <span class="updated">
                    <span class="value-title" title="<?php echo date('Y-m-d\TH:i:sO', $mdate)?>"> </span>
                    <?php echo date('l, F jS, Y', $mdate)?>
                </span>
            </div>
        <?php endif; ?>

        <div id="content-controls">
    		<h2><?php $this->msg('views') ?></h2>
    		<ul>
            <?php foreach($this->data['content_actions'] as $key => $tab) {
                /** Insert 'issues page' link after Edit */
                if($insert_issues_link) { 
                    $issues_url = $this->data['content_actions']['nstab-main']['href'];
                    $issues_url .= '-issues';                    
                ?>
                    <li id="mf-issues">
                        <a href="<?php echo $issues_url; ?>" title="Raise and track issues against this page">Issues Page</a>
                    </li>
                <?php 
                    $insert_issues_link = false;
                }

                /* 
                 * Selectively hide tabs based on context.
                 * Do not show ‘Page’ link when viewing the page
                 * Never show ‘Talk’ link; we don't use it.
                 */                
                if('edit' === $key
                && (
                    $is_spec
                    || $is_draft
                    || $is_pattern
                    || $is_brainstorm
                )) {
                    $insert_issues_link = true;
                }
                         
                if( !$override_context
                    && (   ('nstab-main' === $key && $no_action)
                        || ('nstab-user' === $key && $is_user_page)
                        || ('talk' === $key)
                        || ('nstab-special' === $key)
                        || ('edit' === $key && $is_editpage)
                        || ('history' === $key && $is_historypage)
                    )
                ) {
                    continue;
                } ?>
    		    <li id="ca-<?php echo Sanitizer::escapeId($key) ?>"<?php
    		 	if($tab['class']) { ?> class="<?php echo htmlspecialchars($tab['class']) ?>"<?php }
    				 ?>><a href="<?php echo htmlspecialchars($tab['href']) ?>"<?php echo $skin->tooltipAndAccesskey('ca-'.$key) ?>><?php
    				 echo htmlspecialchars($tab['text']) ?></a></li>
            <?php } ?>
    		</ul>
    	</div>

<?php endif; ?>
	</div>
	
<div id="sidebar">

<?php if (0 !== $this->data['skin']->mUser->mId): ?>
	<div class="box vcard" id="user-box">
	    <?php $user_item = $this->data['personal_urls']['userpage']; ?>
	    <h3>
	        <?php
	            $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id="
	                .md5($this->data['skin']->mUser->mEmail)
                    ."&size=50";
	        ?>
	        <img class="logo" alt="" src="<?php echo $grav_url; ?>"/>
	        <a class="url nickname fn" href="<?php
		    echo htmlspecialchars($user_item['href']) ?>"<?php 
		    echo $skin->tooltipAndAccesskey('pt-userpage') ?><?php
		    if(!empty($user_item['class'])) { 
		        ?> class="url <?php
		        echo htmlspecialchars($user_item['class']) ?>"<?php } ?>><?php
		        
		        $name = $this->data['skin']->mUser->mRealName;
		        if('' == $name) {
		            $name = $this->data['skin']->mUser->mName;
		        }
		        
		        echo htmlspecialchars($name); ?>
		    </a>
		</h3>
		<ul class="box-inner">
<?php foreach($this->data['personal_urls'] as $key => $item) { 
            if($key === 'userpage'
            || $key === 'mytalk') {
                continue;
            }
            if($key === 'logout') {
                // Last item, so insert some manual items beforehand
                
                // <li><a href="/wiki/todo#<?php echo $this->data['username']">My to-do list</a></li>
            ?>    
            </ul>
            <ul class="logout">
                <?php
            }

    ?>
		    <li id="pt-<?php echo Sanitizer::escapeId($key) ?>"<?php
				if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
				echo htmlspecialchars($item['href']) ?>"<?php echo $skin->tooltipAndAccesskey('pt-'.$key) ?><?php
				if(!empty($item['class'])) { ?> class="<?php
				echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
				echo htmlspecialchars($item['text']) ?></a></li>
<?php } ?>
		</ul>
	</div>
<?php endif; ?>

<?php foreach ($this->data['sidebar'] as $bar => $cont) { ?>
	<div class="box" id="p-<?php echo Sanitizer::escapeId($bar) ?>"<?php echo $skin->tooltip('p-'.$bar) ?>>
		<h3><?php $out = wfMsg( $bar ); if (wfEmptyMsg($bar, $out)) echo $bar; else echo $out; ?></h3>
		<ul class="box-inner">
<?php	foreach($cont as $key => $val) { ?>
			<li id="<?php echo Sanitizer::escapeId($val['id']) ?>"<?php
				if ( $val['active'] ) { ?> class="active" <?php }
			?>><a href="<?php echo htmlspecialchars($val['href']) ?>"<?php echo $skin->tooltipAndAccesskey($val['id']) ?>><?php echo htmlspecialchars($val['text']) ?></a></li>
<?php	} ?>
		</ul>
	</div>
	
<?php } ?>	
	<div class="box" id="p-tb">
		<h3><?php $this->msg('toolbox') ?></h3>
		<ul class="box-inner">
<?php
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked') ?></a></li>
<?php 		}
		}
		if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="feed-<?php echo Sanitizer::escapeId($key) ?>"><a href="<?php
					echo htmlspecialchars($feed['href']) ?>"<?php echo $skin->tooltipAndAccesskey('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-'.$special) ?>><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-print') ?>><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-permalink') ?>><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
?>
		</ul>
	</div>

<?php
	if( $this->data['language_urls'] ) { ?>
	<div id="p-lang" class="box">
		<h3><?php $this->msg('otherlanguages') ?></h3>
		<ul class="box-inner">
<?php		foreach($this->data['language_urls'] as $langlink) { ?>
			<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
			?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php		} ?>
		</ul>
	</div>
<?php	} ?>


<?php if (0 === $this->data['skin']->mUser->mId): ?>
	<div class="box" id="user-box">
	    <?php $user_item = $this->data['personal_urls']['userpage']; ?>
	    <h3>Hey! You're not logged in</h3>
		<ul class="box-inner">
        <?php foreach($this->data['personal_urls'] as $key => $item) { 
            if($key === 'anonuserpage'
            || $key === 'anontalk') {
                continue;
            }
        ?>
		    <li id="pt-<?php echo Sanitizer::escapeId($key) ?>"<?php
				if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
				echo htmlspecialchars($item['href']) ?>"<?php echo $skin->tooltipAndAccesskey('pt-'.$key) ?><?php
				if(!empty($item['class'])) { ?> class="<?php
				echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
				echo htmlspecialchars($item['text']) ?></a></li>
        <?php } ?>        
        
        
		</ul>
	</div>
<?php endif; ?>

</div><!-- end of the sidebar -->

<div id="footer">

<?php if($this->data['copyrightico']) { ?>
	<p class="copyright"><?php $this->html('copyrightico') ?></p>
<?php } ?>

    <address class="vcard">
        The content of this wiki is the combined effort of the
        <a class="fn org url" href="http://microformats.org">
            microformats community
        </a>.
    </address>

	<ul>
    <?php 
		$footerlinks = array(
			/* 'lastmod', 'viewcount', */'numberofwatchingusers', 'credits', 'copyright',
			/* 'privacy',*/ 'about', 'disclaimer',
		);
		foreach( $footerlinks as $aLink ) {
			if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
?>				<li id="<?php echo$aLink?>"><?php $this->html($aLink) ?></li>
<?php 		}
		}		
?>
        <li>Powered by <a href="http://mediawiki.org">MediaWiki</a> | <a href="http://mediatemple.net/">(mt) media temple</a></li>
	</ul>
</div>
<?php
    $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */
    $this->html('reporttime') 
?>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "UA-1889385-1";
urchinTracker();
</script>

<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>

-->
<?php endif; ?>

</body></html>
<?php
	wfRestoreWarnings();
	} // end of execute() method
} // end of class
?>
