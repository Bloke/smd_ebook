<?php

// This is a PLUGIN TEMPLATE for Textpattern CMS.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'smd_ebook';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.10';
$plugin['author'] = 'Stef Dawson';
$plugin['author_uri'] = 'http://stefdawson.com/';
$plugin['description'] = 'Create e-books (e.g. Kindle) from Textpattern content';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public              : only on the public side of the website (default)
// 1 = public+admin        : on both the public and admin side
// 2 = library             : only when include_plugin() or require_plugin() is called
// 3 = admin               : only on the admin side (no AJAX)
// 4 = admin+ajax          : only on the admin side (AJAX supported)
// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)
$plugin['type'] = '4';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '3';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

/** Uncomment me, if you need a textpack
$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
abc_sample_string => Sample String
abc_one_more => One more
#@language de-de
abc_sample_string => Beispieltext
abc_one_more => Noch einer
EOT;
**/
// End of textpack

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
/**
 * smd_ebook
 *
 * A Textpattern CMS plugin for creating e-books (Kindle) from Txp content
 *  -> Content can be in one article or across many
 *  -> Article image of one of the articles is used as cover art
 *  -> Automatic TOC generation and page breaks from Textiled markup
 *  -> Support for book description
 *
 * @author Stef Dawson
 * @link   http://stefdawson.com/
 */

global $smd_ebook_prefs;
smd_ebook_get_prefs();

if(@txpinterface == 'admin') {
	global $smd_ebook_event, $smd_ebook_styles;
	$smd_ebook_event = 'smd_ebook';

	$smd_ebook_styles = array(
		'cpanel' =>
         '.smd_hidden { display:none; }
         .smd_active { font-weight:bold; }
         .smd_clear { clear:both; }
         .smd_preselected { opacity:0.6; font-style:italic; }
         .smd_selected { border-top:solid #444; border-bottom:solid #444; }
         .smd_important { color:red; }
         .smd_inline { display:inline; }
         .txp-container, .txp-control-panel { text-align:center; }
         #smd_ebook_preview { display:none; position:absolute; top:1em; left:1em; margin:0 auto; text-align:left; border:2px ridge #999; background:#ececec; max-width:640px; min-width:300px; box-shadow: 8px 8px 15px #b9b9b9; }
         #smd_ebook_preview_close { float:right; cursor:pointer; margin-left:1em;}
         #smd_ebook_preview_content { padding:1em; }
         #smd_ebook_preview_titlebar { padding:5px; border-bottom:1px solid black; font-size:120%; background:#ccc;;}
         #smd_ebook_form { margin:0 auto; width:80%; }
         #smd_ebook_form label, #smd_ebook_create { display:block; }
         #smd_ebook_prefs input[type="text"] { width:250px }
         .smd_ebook_manager { position:relative; margin:0 auto; width:80% }
         .smd_ebook_buttons { display:inline-block; margin:0 2em; }
         .smd_ebook_report textarea { width:80%; }
         #smd_ebook_editor { display:block; width:75%}
         .smd_ebook_files { float:left; width:25% }
         .smd_ebook_mobi_options { text-align:right; }
         .smd_ebook_entity { float:left; margin:1em; }
         #smd_ebook_form label, .smd_ebook_report, .smd_ebook_manager { margin-top:1.5em; }
         .smd_ebook_file { display:block; line-height:1.5; }',
	);

	$pub_prv = get_pref('smd_ebook_privs', $smd_ebook_prefs['smd_ebook_privs']['default']);
	add_privs($smd_ebook_event, '1'. (($pub_prv) ? ','.$pub_prv: '') );
	add_privs('plugin_prefs.'.$smd_ebook_event, '1');

	register_tab('content', $smd_ebook_event, smd_ebook_gTxt('smd_ebook_tab_name'));
	register_callback('smd_ebook_dispatcher', $smd_ebook_event);
	register_callback('smd_ebook_dispatcher', 'plugin_prefs.'.$smd_ebook_event);
	register_callback('smd_ebook_welcome', 'plugin_lifecycle.'.$smd_ebook_event);
}

// ********************
// ADMIN SIDE INTERFACE
// ********************
// Plugin jump off point
function smd_ebook_dispatcher($evt, $stp) {
	global $smd_ebook_event;

	$available_steps = array(
		'smd_ebook'               => false,
		'smd_ebook_prefs'         => true,
		'smd_ebook_create'        => true,
		'smd_ebook_generate'      => true,
		'smd_ebook_loadfile'      => true,
		'smd_ebook_savefile'      => true,
		'smd_ebook_viewfile'      => true,
		'smd_ebook_test'          => true,
		'smd_ebook_tidy'          => true,
		'save_pane_state'         => true,
	);

	if ($stp == 'save_pane_state') {
		smd_ebook_save_pane_state();
	} else if (!$stp or !bouncer($stp, $available_steps)) {
		$stp = $smd_ebook_event;
	}
	$stp();
}

// ------------------------
function smd_ebook_welcome($evt, $stp) {
	$msg = '';
	switch ($stp) {
		case 'installed':
			$msg = 'Go publish!';
			break;
		case 'deleted':
			smd_ebook_prefs_remove(0);
			break;
	}
	return $msg;
}

// ------------------------
// Stub with correct signature for being called via Txp
function smd_ebook($evt='', $stp='') {
	smd_ebook_ui();
}

// ------------------------
// Interface for compiling the book
function smd_ebook_ui($msg='', $listfile='', $report = '', $retval='') {
	global $smd_ebook_event, $smd_ebook_prefs, $smd_ebook_styles, $prefs;

	pagetop(smd_ebook_gTxt('smd_ebook_tab_name'), $msg);
	extract(smd_ebook_buttons('mgr'));

	$btnbar = (has_privs('plugin_prefs.'.$smd_ebook_event))? '<span class="smd_ebook_buttons">'.$btnMgr.$btnPrf.$btnCln.'</span>' : '';

	// Inject styles
	echo n.'<style type="text/css">' . $smd_ebook_styles['cpanel'] . '</style>';
	echo n.'<div id="'.$smd_ebook_event.'_control" class="txp-control-panel">' . $btnbar . '</div>';

	if (!$listfile) {
		// Stage 1: Gather the info and create the content

		// Figure out if the various fields are coming from CFs or from user-supplied text areas
		$fields = array(
			'description' => array(
				'html' => 'textarea',
			),
			'authornote' => array(
				'html' => 'textarea',
			),
			'title' => array(
				'html'     => 'text_input',
				'required' => true,
			),
			'chaptitle' => array(
				'html'       => 'text_input',
				'hide_empty' => true,
			),
			'author' => array(
				'html' => 'text_input',
			),
			'publisher' => array(
				'html' => 'text_input',
			),
			'subject' => array(
				'html' => 'text_input',
			),
			'srp' => array(
				'html' => 'text_input',
			),
		);

		$cfs = getCustomFields();

		foreach ($fields as $field => $data) {
			$data['value'] = get_pref('smd_ebook_fld_'.$field, $smd_ebook_prefs['smd_ebook_fld_'.$field]['default']);
			$data['column'] = is_numeric($data['value']) ? 'custom_'.$data['value'] : $data['value'];
			$data['name'] = (is_numeric($data['value']) && isset($cfs[$data['value']])) ? $cfs[$data['value']] : $data['value'];
			$data['content'] = ($data['value'] == 'SMD_FIXED') ? get_pref('smd_ebook_fld_'.$field.'_fixed', '') : '';
			$data['required'] = isset($data['required']) ? $data['required'] : false;
			$data['hide_empty'] = isset($data['hide_empty']) ? $data['hide_empty'] : false;
			${'ip_'.$field} = '<div class="smd_ebook_entity">' . ( ($data['content'] || ($data['name']=='' && $data['hide_empty']))
				? hInput('smd_ebook_fld_'.$field, htmlspecialchars($data['content']))
				: '<label for="smd_ebook_fld_'.$field.'">' . smd_ebook_gTxt('smd_ebook_lbl_'.$field) . '</label>'
					. ( ($data['column'])
						? hInput('smd_ebook_fld_'.$field, 'SMD_FLD_'.$data['column'])
							. '<span class="smd_preselected">' . smd_ebook_gTxt('smd_ebook_from').' '.str_replace('SMD_FLD_', '', $data['name']) . '</span>'
						: ( ($data['html'] == 'textarea')
							? text_area('smd_ebook_fld_'.$field, '150', '250', '', 'smd_ebook_fld_'.$field)
							: fInput('text', 'smd_ebook_fld_'.$field, '', '', '', '', '', '', 'smd_ebook_fld_'.$field, '', $data['required'])
						)
					)
			) . '</div>';
		}

		$where = array('1=1');
		$sec = get_pref('smd_ebook_section', $smd_ebook_prefs['smd_ebook_section']['default']);
		if ($sec) {
			$where[] = "Section='" . doSlash($sec) . "'";
		}

		$articles = safe_rows('*', 'textpattern', join(' AND ', $where). ' ORDER BY url_title');

		// Build dropdown list of articles: not using selectInput() because it doesn't support multiples
		$alist = array();
		$alist[] = '<select name="smd_ebook_articles[]" id="smd_ebook_articles" class="list multiple" multiple="multiple" size="12" required="">';
		foreach ($articles as $row) {
			$alist[] = '<option value="smd_ebook_article_'.htmlspecialchars($row['ID']).'">' . htmlspecialchars($row['Title']) . '</option>';
		}
		$alist[] = '</select>';

		echo n.'<div id="'.$smd_ebook_event.'_container" class="txp-container">';
		echo n.'<form id="smd_ebook_form" action="index.php" method="post">';
		echo n.'<div class="smd_ebook_entity"><label for="smd_ebook_articles">' . smd_ebook_gTxt('smd_ebook_lbl_articles') . '</label>';
		echo join(n, $alist);
		echo n.'</div>';
		echo n. $ip_description .n. $ip_authornote .n. $ip_title .n. $ip_chaptitle .n. $ip_author .n. $ip_publisher .n.$ip_subject .n. $ip_srp;
		echo n.'<div class="smd_clear"></div>';
		echo n.fInput('submit', 'smd_ebook_create', smd_ebook_gTxt('smd_ebook_lbl_create'), 'publish', '', '', '', '', 'smd_ebook_create');
		echo n.eInput($smd_ebook_event);
		echo n.sInput('smd_ebook_create');
		echo n.tInput();
		echo '</form>';
		echo '</div>';

	} else {
		// Stage 2: Edit the content and generate the kindle file
		$titlePrefix = smd_ebook_gTxt('smd_ebook_preview_prefix');

		$qs = array(
			"event" => $smd_ebook_event,
		);
		$qsVars = "index.php".join_qs($qs);

		echo <<<EOJS
<script type="text/javascript">
var smd_ebook_currfile;

jQuery(function() {
	// Load a file into the editor
	jQuery('.smd_ebook_files .smd_ebook_file').click(function(ev) {
		ev.preventDefault();

		// Spinner and user feedback
		var form = jQuery(this).closest('form');
		form.addClass('busy');
		s = jQuery(ev.currentTarget);
		s.after('<span class="spinner"></span>')

		var me = jQuery(this);
		var name = me.text();
		smd_ebook_currfile = name;

		jQuery.post('{$qsVars}',
			{
				step: 'smd_ebook_loadfile',
				name: name,
				_txp_token: textpattern._txp_token
			},
			function(data) {
				jQuery('.smd_ebook_files .smd_ebook_file').removeClass('smd_selected');
				me.toggleClass('smd_selected');
				jQuery('#smd_ebook_editor').val(jQuery(data).find('smd_ebook_filedata').attr('value'));
				form.removeClass('busy');
				jQuery('span.spinner').remove();
			}
		);
	});

	// Save the current file back to the file system
	jQuery('.smd_ebook_filesave').click(function(ev) {
		ev.preventDefault();

		// Spinner and user feedback
		var form = jQuery(this).closest('form');
		form.addClass('busy');
		s = jQuery(ev.currentTarget);
		s.after('<span class="spinner"></span>')

		var content = jQuery('#smd_ebook_editor').val();

		jQuery.post('{$qsVars}',
			{
				step: 'smd_ebook_savefile',
				name: smd_ebook_currfile,
				data: content,
				_txp_token: textpattern._txp_token
			},
			function(data) {
				jQuery('.smd_ebook_files .smd_ebook_file').removeClass('smd_selected');
				jQuery('#smd_ebook_editor').val('');
				form.removeClass('busy');
				jQuery('span.spinner').remove();
			}
		);
	});

	// Preview an html file in its own popup window
	var smd_ebook_previewing = 0;
	jQuery('.smd_ebook_files .smd_ebook_view').click(function(ev) {
		ev.preventDefault();

		// Spinner and user feedback
		var form = jQuery(this).closest('form');
		form.addClass('busy');
		s = jQuery(ev.currentTarget);
		s.after('<span class="spinner"></span>')

		var me = jQuery(this).prev();
		var name = me.text();

		jQuery.post('{$qsVars}',
			{
				step: 'smd_ebook_viewfile',
				name: name,
				_txp_token: textpattern._txp_token
			},
			function(data) {
				// Grab body text and inject it into the preview container
				jQuery('#smd_ebook_preview_content').empty().append(jQuery(data).find('smd_ebook_filedata').attr('value'));
				jQuery('#smd_ebook_preview_title').text('{$titlePrefix} ' + name);
				if ((jQuery(ev.target).hasClass('smd_ebook_view')) && !smd_ebook_previewing) {
					smd_ebook_prevu();
				}
				form.removeClass('busy');
				jQuery('span.spinner').remove();
			}
		);
	});

	function smd_ebook_prevu() {
		jQuery('#smd_ebook_preview').toggle('fast');
		smd_ebook_previewing = !smd_ebook_previewing;
	}

	function smd_ebook_prevu_bind() {
		jQuery('#smd_ebook_preview_close').click(function(){
			smd_ebook_prevu();
		});
		jQuery(document).keypress(function(e){
			if(e.keyCode==27 && smd_ebook_previewing) {
				smd_ebook_prevu();
			}
		});
	}
	smd_ebook_prevu_bind();
});
</script>
EOJS;
		echo n.'<div id="'.$smd_ebook_event.'_container" class="txp-container">';
		echo n.'<form id="smd_ebook_form" action="index.php" method="post">';

		echo n.'<div class="smd_ebook_report">'
			. hed(smd_ebook_gTxt('smd_ebook_lbl_report'), 2)
			. '<textarea id="smd_ebook_report" cols=80 rows="6">'.$report.'</textarea>'
			. '</div>';

		echo n.'<div class="smd_ebook_manager">';

		// 'Generate book' and 'download' buttons
		echo '<div class="smd_ebook_mobi_options">';
		echo n.fInput('submit', 'smd_ebook_generate', smd_ebook_gTxt('smd_ebook_lbl_generate'), 'publish smd_ebook_mobi');
		if ($retval <= 1) {
			$info = explode ('.',$listfile);
			$basepart = array_slice($info, 0, count($info)-1);
			$mobifile = join('', $basepart) . '.mobi';
			echo n.hInput('smd_ebook_mobifile', $mobifile);
			echo n.fInput('submit', 'smd_ebook_to_files', smd_ebook_gTxt('smd_ebook_lbl_to_files'), 'publish smd_ebook_mobi');;
			echo n.fInput('submit', 'smd_ebook_download', smd_ebook_gTxt('smd_ebook_lbl_download'), 'publish smd_ebook_mobi');;
		}
		echo '</div>';

		echo n.hed(smd_ebook_gTxt('smd_ebook_lbl_files'), 2);
		echo n.'<div class="smd_ebook_files">';

		$opf_edit = get_pref('smd_ebook_opf_edit', $smd_ebook_prefs['smd_ebook_opf_edit']['default']);
		$opf_allowed = do_list($opf_edit);
		$opf_allowed[] = '1'; // Publishers can always edit .opf
		$can_opf = in_array($GLOBALS['privs'], $opf_allowed);

		$files = file($prefs['tempdir'] . DS . $listfile);

		foreach ($files as $file) {
			$info = explode ('.',$file);
			$lastpart = count($info)-1;
			$ext = trim($info[$lastpart]);
			if ($ext == 'opf') {
				echo n.hInput('smd_ebook_opf_file', $file);
			}
			if ($ext != 'opf' || ($ext == 'opf' && $can_opf)) {
				echo n.'<a href="#" class="smd_ebook_file">'.trim($file).'</a>';
				if ($ext == 'html') {
					echo n.'<a href="#" class="smd_ebook_view">'.smd_ebook_gTxt('smd_ebook_lbl_view').'</a>';
				}
			}
		}

		echo n.'<button class="smd_ebook_filesave smallerbox">'.gTxt('save').'</button>';
		echo n.'</div>';

		echo n.'<div class="smd_ebook_editor">';
		echo n.'<textarea id="smd_ebook_editor" cols="60" rows="25"></textarea>';
		echo n.'</div>';
		echo n.'<div id="smd_ebook_preview">
			<div id="smd_ebook_preview_titlebar">
				<span id="smd_ebook_preview_title"></span>
				<a id="smd_ebook_preview_close">X</a>
			</div>
			<div id="smd_ebook_preview_content"></div>
			</div>';

		echo n.hInput('smd_ebook_listfile', $listfile);
		echo n.eInput($smd_ebook_event);
		echo n.sInput('smd_ebook_generate');
		echo n.tInput();
		echo n.'</div>';
		echo n.'</form>';
		echo n.'<div class="smd_clear"></div>';
		echo n.'</div>';
	}
}

// ------------------------
function smd_ebook_loadfile() {
	global $prefs;

	$name = sanitizeForFile(ps('name'));
	$file = file($prefs['tempdir'] . DS . $name);
	if ($file) {
		send_xml_response(array('smd_ebook_filedata' => str_replace(array("'"), array('&#039;'), join('', $file)) ));
	} else {
		send_xml_response(array('http-status' => '400 Bad Request'));
	}
	exit; // Don't display page_end
}

// ------------------------
function smd_ebook_savefile() {
	global $prefs;

	$name = sanitizeForFile(ps('name'));
	$content = ps('data');
	$fp = fopen($prefs['tempdir'] . DS . $name, "wb");
	fwrite($fp, trim($content));
	fclose($fp);
	if ($fp) {
		send_xml_response();
	} else {
		send_xml_response(array('http-status' => '400 Bad Request'));
	}
	exit; // Don't display page_end
}

// ------------------------
// Extract a subset of the HTML file for display
function smd_ebook_viewfile() {
	global $prefs, $path_to_site;

	$name = sanitizeForFile(ps('name'));
	$file = file_get_contents($prefs['tempdir'] . DS . $name);

	if ($file) {
		$doc = new DOMDocument();
		$doc->loadHTML($file);
		$domxpath = new DOMXpath($doc);
		$newDoc = new DOMDocument('1.0','UTF-8');

		$nodeStyle = $domxpath->query('//style');
		$nodeList = $domxpath->query('//body');

		// Create a new document and import the document subsets
		$newDoc->appendChild($newDoc->importNode($nodeStyle->item(0), true));
		$newDoc->appendChild($newDoc->importNode($nodeList->item(0), true));
		$out = $newDoc->saveHTML();

		send_xml_response(array('smd_ebook_filedata' => str_replace(array("'", $path_to_site.DS), array('&#039;', ihu), $out) ));
	} else {
		send_xml_response(array('http-status' => '400 Bad Request'));
	}
	exit; // Don't display page_end
}

// ------------------------
function smd_ebook_templates() {
	// .opf file template
	$template['opf'] = <<<EOOPF
<?xml version="1.0" encoding="{smd_ebook_encoding}"?>
<package xmlns="http://www.idpf.org/2007/opf" version="2.0" {smd_ebook_uid_ref}>
	<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
		{smd_ebook_md_uid}
		{smd_ebook_md_title}
		{smd_ebook_md_lang}
		{smd_ebook_md_creator}
		{smd_ebook_md_date}
		{smd_ebook_md_description}
		{smd_ebook_md_subject}
		{smd_ebook_md_publisher}
		{smd_ebook_md_cover}
		<x-metadata>
			<output encoding="{smd_ebook_encoding}" content-type="text/x-oeb1-document"></output>
			{smd_ebook_md_srp}
		</x-metadata>
	</metadata>

	<manifest>
		{smd_ebook_manifest_ncx}
		{smd_ebook_manifest_cover}
		{smd_ebook_manifest_authornote}
		{smd_ebook_manifest_toc}
		{smd_ebook_manifest_items}
	</manifest>

	<spine {smd_ebook_spine_ncx_ref}>
		{smd_ebook_spine_ncx}
		{smd_ebook_spine_authornote}
		{smd_ebook_spine_toc}
		{smd_ebook_spine_items}
	</spine>

	<guide>
		{smd_ebook_guide_toc}
		{smd_ebook_guide_start}
	</guide>
</package>
EOOPF;

	// .ncx file template
	$template['ncx'] = <<<EONCX
<?xml version="1.0" encoding="{smd_ebook_encoding}"?>
<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN"
"http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1" xml:lang="{smd_ebook_lang}">
	<head>
		{smd_ebook_dtb_uid}
	</head>

	<docTitle><text>{smd_ebook_title}</text></docTitle>
	<docAuthor><text>{smd_ebook_creator}</text></docAuthor>
	<navMap>
		{smd_ebook_ncx_map}
	</navMap>
</ncx>
EONCX;

	// navPoint template (a portion of the .ncx file)
	$template['nav'] = <<<EONAV
		<navPoint class="titlepage" id="{smd_ebook_nav_hash}" playOrder="{smd_ebook_nav_idx}">
			<navLabel><text>{smd_ebook_nav_label}</text></navLabel>
			<content src="{smd_ebook_file_name}#{smd_ebook_nav_hash}" />
		</navPoint>
EONAV;

	// TOC template
	$template['toc'] = <<<EOTOC
<html>
<head>
	<title>Table of Contents</title>
	{smd_ebook_stylesheet}
</head>
<body>
	{smd_ebook_toc_list}
</body>
</html>
EOTOC;

	// HTML template
	$template['doc'] = <<<EOTOC
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={smd_ebook_encoding}" />
	<title>{smd_ebook_title}</title>
	{smd_ebook_stylesheet}
</head>
<body>
	{smd_ebook_chaptitle}
	{smd_ebook_contents}
</body>
</html>
EOTOC;

	return $template;
}

// ------------------------
// Stage 1: create the files necessary for generation of the book.
// The actual generation via kindlegen is a separate step.
function smd_ebook_create() {
	global $smd_ebook_prefs, $prefs, $img_dir;

	@include_once txpath.'/lib/classTextile.php';
	@include_once txpath.'/publish.php'; // for parse()
	$textile = new Textile();

	$template = smd_ebook_templates();
	$msg = '';
	$report = $toc = $ncx = $reps = array();

	// Get Textile and encoding options
	$encoding = get_pref('smd_ebook_encoding', $smd_ebook_prefs['smd_ebook_encoding']['default']);
	$which = get_pref('smd_ebook_textile', $smd_ebook_prefs['smd_ebook_textile']['default']);
	$txt_description = in_list('description', $which);
	$txt_authornote = in_list('authornote', $which);

	// Build up a giant replacement table which is then substituted into
	// the various templates before passing to kindlegen

	// Populate the unique ID entries direcly into the .opf template as they're only used once each
	$uid = get_pref('smd_ebook_uid', $smd_ebook_prefs['smd_ebook_uid']['default']);

	$template['opf'] = str_replace('{smd_ebook_uid_ref}', (($uid) ? 'unique-identifier="uid"' : ''), $template['opf']);
	$template['opf'] = str_replace('{smd_ebook_md_uid}', (($uid) ? '<dc:identifier id="uid">' . $uid . '</dc:identifier>' : ''), $template['opf']);
	$template['ncx'] = str_replace('{smd_ebook_dtb_uid}', (($uid) ? '<meta name="dtb:uid" content="uid"/>' : ''), $template['ncx']);

	// Set up the TOC wrappers
	$toc_wrap = get_pref('smd_ebook_toc_wraptag', $smd_ebook_prefs['smd_ebook_toc_wraptag']['default']);
	$toc_class = get_pref('smd_ebook_toc_class', $smd_ebook_prefs['smd_ebook_toc_class']['default']);
	$wrapit = ($toc_wrap == 'ol') ? '#' : '*';

	$article_cnt = $ncx_cnt = $elem_cnt = 0;
	$article_refs = $article_spines = array();

	// Page break, stylesheet and heading references
	$pbr = get_pref('smd_ebook_page_break', $smd_ebook_prefs['smd_ebook_page_break']['default']);
	$css = get_pref('smd_ebook_stylesheet', $smd_ebook_prefs['smd_ebook_stylesheet']['default']);
	$hdg = get_pref('smd_ebook_heading_level', $smd_ebook_prefs['smd_ebook_heading_level']['default']);

	if ($css) {
		$sheet = safe_field('css', 'txp_css', "name='" . doSlash($css) . "'");
	}
	$sheet = ($sheet) ? '<style type="text/css">' . $sheet . '</style>' : '';


	// Loop for each article in the collection
	foreach (ps('smd_ebook_articles') as $artid) {
		$article_cnt++;

		$id = str_replace('smd_ebook_article_', '', $artid);
		$row = safe_row('*', 'textpattern', "ID = '" . doSlash($id) . "'");

		if ($row) {
			// Initialize a few things
			$note_content = '';
			$reps['{smd_ebook_file_name}'] = $row['url_title'] . '.html';
			$reps['{smd_ebook_encoding}'] = $encoding;
			$cur_file = $row['url_title'] . '.html';

			// Each of the items starting !isset() are only ever loaded _once_ from the
			// first article in which they are found.
			// Begin by setting up the file names
			if (!isset($firstfile)) {
				$firstfile = $row['url_title'] . '.html';
				$listfile = $row['url_title'] . '.smd';
				$notefile = $row['url_title'] . '_notes.html';
				$toc_file = $row['url_title'] . '_toc.html';
				$ncx_file = $row['url_title'] . '.ncx';
				$opf_file = $row['url_title'] . '.opf';
			}
			// Language
			if (!isset($reps['{smd_ebook_md_lang}'])) {
				$reps['{smd_ebook_md_lang}'] = '<dc:language>'.$prefs['language'].'</dc:language>';
				$reps['{smd_ebook_lang}'] = $prefs['language'];
			}
			// Author can come from:
			//  1) an article field
			//  2) the current logged in user
			//  3) user-supplied at book creation time
			//  4) hard-coded in plugin settings
			if (!isset($reps['{smd_ebook_md_creator}'])) {
				$val = ps('smd_ebook_fld_author');
				if (strpos($val, 'SMD_FLD_') !== false) {
					$valfld = str_replace('SMD_FLD_', '', $val);
					$val = isset($row[$valfld]) ? ( ($valfld == 'AuthorID') ? get_author_name($row[$valfld]) : $row[$valfld] ) : '';
				}
				if ($val) {
					$reps['{smd_ebook_md_creator}'] = '<dc:creator>'.$val.'</dc:creator>';
					$reps['{smd_ebook_creator}'] = $val;
				}
			}
			// Publication date
			// TODO: reformat?
			if (!isset($reps['{smd_ebook_md_date}'])) {
				$reps['{smd_ebook_md_date}'] = '<dc:date>'.($row['Posted']).'</dc:date>';
			}

			// Cover image
			if (!isset($reps['{smd_ebook_md_cover}'])) {
				if (isset($row['Image'])) {
					$img = safe_row('*', 'txp_image', "id='" . intval($row['Image']) . "'");

					// Only GIFs and JPGs need apply
					$mime_type = (($img['ext'] == '.jpg') ? 'image/jpeg' : (($img['ext'] == '.gif') ? 'image/gif' : ''));
					if ($mime_type) {
						$reps['{smd_ebook_md_cover}'] = '<meta name="cover" content="cover-image" />';
						$reps['{smd_ebook_manifest_cover}'] = '<item id="cover-image" media-type="'.$mime_type.'" href="' . $prefs['path_to_site'] . DS . $img_dir . DS . $img['id'] . $img['ext'] . '" />';
					}
				}
			}

			// The following values can either come from the given field or be used verbatim
			// Firstly the title, description, subject, publisher and chapter title
			$setMany = array('chaptitle');
			foreach (array('title', 'description', 'subject', 'publisher', 'chaptitle') as $thingy) {
				if (in_array($thingy, $setMany) || !isset($reps['{smd_ebook_md_'.$thingy.'}'])) {
					$val = ps('smd_ebook_fld_'.$thingy);
					if (strpos($val, 'SMD_FLD_') !== false) {
						$valfld = str_replace('SMD_FLD_', '', $val);
						$val = isset($row[$valfld]) ? $row[$valfld] : '';
					}
					if ($val) {
						// Textile the content?
						$content = (isset($txt_{$thingy}) && $txt_{$thingy}) ? trim($textile->TextileThis($val)) : trim($val);
						if (!in_array($thingy, $setMany)) {
							$reps['{smd_ebook_md_'.$thingy.'}'] = '<dc:'.$thingy.'>' . $content . '</dc:'.$thingy.'>';
						}

						// There are two titles: one for the metadata and one raw so if the title
						// has just been found, populate the raw title too
						if ($thingy == 'title') {
							$reps['{smd_ebook_title}'] = $content;
						} else if ($thingy == 'chaptitle') {
							// Chapter title has an associated heading level
							$reps['{smd_ebook_chaptitle}'] = '<h'.$hdg.'>'.$content.'</h'.$hdg.'>';
						}
					}
				}
			}

			// Price (SRP) can also come from a field but it needs some special jiggery pokery
			if (!isset($reps['{smd_ebook_md_srp}'])) {
				$val = ps('smd_ebook_fld_srp');
				if (strpos($val, 'SMD_FLD_') !== false) {
					$valfld = str_replace('SMD_FLD_', '', $val);
					$val = isset($row[$valfld]) ? $row[$valfld] : '';
				}
				if ($val) {
					$parts = do_list($val, '|');
					$parts[0] = $parts[0] ? $parts[0] : '0.00';
					$parts[1] = (isset($parts[1]) && $parts[1]) ? $parts[1] : get_pref('smd_ebook_currency', $smd_ebook_prefs['smd_ebook_currency']['default']);
					$reps['{smd_ebook_md_srp}'] = '<SRP Currency="'.$parts[1].'">'.$parts[0].'</SRP>';
				}
			}

			// Authornote is slightly different because it needs storing as a file,
			// and needs adding to the .ncx (but not to the TOC)
			if (!isset($reps['{smd_ebook_manifest_authornote}'])) {
				$val = ps('smd_ebook_fld_authornote');
				if (strpos($val, 'SMD_FLD_') !== false) {
					$valfld = str_replace('SMD_FLD_', '', $val);
					$val = isset($row[$valfld]) ? $row[$valfld] : '';
				}
				if ($val) {
					$reps['{smd_ebook_manifest_authornote}'] = '<item id="smd_ebook_notes" media-type="text/html" href="'.$notefile.'" />';
					$reps['{smd_ebook_spine_authornote}'] = '<itemref idref="smd_ebook_notes" />';
					$note_content = '<span id="smd_ebook_notes"></span>' . (($txt_authornote) ? $textile->TextileThis($val) : $val);

					// While it's 99% likely the actual title used for the eventual book has been found,
					// there's a slim chance it hasn't. In that case, the current row's title is used as a fallback
					$note_title = isset($reps['{smd_ebook_title}']) ? $reps['{smd_ebook_title}'] : $row['Title'];
					$note_content = str_replace(array('{smd_ebook_encoding}', '{smd_ebook_title}', '{smd_ebook_chaptitle}', '{smd_ebook_stylesheet}', '{smd_ebook_contents}'), array($encoding, $note_title, '', $sheet, $note_content), $template['doc']);

					$fp = fopen($prefs['tempdir'].DS.$notefile, "wb");
					fwrite($fp, trim($note_content));
					fclose($fp);
					$lfout[] = $notefile;

					// Add it to the .ncx
					$ncx_cnt++;
					$from = array('{smd_ebook_file_name}', '{smd_ebook_nav_label}', '{smd_ebook_nav_hash}', '{smd_ebook_nav_idx}');
					$to = array($notefile, smd_ebook_gTxt('smd_ebook_lbl_authornote'), 'smd_ebook_notes', $ncx_cnt);
					$ncx[] = str_replace($from, $to, $template['nav']);
				}
			}

			// Note:
			//  1) a full (well-formed, hopefully) HTML file (from <html>...</html>) is generated
			//     here so the loadHTML() method is happy. The body will need reinjecting into
			//     the template after the ToC has been generated.
			//  2) The current HTML file's title is used instead of the overall book title.
			//  3) parse() is called twice to simulate secondpass.
			$chap_title = isset($reps['{smd_ebook_chaptitle}']) ? $reps['{smd_ebook_chaptitle}'] : '';
			article_format_info($row); // Load article context
			$html_content = str_replace(array('{smd_ebook_encoding}', '{smd_ebook_title}', '{smd_ebook_chaptitle}', '{smd_ebook_stylesheet}', '{smd_ebook_contents}'), array($encoding, $row['Title'], $chap_title, $sheet, parse(parse($row['Body_html']))), $template['doc']);

			// Trawl through the HTML content, either:
			//  a) pulling out the ToC entries.
			//  b) creating ToC entries if the pref allows
			$autotoc = get_pref('smd_ebook_auto_toc', $smd_ebook_prefs['smd_ebook_auto_toc']['default']);
			$doc = new DOMDocument();
			$dom_ok = $doc->loadHTML($html_content);
			if ($dom_ok) {
				$items = $doc->getElementsByTagName('*');
				$offset = $toc_cnt = 0;
				foreach ($items as $item) {
					if ($autotoc && !$item->hasAttribute('id') && preg_match('/h([1-6])/i', $item->nodeName, $matches)) {
						// It's a heading. Make the anchor chain based on the heading level
						$anchor_parts = array_fill(0, $matches[1], 'sub');
						$anchor = join('-', $anchor_parts). ++$elem_cnt;
						$item->setAttribute('id', $anchor);
					}

					if ($item->hasAttribute('id')) {
						$ncx_cnt++;
						$toc_cnt++;
						$hashval = $item->getAttribute('id');
						if ( (!isset($reps['{smd_ebook_guide_start}'])) && ($toc_cnt == 1) ) {
							$reps['{smd_ebook_guide_start}'] = '<reference type="text" title="'.smd_ebook_gTxt('smd_ebook_lbl_start').'" href="'.$firstfile.'#'.$hashval.'"></reference>';
						}

						// mb_convert_encoding() seems to bypass the odd behaviour where apostrophes
						// would appear in the TOC as â€™. This may actually be a band-aid to circumvent
						// problems with the encoding in DOMDocument: perhaps if appropriate encoding is
						// used there, this hack won't be necessary
						$node = mb_convert_encoding($item->nodeValue, 'HTML-ENTITIES', 'utf-8');
						$from = array('{smd_ebook_file_name}', '{smd_ebook_nav_label}', '{smd_ebook_nav_hash}', '{smd_ebook_nav_idx}');
						$to = array($cur_file, $node, $hashval, $ncx_cnt);
						$ncx[] = str_replace($from, $to, $template['nav']);

						// Now it's the turn of the HTML TOC. Utilise Textile here to
						// create the toc list from ul or ol syntax
						$hashBits = do_list($hashval, '-');
						$indent = count($hashBits);

						if ( ($toc_cnt == 1) && ($indent > 1) ) {
							// Doesn't start with h1 (begins h2, maybe) so scale back the indent.
							// Without this, Textile produces invalid markup
							$offset = $indent - 1;
						}

						$toc_cls = (($toc_cnt == 1) && $toc_class) ? '('.$toc_class.')' : '';
						$toc[] = str_pad('', max(1, $indent-$offset), $wrapit) . $toc_cls.' ' . href($node, $cur_file.'#'.$hashval);
					}
				}

				// Grab any changes just made to the DOM tree in case anchors have been added.
				// Note _only_ the <body> is extracted since the XML headers that come with a full
				// saveXML() get in the way. Also note that saveHTML() is not being used because its
				// 'node' parameter wasn't added until PHP 5.3.6 which would affect the plugin's
				// base requirements.
				// Hackish: remove the body tag wrapper with substr() so when the html_content
				// is shoved back into the template (which has a body tag already) there's no
				// tag duplication
				$html_content = substr($doc->saveXML($doc->getElementsByTagName('body')->item(0)), 6, -7);

				// Swap out any line break placeholders. Note that the line break is replaced twice:
				// once to get rid of any surrounding <p> tags that Textile may have introduced around
				// the marker, and again in case a few of them didn't get paragraph tags.
				$html_content = str_replace('<p>'.$pbr.'</p>', '<mbp:pagebreak />', $html_content);
				$html_content = str_replace($pbr, '<mbp:pagebreak />', $html_content);

				// Pass the extracted <body> tree into the doc template again so it regenerates
				// the full <html>...</html> document.
				$html_content = str_replace(array('{smd_ebook_encoding}', '{smd_ebook_title}', '{smd_ebook_chaptitle}', '{smd_ebook_stylesheet}', '{smd_ebook_contents}'), array($encoding, $row['Title'], '', $sheet, $html_content), $template['doc']);
			} else {
				trigger_error(smd_ebook_gTxt('smd_ebook_malformed'), E_WARNING);
			}

			// Write the final HTML document to the file system
			$fp = fopen($prefs['tempdir'].DS.$cur_file, "wb");
			fwrite($fp, trim($html_content));
			fclose($fp);

			$lfout[] = $cur_file;
			$article_refs[] = '<item id="smd_ebook_item_'.$article_cnt.'" media-type="text/html" href="'.$row['url_title'].'.html" />';
			$article_spines[] = '<itemref idref="smd_ebook_item_' . $article_cnt . '" />';

		}
	}

	// Ensure any NULL replacements are cleared or throw errors
	$reps['{smd_ebook_chaptitle}'] = (!isset($reps['{smd_ebook_chaptitle}'])) ? '' : $reps['{smd_ebook_chaptitle}'];
	$reps['{smd_ebook_creator}'] = (!isset($reps['{smd_ebook_creator}'])) ? '' : $reps['{smd_ebook_creator}'];
	$reps['{smd_ebook_md_creator}'] = (!isset($reps['{smd_ebook_md_creator}'])) ? '' : $reps['{smd_ebook_md_creator}'];
	$reps['{smd_ebook_md_description}'] = (!isset($reps['{smd_ebook_md_description}'])) ? '' : $reps['{smd_ebook_md_description}'];
	$reps['{smd_ebook_md_subject}'] = (!isset($reps['{smd_ebook_md_subject}'])) ? '' : $reps['{smd_ebook_md_subject}'];
	$reps['{smd_ebook_md_publisher}'] = (!isset($reps['{smd_ebook_md_publisher}'])) ? '' : $reps['{smd_ebook_md_publisher}'];
	$reps['{smd_ebook_md_srp}'] = (!isset($reps['{smd_ebook_md_srp}'])) ? '' : $reps['{smd_ebook_md_srp}'];
	$reps['{smd_ebook_guide_start}'] = (!isset($reps['{smd_ebook_guide_start}'])) ? '' : $reps['{smd_ebook_guide_start}'];
	if (!isset($reps['{smd_ebook_md_cover}'])) {
		$reps['{smd_ebook_md_cover}'] = '';
		$reps['{smd_ebook_manifest_cover}'] = '';
	}
	if (!isset($reps['{smd_ebook_manifest_authornote}'])) {
		$reps['{smd_ebook_manifest_authornote}'] =  '';
		$reps['{smd_ebook_spine_authornote}'] =  '';
	}

	// All the replacements are set up so prepare for kindlegen
	// First, create the TOC and write it to the filesystem
	if ($toc_cnt > 0) {
		$reps['{smd_ebook_manifest_toc}'] = '<item id="toc" media-type="text/html" href="'.$toc_file.'" />';
		$reps['{smd_ebook_spine_toc}'] = '<itemref idref="toc" />';
		$reps['{smd_ebook_guide_toc}'] = '<reference type="toc" title="' . smd_ebook_gTxt('smd_ebook_toc') . '" href="'.$toc_file.'"></reference>';
		$html_toc = $textile->TextileThis(join(n, $toc));
		$html_toc = str_replace(array('{smd_ebook_toc_list}', '{smd_ebook_stylesheet}'), array($html_toc, $sheet), $template['toc']);
		$fp = fopen($prefs['tempdir'] . DS . $toc_file, "wb");
		fwrite($fp, trim($html_toc));
		fclose($fp);
		$lfout[] = $toc_file;
	} else {
		$reps['{smd_ebook_manifest_toc}'] = '';
		$reps['{smd_ebook_spine_toc}'] = '';
		$reps['{smd_ebook_guide_toc}'] = '';
	}

	// Add the ncx waypoints to the reps array and generate the .ncx file
	if ($ncx_cnt > 0) {
		$reps['{smd_ebook_ncx_map}'] = join(n, $ncx);
		$reps['{smd_ebook_manifest_ncx}'] = '<item id="ncx" media-type="application/x-dtbncx+xml" href="'.$ncx_file.'" />';
		$reps['{smd_ebook_spine_ncx}'] = '<itemref idref="ncx" />';
		$reps['{smd_ebook_spine_ncx_ref}'] = 'toc="ncx"';
		$ncx_file_content = strtr($template['ncx'], $reps);
		$fp = fopen($prefs['tempdir'] . DS . $ncx_file, "wb");
		fwrite($fp, trim($ncx_file_content));
		fclose($fp);
		$lfout[] = $ncx_file;
	} else {
		$reps['{smd_ebook_manifest_ncx}'] = '';
		$reps['{smd_ebook_spine_ncx}'] = '';
		$reps['{smd_ebook_spine_ncx_ref}'] = '';
	}

	// Build the remaining manifest replacements and generate the OPF
	$reps['{smd_ebook_manifest_items}'] = join(n, $article_refs);
	$reps['{smd_ebook_spine_items}'] = join(n, $article_spines);

	$opf_file_content = strtr($template['opf'], $reps);
	$fp = fopen($prefs['tempdir'] . DS . $opf_file, "wb");
	fwrite($fp, trim($opf_file_content));
	fclose($fp);
	$lfout[] = $opf_file;

	// Write the listfile, which contains a list of all the files used in this stage
	$fp = fopen($prefs['tempdir'] . DS . $listfile, "wb");
	fwrite($fp, join(n, $lfout));
	fclose($fp);

	// Hand off to Stage 2 to do the deed
	smd_ebook_generate($listfile, $opf_file);
}

// ------------------------
// Stage 2 only: Pre-requisites are that the necessary files (toc, .html, ncx + opf)
// have already been generated by the previous stage. If called directly via the
// GUI, the hidden form value containing the OPF file is read.
function smd_ebook_generate($listfile='', $opf_file='') {
	global $prefs, $smd_ebook_prefs;

	$report = array();
	$retval = NULL;

	// Use passed in values in lieu of the one in the form
	$opf_file = ($opf_file) ? $opf_file : ps('smd_ebook_opf_file');
	$listfile = ($listfile) ? $listfile : ps('smd_ebook_listfile');

	// .mobifile credentials
	$mobifile = ps('smd_ebook_mobifile');
	$fullpath = $prefs['tempdir'] . DS . $mobifile;
	$filesize = filesize($fullpath);

	$downloadit = ps('smd_ebook_download');
	$fileit = ps('smd_ebook_to_files');

	if ($downloadit) {
		ob_clean();
		header('Content-Description: File Download');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$mobifile.'"; size = "'.$filesize.'"');
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: no-cache, must-revalidate, max-age=60");
		header("Expires: Sat, 01 Jan 2000 12:00:00 GMT");
		header('Cache-Control: private');
		@set_time_limit(0);
		if ($file = fopen($fullpath, 'rb')) {
			while(!feof($file) and (connection_status()==0)) {
				echo fread($file, 1024*64);
				ob_flush();
				flush();
			}
			fclose($file);
		}
		exit;

	} else if ($fileit) {

		@include_once txpath.'/include/txp_file.php';

		// Copy the file to the files area
		$destpath = $prefs['file_base_path'] . DS . $mobifile;
		copy($fullpath, $destpath);

		// Get the file category
		$filecat = get_pref('smd_ebook_file_cat', $smd_ebook_prefs['smd_ebook_file_cat']['default']);

		// Read description and title from .opf
		$doc = new DOMDocument();
		$content = file_get_contents($prefs['tempdir'] . DS . $opf_file);
		$dom_ok = $doc->loadXML($content);

		$description = $title = '';
		if ($dom_ok) {
			$items = $doc->getElementsByTagName('*');
			foreach ($items as $item) {
				if ($item->nodeName == 'dc:title') {
					$title = $item->nodeValue;
				}
				if ($item->nodeName == 'dc:description') {
					$description = $item->nodeValue;
				}
			}
		}

		$curid = safe_field('id', 'txp_file', "filename='".doSlash($mobifile)."'");

		if ($curid) {
			// Update existing database entry
			$ret = safe_update('txp_file',
				"
					title='" . doSlash($title) . "',
					category='" . doSlash($filecat) . "',
					description='" . doSlash($description) . "',
					size='" . doSlash($filesize) . "',
					modified= now()
				",
				"id='".doSlash($curid)."'"
			);
			if ($ret) {
				$msg = smd_ebook_gTxt('smd_ebook_updated', array('{id}' => $curid));
			} else {
				$msg = smd_ebook_gTxt('smd_ebook_not_filed');
			}

		} else {
			// Make a new entry in the database for it
			$newid = file_db_add(doSlash($mobifile), doSlash($filecat), '', doSlash($description), doSlash($filesize), doSlash($title));
			if ($newid) {
				$msg = smd_ebook_gTxt('smd_ebook_filed', array('{id}' => $newid));
			} else {
				$msg = smd_ebook_gTxt('smd_ebook_not_filed');
			}
		}

	} else {

		// (Re)generate the book
		list($report, $retval) = smd_ebook_kindlegen($opf_file);

		if ($retval > 1) {
			$msg = smd_ebook_gTxt('smd_ebook_generate_failed', array('{code}' => $retval));
		} else {
			$msg = smd_ebook_gTxt('smd_ebook_generate_ok');
		}
	}
	smd_ebook_ui($msg, $listfile, join(n, $report), $retval);
}

// ------------------------
// Interface with kindlegen to generate the .mobi file.
function smd_ebook_kindlegen($opf) {
	global $prefs, $smd_ebook_prefs;	

	$kgen = get_pref('smd_ebook_kindlegen_path', $smd_ebook_prefs['smd_ebook_kindlegen_path']['default']);
	$command = $kgen . ' ' . $prefs['tempdir'] . DS . $opf;
	exec($command, $output, $result);

	return array($output, $result);
}

// ------------------------
// Common buttons for the interface
function smd_ebook_buttons($curr='mgr') {
	global $smd_ebook_event;

	$ret = array (
		'btnMgr' => '<form method="post" action="?event='.$smd_ebook_event.'" class="smd_inline">'.fInput('submit', 'submit', smd_ebook_gTxt('smd_ebook_lbl_mgr'), 'smallerbox'.($curr=='mgr'?' smd_active':'')).tInput().'</form>',
		'btnPrf' => '<form method="post" action="?event='.$smd_ebook_event.a.'step=smd_ebook_prefs" class="smd_inline">'.fInput('submit', 'submit', smd_ebook_gTxt('smd_ebook_lbl_prf'), 'smallerbox'.($curr=='prf'?' smd_active':'')).tInput().'</form>',
		'btnCln' => '<form method="post" action="?event='.$smd_ebook_event.a.'step=smd_ebook_tidy" class="smd_inline">'.fInput('submit', 'submit', smd_ebook_gTxt('smd_ebook_lbl_cln'), 'smallerbox'.($curr=='cln'?' smd_active':'')).tInput().'</form>',
		'btnTst' => '<a href="?event='.$smd_ebook_event.a.'step=smd_ebook_test'.a.'_txp_token='.form_token().'" class="smd_inline">'.smd_ebook_gTxt('smd_ebook_lbl_tst').'</a>',
	);
	return $ret;
}

// ------------------------
// Tidy up the temp dir
function smd_ebook_tidy($msg='') {
	global $prefs, $smd_ebook_event, $smd_ebook_styles;

	require_privs('plugin_prefs.'.$smd_ebook_event);

	if (ps('smd_ebook_cleanup')) {
		$to_delete = ps('smd_ebook_files');
		foreach($to_delete as $del) {
			$path = realpath($prefs['tempdir'] . DS . $del);
			unlink($path);
		}
		$msg = smd_ebook_gTxt('smd_ebook_deleted');
	}

	pagetop(smd_ebook_gTxt('smd_ebook_tab_name'), $msg);
	extract(smd_ebook_buttons('cln'));

	$btnbar = (has_privs('plugin_prefs.'.$smd_ebook_event))? '<span class="smd_ebook_buttons">'.$btnMgr.$btnPrf.$btnCln.'</span>' : '';

	$filelist = array();
	$valid = array('mobi', 'html', 'ncx', 'opf', 'smd');
	$tmp = $prefs['tempdir'] . DS;

	// Grab all files then remove unnecessary ones: faster than multiple globs
	// for each file type and more robust than relying on GLOB_BRACE support
	$allfiles = glob($tmp.'*.*');

	foreach ($allfiles as $file) {
		$info = explode ('.',$file);
		$lastpart = count($info)-1;
		$ext = trim($info[$lastpart]);
		if (in_array($ext, $valid)) {
			$filelist[] = $file;
		}
	}

	// Inject styles
	echo '<style type="text/css">' . $smd_ebook_styles['cpanel'] . '</style>';

	echo '<div id="'.$smd_ebook_event.'_control" class="txp-control-panel">' . $btnbar . '</div>';

	$filesel = '';
	if ($filelist) {
		$filez = array();
		foreach($filelist as $val) {
			$val = basename($val);
			$key = sanitizeForFile($val);
			$filez[$key] = $val;
	   }
		$selout[] = '<select id="smd_ebook_files" name="smd_ebook_files[]" class="list" size="20" multiple="multiple">';
		foreach ($filez as $key => $leaf) {
			$selout[] = t.'<option value="'.$key.'">'.htmlspecialchars($leaf).'</option>'.n;
		}
		$selout[] = '</select>';
		$filesel = join(n, $selout);
	}

	echo '<div class="txp-list">';
	echo startTable('list');
	echo '<form method="post" action="?event='.$smd_ebook_event.'">';
	echo tr(tda(strong(smd_ebook_gTxt('smd_ebook_tidy'))));
	echo ($filesel) ? tr(tda($filesel)) : tr(tda(smd_ebook_gTxt('smd_ebook_no_files')));
	echo tr(tda(fInput('submit', 'smd_ebook_cleanup', gTxt('delete'), 'publish'), ' class="noline"'));
	echo sInput('smd_ebook_tidy');
	echo tInput();
	echo '</form>';
	echo endTable();
	echo '</div>';
}

// ------------------------
// List of current stylesheets
function smd_ebook_style_list($name, $val='') {
	$styles = safe_column('name', 'txp_css', '1=1');
	return selectInput($name, $styles, $val, true);
}

// ------------------------
// List of current file categories
function smd_ebook_file_cat_list($name, $val='') {
	$rs = getTree('root', 'file');
	if ($rs) {
		return treeSelectInput($name, $rs, $val, $name);
	}
}

// ------------------------
// List of numbers for heading levels
function smd_ebook_number($name, $val='') {
	// Can't use range() since it creates indices starting at 0
	$nums = array();
	for ($idx = 1; $idx <= 6; $idx++) {
		$nums[$idx] = $idx;
	}
	return selectInput($name, $nums, $val, false);
}

// ------------------------
// List of current sections
// TODO: multiple select?
function smd_ebook_section_list($name, $val='') {
	$secs = safe_column('name', 'txp_section', '1=1');
	return selectInput($name, $secs, $val, true);
}

// ------------------------
// List of custom fields
function smd_ebook_fld_list($name, $val='') {
	$cfs = getCustomFields();
	$cfs['Title'] = gTxt('title');
	$cfs['Excerpt_html'] = gTxt('excerpt');
	$cfs['SMD_FIXED'] = smd_ebook_gTxt('smd_ebook_fixed');
	return selectInput($name, $cfs, $val, true);
}

// ------------------------
// List of custom fields with a few extras
function smd_ebook_fld_list_plus($name, $val='') {
	$cfs = getCustomFields();
	$cfs['Title'] = gTxt('title');
	$cfs['Excerpt_html'] = gTxt('excerpt');
	$cfs['Category1'] = gTxt('category1');
	$cfs['Category2'] = gTxt('category2');
	$cfs['Section'] = gTxt('section');
	$cfs['SMD_FIXED'] = smd_ebook_gTxt('smd_ebook_fixed');
	return selectInput($name, $cfs, $val, true);
}

// ------------------------
// List of custom fields
function smd_ebook_fld_list_author($name, $val='') {
	$cfs = getCustomFields();
	$cfs['Title'] = gTxt('title');
	$cfs['Excerpt_html'] = gTxt('excerpt');
	$cfs['AuthorID'] = gTxt('author');
	$cfs['SMD_FIXED'] = smd_ebook_gTxt('smd_ebook_fixed');
	return selectInput($name, $cfs, $val, true);
}

// ------------------------
// Multi-select list of privilege levels
function smd_ebook_priv_list($name, $val='') {
	$grps = get_groups();
	unset($grps['0']); // Remove 'none'
	unset($grps['1']); // Remove publishers -- they get access to everything already

	$sels = do_list($val);

	$ulist = array();
	$ulist[] = '<select name="'.$name.'[]" id="'.$name.'" class="list multiple" multiple="multiple" size="6">';
	foreach ($grps as $lvl => $grp) {
		$selected = in_array($lvl, $sels) ? ' selected="selected"' : '';
		$ulist[] = '<option value="'.$lvl.'"'.$selected.'>' . htmlspecialchars($grp) . '</option>';
	}
	$ulist[] = '</select>';

	return join(n, $ulist);
}

// ------------------------
// Mini diagnostics to see if the kindlegen program can be run on this host.
function smd_ebook_test() {
	global $smd_ebook_event, $smd_ebook_prefs;

	require_privs('plugin_prefs.'.$smd_ebook_event);

	$out = '';
	$kgen = get_pref('smd_ebook_kindlegen_path', $smd_ebook_prefs['smd_ebook_kindlegen_path']['default']);
	exec($kgen, $output, $retval);

	if ($retval != 0) {
		switch ($retval) {
			case 126:
				$out = smd_ebook_gTxt('smd_ebook_permissions_issue');
			break;
			case 127:
				$out = smd_ebook_gTxt('smd_ebook_not_found');
			break;
			default:
				$out = smd_ebook_gTxt('smd_ebook_error_code', array('{code}' => $retval));
			break;
		}
		$out = print_r($output, true);
	} else {
		$out = smd_ebook_gTxt('smd_ebook_ok');
	}
	$_POST['smd_ebook_test_output'] = $out;
	$msg = smd_ebook_gTxt('smd_ebook_test_complete');
	smd_ebook_prefs($msg);
}

// ------------------------
// Handle the prefs panel
function smd_ebook_prefs($msg='') {
	global $smd_ebook_event, $smd_ebook_prefs, $smd_ebook_styles, $step;

	require_privs('plugin_prefs.'.$smd_ebook_event);

	if (ps('smd_ebook_pref_save')) {
		foreach ($smd_ebook_prefs as $idx => $prefobj) {
			$val = ps($idx);
			$val = (is_array($val)) ? join(', ', $val) : $val;
			set_pref($idx, doSlash($val), 'smd_ebook', $prefobj['type'], $prefobj['html'], $prefobj['position']);
		}

		$msg = gTxt('preferences_saved');
	}

	pagetop(smd_ebook_gTxt('smd_ebook_tab_name'), $msg);
	extract(smd_ebook_buttons('prf'));

	$btnbar = (has_privs('plugin_prefs.'.$smd_ebook_event))? '<span class="smd_ebook_buttons">'.$btnMgr.$btnPrf.$btnCln.'</span>' : '';

	// Inject styles
	echo '<style type="text/css">' . $smd_ebook_styles['cpanel'] . '</style>';

	echo <<<EOJS
<script type="text/javascript">
jQuery(function() {
	jQuery("select[name='smd_ebook_fld_title'], select[name='smd_ebook_fld_chaptitle'], select[name='smd_ebook_fld_author'], select[name='smd_ebook_fld_description'], select[name='smd_ebook_fld_authornote'], select[name='smd_ebook_fld_subject'], select[name='smd_ebook_fld_publisher'], select[name='smd_ebook_fld_srp']").change(function() {
		var xtra = jQuery(this).attr('name') + '_fixed';
		if (jQuery('option:selected', this).val() == 'SMD_FIXED') {
			jQuery("input[name='"+xtra+"']").parent().parent().show('normal');
		} else {
			jQuery("input[name='"+xtra+"']").parent().parent().hide('fast');
		}
	}).change();
});
</script>
EOJS;
	echo '<div id="'.$smd_ebook_event.'_control" class="txp-control-panel">' . $btnbar . '</div>';

	$out = array();
	$out[] = n.'<div class="txp-list">';
	$out[] = '<form name="smd_ebook_prefs" id="smd_ebook_prefs" action="index.php" method="post">';
	$out[] = eInput($smd_ebook_event).sInput('smd_ebook_prefs');
	$out[] = startTable('list');
	$last_grp = '';
	foreach ($smd_ebook_prefs as $idx => $prefobj) {
		if ($last_grp != $prefobj['group']) {
			$out[] = tr(tdcs(strong(smd_ebook_gTxt($prefobj['group'])), 2));
		}
		$last_grp = $prefobj['group'];
		$subout = array();
		$subout[] = tda('<label for="'.$idx.'">'.smd_ebook_gTxt($idx).'</label>', ' class="noline" style="text-align: right; vertical-align: middle;"');
		$val = get_pref($idx, $prefobj['default'], 1);
		$vis = (isset($prefobj['visible']) && !$prefobj['visible']) ? 'smd_hidden' : '';
		switch ($prefobj['html']) {
			case 'text_input':
				$subout[] = tda(fInput('text', $idx, $val, '', '', '', '', '', $idx),' class="noline"');
			break;
			case 'yesnoradio':
				$subout[] = tda(yesnoRadio($idx, $val),' class="noline"');
			break;
			case 'radioset':
				$subout[] = tda(radioSet($prefobj['content'], $idx, $val),' class="noline"');
			break;
			case 'checkboxset':
				$vals = do_list($val);
				$lclout = array();
				foreach ($prefobj['content'] as $cb => $val) {
					$checked = in_array($cb, $vals);
					$lclout[] = checkbox($idx.'[]', $cb, $checked). smd_ebook_gTxt($val);
				}
				$subout[] = tda(join(n, $lclout),' class="noline"');
			break;
			case 'selectlist':
				$subout[] = tda(selectInput($idx, $prefobj['content'][0], $val, $prefobj['content'][1]),' class="noline"');
			break;
			default:
				if ( strpos($prefobj['html'], 'smd_ebook_') !== false && is_callable($prefobj['html']) ) {
					$subout[] = tda($prefobj['html']($idx, $val),' class="noline"');
				}
			break;
		}
		$out[] = tr(join(n ,$subout), ' class="'.$vis.'"');
	}
	$out[] = tr(tda('&nbsp;', ' class="noline"') . tda($btnTst, ' class="noline"'));

	if ($step == 'smd_ebook_test') {
		$out[] = tr(tda('&nbsp;', ' class="noline"') . tda(text_area('smd_ebook_test_results', 150, 200, ps('smd_ebook_test_output')), ' class="noline"'));
	}

	$out[] = tr(tda('&nbsp;', ' class="noline"') . tda(fInput('submit', 'smd_ebook_pref_save', gTxt('save'), 'publish'), ' class="noline"'));
	$out[] = endTable().tInput();
	$out[] = '</form></div>';

	echo join(n, $out);
}

// ------------------------
// Delete plugin prefs
function smd_ebook_prefs_remove($showpane=1) {
	$message = '';

	safe_delete('txp_prefs', "name like 'smd_ebook_%'");

	if ($showpane) {
		$message = smd_ebook_gTxt('smd_ebook_prefs_deleted');
		smd_ebook($message);
	}
}

// ------------------------
// Set up the global prefs for the plugin
function smd_ebook_get_prefs() {
	global $smd_ebook_prefs, $prefs;

	$smd_ebook_prefs = array(
		'smd_ebook_uid' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 20,
			'default'  => '',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_page_break' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 30,
			'default'  => '~~~~~',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_toc_wraptag' => array(
			'html'     => 'radioset',
			'type'     => PREF_HIDDEN,
			'position' => 40,
			'content'  => array('ul' => smd_ebook_gTxt('smd_ebook_lbl_ul'), 'ol' => smd_ebook_gTxt('smd_ebook_lbl_ol')),
			'default'  => 'ul',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_toc_class' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 60,
			'default'  => 'smd_ebook_toc',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_section' => array(
			'html'     => 'smd_ebook_section_list',
			'type'     => PREF_HIDDEN,
			'position' => 70,
			'default'  => '',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_stylesheet' => array(
			'html'     => 'smd_ebook_style_list',
			'type'     => PREF_HIDDEN,
			'position' => 80,
			'default'  => '',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_textile' => array(
			'html'     => 'checkboxset',
			'type'     => PREF_HIDDEN,
			'position' => 90,
			'content'  => array('description' => 'smd_ebook_lbl_description', 'authornote' => 'smd_ebook_lbl_authornote'),
			'default'  => '',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_encoding' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 100,
			'default'  => 'utf-8',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_kindlegen_path' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 200,
			'default'  => $prefs['path_to_site'].DS.'kindle'.DS.'kindlegen',
			'group'    => 'smd_ebook_settings',
		),
		'smd_ebook_auto_toc' => array(
			'html'     => 'yesnoradio',
			'type'     => PREF_HIDDEN,
			'position' => 10,
			'default'  => '1',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_title' => array(
			'html'     => 'smd_ebook_fld_list',
			'type'     => PREF_HIDDEN,
			'position' => 20,
			'default'  => 'Title',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_title_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 25,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_chaptitle' => array(
			'html'     => 'smd_ebook_fld_list',
			'type'     => PREF_HIDDEN,
			'position' => 30,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_chaptitle_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 35,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_author' => array(
			'html'     => 'smd_ebook_fld_list_author',
			'type'     => PREF_HIDDEN,
			'position' => 40,
			'default'  => 'AuthorID',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_author_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 45,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_description' => array(
			'html'     => 'smd_ebook_fld_list',
			'type'     => PREF_HIDDEN,
			'position' => 50,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_description_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 55,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_authornote' => array(
			'html'     => 'smd_ebook_fld_list',
			'type'     => PREF_HIDDEN,
			'position' => 60,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_authornote_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 65,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_subject' => array(
			'html'     => 'smd_ebook_fld_list_plus',
			'type'     => PREF_HIDDEN,
			'position' => 70,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_subject_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 75,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_publisher' => array(
			'html'     => 'smd_ebook_fld_list_plus',
			'type'     => PREF_HIDDEN,
			'position' => 80,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_publisher_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 85,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_fld_srp' => array(
			'html'     => 'smd_ebook_fld_list',
			'type'     => PREF_HIDDEN,
			'position' => 90,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_fld_srp_fixed' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 95,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
			'visible'  => false,
		),
		'smd_ebook_currency' => array(
			'html'     => 'text_input',
			'type'     => PREF_HIDDEN,
			'position' => 100,
			'default'  => 'EUR',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_heading_level' => array(
			'html'     => 'smd_ebook_number',
			'type'     => PREF_HIDDEN,
			'position' => 110,
			'default'  => '2',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_file_cat' => array(
			'html'     => 'smd_ebook_file_cat_list',
			'type'     => PREF_HIDDEN,
			'position' => 120,
			'default'  => '',
			'group'    => 'smd_ebook_pubset',
		),
		'smd_ebook_privs' => array(
			'html'     => 'smd_ebook_priv_list',
			'type'     => PREF_HIDDEN,
			'position' => 10,
			'default'  => '',
			'group'    => 'smd_ebook_usrset',
		),
		'smd_ebook_opf_edit' => array(
			'html'     => 'smd_ebook_priv_list',
			'type'     => PREF_HIDDEN,
			'position' => 20,
			'default'  => '',
			'group'    => 'smd_ebook_usrset',
		),
	);
}

// ------------------------
function smd_ebook_gTxt($what, $atts = array()) {
	$lang = array(
		'en-gb' => array(
			'smd_ebook_auto_toc'              => 'Automatically create ToC anchors on headings',
			'smd_ebook_currency'              => 'Default three-letter currency code',
			'smd_ebook_deleted'               => 'Temporary files deleted',
			'smd_ebook_encoding'              => 'Character set of document',
			'smd_ebook_error_code'            => 'Status code {code} returned. Ensure the program was uploaded as Binary',
			'smd_ebook_file_cat'              => 'Store files in category',
			'smd_ebook_filed'                 => 'E-book filed as ID {id}',
			'smd_ebook_fixed'                 => 'Static text',
			'smd_ebook_fld_author'            => 'Get author from field',
			'smd_ebook_fld_author_fixed'      => '&lfloor;_ Text',
			'smd_ebook_fld_authornote'        => 'Get author notes from field',
			'smd_ebook_fld_authornote_fixed'  => '&lfloor;_ Text',
			'smd_ebook_fld_chaptitle'         => 'Get chapter titles from field',
			'smd_ebook_fld_chaptitle_fixed'   => '&lfloor;_ Text',
			'smd_ebook_fld_description'       => 'Get description from field',
			'smd_ebook_fld_description_fixed' => '&lfloor;_ Text',
			'smd_ebook_fld_publisher'         => 'Get publisher from field',
			'smd_ebook_fld_publisher_fixed'   => '&lfloor;_ Text',
			'smd_ebook_fld_srp'               => 'Get SRP (price) from field',
			'smd_ebook_fld_srp_fixed'         => '&lfloor;_ Text',
			'smd_ebook_fld_subject'           => 'Get subject (genre) from field',
			'smd_ebook_fld_subject_fixed'     => '&lfloor;_ Text',
			'smd_ebook_fld_title'             => 'Get book title from field',
			'smd_ebook_fld_title_fixed'       => '&lfloor;_ Text',
			'smd_ebook_from'                  => 'From',
			'smd_ebook_heading_level'         => 'Chapter heading level',
			'smd_ebook_generate_failed'       => 'E-book generation failed (exit status {code})',
			'smd_ebook_generate_ok'           => 'E-book generation successful',
			'smd_ebook_kindlegen_path'        => 'Path to kindlegen executable',
			'smd_ebook_lbl_articles'          => 'Choose book article(s)',
			'smd_ebook_lbl_author'            => 'Author',
			'smd_ebook_lbl_authornote'        => 'Author notes',
			'smd_ebook_lbl_chaptitle'         => 'Chapter titles',
			'smd_ebook_lbl_cln'               => 'Tidy up',
			'smd_ebook_lbl_create'            => 'Create',
			'smd_ebook_lbl_description'       => 'Book description',
			'smd_ebook_lbl_download'          => 'Download',
			'smd_ebook_lbl_files'             => 'File manager',
			'smd_ebook_lbl_generate'          => '(Re)generate',
			'smd_ebook_lbl_mgr'               => 'E-book',
			'smd_ebook_lbl_ol'                => 'Numeric list',
			'smd_ebook_lbl_prf'               => 'Settings',
			'smd_ebook_lbl_publisher'         => 'Publisher',
			'smd_ebook_lbl_report'            => 'Build report',
			'smd_ebook_lbl_srp'               => 'Price|Currency',
			'smd_ebook_lbl_start'             => 'Welcome',
			'smd_ebook_lbl_subject'           => 'Subject (genre)',
			'smd_ebook_lbl_title'             => 'Book title',
			'smd_ebook_lbl_to_files'          => 'Store file',
			'smd_ebook_lbl_tst'               => 'Test kindlegen program',
			'smd_ebook_lbl_ul'                => 'Standard list',
			'smd_ebook_lbl_view'              => '[Preview]',
			'smd_ebook_malformed'             => 'Could not process HTML from {file}. Malformed?',
			'smd_ebook_no_files'              => 'No e-book files found',
			'smd_ebook_not_filed'             => 'E-book NOT filed',
			'smd_ebook_not_found'             => 'File not found. Check path?',
			'smd_ebook_ok'                    => 'Everything looks OK',
			'smd_ebook_opf_edit'              => 'Groups that can edit .opf',
			'smd_ebook_page_break'            => 'Page break character sequence',
			'smd_ebook_permissions_issue'     => 'Permissions problem. Is the file executable?',
			'smd_ebook_prefs_deleted'         => 'Settings deleted',
			'smd_ebook_preview_prefix'        => 'Preview of',
			'smd_ebook_privs'                 => 'Groups that can publish',
			'smd_ebook_pubset'                => 'Publishing',
			'smd_ebook_stylesheet'            => 'Stylesheet to include with the book',
			'smd_ebook_section'               => 'List articles from section',
			'smd_ebook_settings'              => 'Plugin configuration',
			'smd_ebook_tab_name'              => 'E-books',
			'smd_ebook_test_complete'         => 'Test complete',
			'smd_ebook_textile'               => 'Apply Textile to',
			'smd_ebook_tidy'                  => 'Tidy up temporary e-book files',
			'smd_ebook_toc'                   => 'Table of Contents',
			'smd_ebook_toc_class'             => 'ToC CSS class name',
			'smd_ebook_toc_wraptag'           => 'Render ToC as',
			'smd_ebook_uid'                   => 'Unique ID',
			'smd_ebook_updated'               => 'E-book info for file ID {id} updated',
			'smd_ebook_usrset'                => 'Rights',
		),
	);

	$thislang = get_pref('language', 'en-gb');
	$exists = (isset($lang[$thislang][$what]));
	$thislang = $exists ? $thislang : 'en-gb';
	return ($exists) ? strtr($lang[$thislang][$what], $atts) : $what;
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. smd_ebook

There are a few ways to create E-books suitable for e-readers like Kindle / Kobo / Nook / etc:

* "Install Calibre":http://calibre-ebook.com/ and use the software to guide you towards creating your book
* Install a plugin for Adobe InDesign and let it help you create the book from your DTP files
* Download the "command-line Kindlegen":http://www.amazon.com/gp/feature.html?ie=UTF8&docId=1000234621 tool, create all the input files manually and hope
* Upload the Kindlegen program to your Textpattern web host and use this plugin to convert one or more articles into an E-book

The last one is of course the focus of this plugin! Features:

* Choose articles to be converted  --  order of articles in final book is alphabetical by URL title
* Standard Textile formatting governs the (multi-level) table of contents and document entry points (plugin will automatically create ToC entry points if you choose)
* Set cover art as article image
* Enter Description, Publisher, Genre, Author notes and Price in plugin or use article fields (useful for publisher sites to allow authors to publish their own content)
* Tweak files if necessary before final E-book generation
* Download files for distribution via third party sites, or send them to Txp's Files tab ready for direct download by others

h2. Installation / uninstallation

Requires Textpattern 4.5.0+ and PHP5+

Download the plugin from either "textpattern.org":http://textpattern.org/plugins/NNNN/smd_ebook, or the "software page":http://stefdawson.com/sw, paste the code into the Txp _Admin->Plugins_ pane, install and enable the plugin. Visit the "forum thread":http://forum.textpattern.com/viewtopic.php?id=YYYYY for more info or to report on the success or otherwise of the plugin.

To remove the plugin, simply delete it from the _Admin->Plugins_ pane.

h2. Setting up

# Obtain the "kindlegen program":http://www.amazon.com/gp/feature.html?ie=UTF8&docId=1000234621 that is compatible with your web host  --  most likely the Linux version. While you're there you might as well grab the (huge!) Kindle Previewer too as it's very handy to test files made with this plugin.
# Upload kindlegen via your FTP program *as binary* to a location of your choosing on your web host; preferably outside document root so it can't be run by other people. Double check it is uploaded as binary  --  some FTP software (e.g. FileZilla) is set to auto-negotiate the file type and often gets it wrong. If the plugin doesn't work, this is the most likely source of failure.
# Visit the _Content->E-books_ tab with a Publisher level account and hit the _Settings_ button. Configure the _Path to kindlegen executable_ to reflect the location of your uploaded kindlegen file. Set up any other relevant settings while you are here and save them.
# *After saving the settings* you can click the _Test kindlegen program_ link to check that the program is uploaded correctly and the plugin can find it. If everything's OK, you will be told so in a text box that appears below the link. If the kindlegen file produces errors or cannot be found, the error messages will be shown instead.

h2. Writing content suitable for E-readers

While the technology and tools are improving, there are some guidelines and things to be aware of when creating content in Textpattern that will translate well into a good e-reader experience:

* Use headings to create chapters or logical breaks in your prose. You can create many articles if you wish  --  perhaps one article per chapter  --  and create a single file from them, or create it all in one article.
* Supply cover art. This must be a GIF or JPG image of dimensions 500 (w) x 600 (h) pixels. Assign the ID of the cover image uploaded to Textpattern in the _Article Image_ field of the first chapter.
* Create a stylesheet to lay out your table of contents or alter facets of your book. Formatting is often hit and miss because the e-readers use their own internal styles, but some things can be influenced with a stylesheet. Tinker with it to see what effects you can create.
* "Inline images":#smd_ebook_images cannot flow around text  --  they always appear block style.
* Add author notes to a field in your first article  --  such notes appear after the cover image and before the ToC. Copyright info and acknowledgements are useful here. See the setting _Get author notes from field_.

h3. Formatting for Table of Contents (ToC)

The concept of a ToC maps nicely in Textile / HTML to the @<h1>@  -  @<h6>@ tags, although you do not have to stick to that convention. Any anchor with an HTML ID will be converted into a ToC entry point by the plugin.

There are two primary methods for creating a table of contents:

# Use @h1.@ to @h6.@ Textile tags and set the plugin to automatically create ToC entries from headings
# Manually add @id@ atributes to some/all headings (in Textile: @h2(#some-id). My Great Heading@) or other anchors

Any anchors you miss off will be automatically assigned by the plugin if you have elected to permit this behaviour.

Use hyphens in the ID to create nested menu structures: each hyphen creates one 'level', e.g.:

bc(block). h2(#l1). Level 1 heading
h2(#some_heading). Another level 1 heading
h2(#l1-subbie). A sub-heading beneath the previous heading
h2(#some-name). Another subheading beneath the previous heading


Note that the names don't have to have the same prefix or follow any pattern (though it makes sense to do so for sanity's sake!), nor does the heading level bear any influence. The number of hyphens overrides the heading level to govern the nesting levels. If you don't like this feature, use underscores to separate words in your IDs, or leave the plugin to create ToC entry points for you.

h3(#smd_ebook_pbr). Page breaks

Page breaks normally occur in E-books before chapter headings. To insert page breaks into the document you have three options:

# Add the special tag @~~~~~@ before each chapter heading (you will most likely need a blank line above and below it: the plugin will eat the extra paragraph marks that Textile inserts).
# Modify the heading style to include a class name in your nominated kindle stylesheet that has the rule: @page-break-before: always@.
# Add @style="page-break-before: always"@ directly inline in your heading tags, or wherever you want a page break to occur.

h3(#smd_ebook_images). Inline images

It may seem tempting to use Textpattern's image tags in articles to insert inline images. While this works and looks good in the plugin's Preview window, the images will not be rendered in the final downloadable book. If you study the Report closely you'll see that kindlegen struggles with such images, stating it cannot find them.

The reason for this is that kindlegen expects images and embedded content to be presented as _files_ not _URLs_. Textpattern's image tags (and those in image plugins) all output URLs of the form @http://site.com/images/NN.ext@ by default. In order to use images in the final e-book you need to specify images as @/path/to/site/images/NN.ext@. You can do this fairly easily with the @<txp:images>@ tag and @<txp:image_info />@, like this:

bc(block). <txp:images name="my-pic.jpg">
   <img
     src="/path/to/site/images/<txp:image_info type="id, ext" />"
     alt="<txp:image_info type="alt" />"
     title="<txp:image_info />" />
</txp:images>


For convenience you could set this content up as an smd_macro.

The plugin's [Preview] window will detect the fact your images are using paths and attempt to swap them for URLs on the fly so you can see them in the preview. It may not work on Windows hosts or if you have your images in a separate domain.

h2. Plugin settings

h3. Configuration

<dl>
<dt>Unique ID:</dt>
<dd>An optional unique reference ID for all projects created from the plugin. Useful for publishers to 'tag' all their own books with a code.</dd>
<dt>Page break character sequence</dt>
<dd>Use this string in your article text to denote where a page break should occur. You may "use CSS":#smd_ebook_pbr if you prefer.</dd>
<dd>Default: @~~~~~@ (five tilde characters)</dd>
<dt>Render ToC as</dt>
<dd>Choose between creating the Table of Contents markup as:</dd>
<dd>*Standard* (@<ul>@)</dd>
<dd>*Numeric* (@<ol>@)</dd>
</dl>

Default: Standard

ToC CSS class name

The CSS class to apply to the table of contents.

Default: @smd_ebook_toc@

List articles from section

Limit the articles in the select list to the ones in the nominated section. Otherwise, all sections are considered.

Stylesheet to include with the book

Choose one Textpattern stylesheet that will be inserted into each article in the book to govern formatting.

Apply Textile to

Choose whether to pass the checked content through Textile. If the field you are choosing to represent the content has already been Textiled, it's probably not a good idea to do it a second time.

Character set of document

The character set to use in the final document. Useful values are usually @utf-8@ or @iso-8859-1@.

Default: @utf-8@

Path to kindlegen executable

The full system file path to the kindlegen program that you uploaded to your web host (*as a binary file*!)

It is preferable to make this a non-web-accessible location.

h3. Publishing

<dl>
<dt>Automatically create ToC anchors on headings</dt>
<dd>Set to @Yes@ to automatically create anchors from all heading tags in your article(s). If you have already put some in the dcument, the plugin will only auto-generate ones you have missed.</dd>
<dd>Default: Yes</dd>
<dt>Get book title from field</dt>
<dd>Nominate an article field to hold the book's title. Leave this item empty to force the title to be entered at book compilation time, or choose _Static text_ and enter a title that will be applied to all created books.</dd>
<dd>Default: article's @Title@ field</dd>
<dt>Get chapter titles from field</dt>
<dd>Nominate an article field to hold the chapter titles. Leave this item empty to set chapters manually in the body text, or choose _Static text_ and enter a title that will be applied to every chapter.</dd>
<dd>Any chosen item will be wrapped with HTML heading tags of the level given in the _Chapter heading level_ setting.</dd>
<dt>Get author from field</dt>
<dd>Nominate an article field to hold the author of the work. Leave this item empty to allow the author to be entered at book compilation time, or choose _Static text_ and enter an author that will be applied to all created books.</dd>
<dd>The @Author@ entry in the list will read the Author from the first article in the book.</dd>
<dd>Default: @Author@</dd>
<dt>Get description from field</dt>
<dd>Nominate an article field to hold the book's description. Leave this item empty to allow the description to be entered at book compilation time, or choose _Static text_ and enter a description that will be applied to all created books.</dd>
<dd>This field will be Textiled if you have checked it in the _Apply Textile to_ setting.</dd>
<dt>Get author notes from field</dt>
<dd>Nominate an article field to hold the author notes (acknowledgements, copyright, etc). Leave this item empty to allow the notes to be entered at book compilation time, or choose _Static text_ and enter content that will be applied to all created books.</dd>
<dd>This field will be Textiled if you have checked it in the _Apply Textile to_ setting.</dd>
<dt>Get subject (genre) from field</dt>
<dd>Nominate an article field to determine the subject or genre of the article. Leave this item empty to allow the genre to be entered at book compilation time, or choose _Static text_ and enter content that will be applied to all created books.</dd>
<dt>Get publisher from field</dt>
<dd>Nominate an article field to set the publisher of the article. Leave this item empty to allow the info to be entered at book compilation time, or choose _Static text_ and enter a publisher name that will be applied to all created books.</dd>
<dt>Get SRP (price) from field</dt>
<dd>Nominate an article field to set the download price of the article. Leave this item empty to allow the info to be entered at book compilation time, or choose _Static text_ ten enter a price and optional three-letter currency code (e.g. USD, GBP, EUR), separated by a pipe symbol, that will be applied to all created books. If the currency is not supplied it will be taken from the setting _Default three-letter currency code_.</dd>
<dt>Default three-letter currency code</dt>
<dd>The three-letter "currency code":http://www.xe.com/iso4217.php of the default currency to use for the book's price.</dd>
<dd>Default: @EUR@</dd>
<dt>Chapter heading level</dt>
<dd>If your chapter headings are being read from an article field, they will automatically be wrapped with HTML @<hN>...</hN>@ tags, where @N@ is the value in this setting.</dd>
<dd>Default: 2</dd>
<dt>Store files in category</dt>
<dd>If you elect to store your completed E-books in Textpattern's Files tab, this is the category to which they'll be assigned.</dd>
</dl>

h3. Rights

<dl>
<dt>Groups that can publish</dt>
<dd>Select the user groups that are permitted to publish e-books. Users in these groups will see a _Content->E-books_ tab but will not be permitted to alter the Settings.</dd>
<dt>Groups that can edit .opf</dt>
<dd>The .opf is the master file that governs e-book creation. Publisher account holders can always edit this file but if you have preset some of the content using the _Static text_ publishing options, being able to alter the .opf would allow someone to change the presets. For this reason you can use this setting to govern which user groups you trust to edit the .opf and potentially override the settings.</dd>
</dl>

h2. Creating a book

The creation process takes place in two stages, although the plugin will have a stab at creating everything in one step if it can.

To kick things off, visit the _Content->E-books_ tab. Choose one or more articles from the first select list to create into a book. When choosing multiple files, the order they will appear in the book is the same order as they are in this list. It is goverened by URL title so if you want things to appear in a different order, alter the url-only title of your articles, e.g.:

* chapter01-the-bell-tolls
* chapter02-trousers-on-fire
* chapter03-false-alarm
 ...

You may optionally fill in any of the remaining content fields that are presented. Only Title is mandatory. Some may be prefilled by content from the indicated fields in your selected article(s). Note that such information is *only taken from the first article in which that field has data*. Thus when compiling multiple articles into a single book, it's a good idea to put all such meta data  --  including article image (which is used for the cover artwork)  --  in the first chapter. Once a piece of info is set, it is not altered in subsequent files that make up the same book, even if data is present in the nominated fields.

A note about the Price field: you specify up to two pieces of info in this field, separated by a pipe symbol (@|@). First the price itself and then the three-letter currency code (e.g. GBP, EUR, USD, AUD, etc). If the currency code is not supplied, the setting _Default three-letter currency code_ will be used.

After clicking _Create_, the plugin will collate all the selected articles and meta data and try to produce a complete e-book (.mobi file). The success or otherwise of the process is shown in the _Build report_ box. Scroll through this info to find any errors. You may need to go back to your source documents to fix them. Alternatively you may be able to fix the problems by manually editing the various files that make up the project.

All files are listed on the left hand side of the screen. Although each project is different, the various components that may be present are:

* One .html file for each article you selected.
* An HTML Table of Contents with the filename of the first article in the project plus @_toc.html@.
* An author notes page (filename of first article plus @_notes.html@).
* A .ncx file which is a special (XML) waypoint and navigation aid that allows e-readers to show chapter points in a timeline and permit jumping to various parts of the document (cover, author notes, chapters, etc) from the context menu key on the reader.
* A .opf file (which may not be displayed depending on the rights assigned to your user account) which is the master (XML) record that ties all the other files together and is fed to the kindlegen program to create the final .mobi file.

You can click any file name to open it for editing in the adjacent box, make changes to the markup and _Save_ the alterations. Do this as often as you like and, when completed, hit the _generate_ button to tell the plugin to try to create the .mobi file again. If you wish to preview any of the HTML files (for example, to check your stylesheet is applying appropriate rules) click the @[Preview]@ link after the file's title. Click the 'X' in the top-right corner of the preview window, or hit the ESCape key to dismiss the preview.

If you wish to create an article with a specific language string you can do one of two things:

# Manually alter the .ncx file's @xml:lang@ attribute and the .opf file's @<dc:language>@ markup.
# Change Textpattern's admin side language from the _Admin->Preferences_ tab, then regenerate your book.

Upon successful completion of the process you can choose whether to:

* Click the _Store file_ button to copy the complete E-book file to Textpattern's Files tab. If the file does not exist, it will be created. If it exists, it will be updated with the new details (title and description as you entered them in the input boxes when the book was created, and the category as set in the plugin settings).
* Click the _Download_ button to download a copy of the complete E-book to your computer, whereby it's a good idea to test it in Kindle Previewer or transfer it to your real e-reader and check the navigation and formatting are to your satisfaction.

h2. Tidying up

The plugin uses Textpattern's @tmp@ directory to store its files as it creates them. Since the editorial process may involve editing or tweaking them and so forth, the files are left in situ even after the .mobil file has been created.

It is up to the site admin to keep things tidy and, to this end, there's a helpful extra panel under the e-books tab called 'Tidy up'. Click that button to be shown a list of possible e-book-ish files in the tmp directory. In addition to the files that are editable after creation of an e-book, one other special file  --  a .smd file  --  is something the plugin uses to keep track of which files are in each project. Once a .mobil file has been created, this file is also no longer of any use.

Select the files you want to delete and hit the _Delete_ button. No warning is given: they are deleted immediately.

h2. Author and credits

Written by "Stef Dawson":http://stefdawson.com/contact. For other software by me, or to make a donation, see the "software page":http://stefdawson.com/sw.

While the code to glue all the various parts together is mine, the various websites, blogs and forums I had to trawl to gather the info are many. Thank you to anybody who has posted Kindle / ePub / .mobi / e-reader tricks, tips and guides. Without you I could not have completed this plugin because official documentation on the kindlegen program is surprisingly lacking. Thanks also to Amazon techs for writing the kindlegen program.

h2. Changelog

* 19 Mar 2012 | 0.10 | Beta release

# --- END PLUGIN HELP ---
-->
<?php
}
?>