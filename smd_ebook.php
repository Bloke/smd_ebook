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

$plugin['version'] = '0.3.0';
$plugin['author'] = 'Stef Dawson';
$plugin['author_uri'] = 'https://stefdawson.com/';
$plugin['description'] = 'Create e-books (e.g. ePub / Kindle) from Textpattern content';

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

$plugin['textpack'] = <<<EOT
#@smd_ebook
smd_ebook_auto_toc => Automatically create ToC anchors on headings
smd_ebook_auto_toc_headings => Create visible ToC from these heading numbers
smd_ebook_currency => Default three-letter currency code
smd_ebook_deleted => Temporary files deleted
smd_ebook_encoding => Character set of document
smd_ebook_error_code => Status code {code} returned. Ensure the program was uploaded as Binary
smd_ebook_filed => E-book filed as ID {id}
smd_ebook_file_cat => Store files in category
smd_ebook_fixed => Static text
smd_ebook_fld_author => Get author from field
smd_ebook_fld_authornote => Get author notes from field
smd_ebook_fld_authornote_fixed => &#8600; Text
smd_ebook_fld_author_fixed => &#8600; Text
smd_ebook_fld_chaptitle => Get chapter titles from field
smd_ebook_fld_chaptitle_fixed => &#8600; Text
smd_ebook_fld_description => Get description from field
smd_ebook_fld_description_fixed => &#8600; Text
smd_ebook_fld_guide => Guide tags defined in field
smd_ebook_fld_publisher => Get publisher from field
smd_ebook_fld_publisher_fixed => &#8600; Text
smd_ebook_fld_srp => Get SRP (price) from field
smd_ebook_fld_srp_fixed => &#8600; Text
smd_ebook_fld_subject => Get subject (genre) from field
smd_ebook_fld_subject_fixed => &#8600; Text
smd_ebook_fld_title => Get book title from field
smd_ebook_fld_title_fixed => &#8600; Text
smd_ebook_fld_uid => Get unique ID from field
smd_ebook_fld_uid_fixed => &#8600; Text
smd_ebook_from => From
smd_ebook_generate_failed => E-book generation failed (exit status {code})
smd_ebook_generate_ok => E-book generation successful
smd_ebook_guide => Guide
smd_ebook_heading_level => Chapter heading level
smd_ebook_kindlegen_path => Path to kindlegen executable
smd_ebook_lbl_acknowledgments => Acknowledgments
smd_ebook_lbl_afterword => Afterword
smd_ebook_lbl_appendix => Appendix
smd_ebook_lbl_articles => Choose book article(s)
smd_ebook_lbl_author => Author
smd_ebook_lbl_authornote => Author notes
smd_ebook_lbl_bibliography => Bibliography
smd_ebook_lbl_chaptitle => Chapter titles
smd_ebook_lbl_cln => Tidy up
smd_ebook_lbl_colophon => Colophon
smd_ebook_lbl_conclusion => Conclusion
smd_ebook_lbl_contributors => Contributors
smd_ebook_lbl_copyright_page => Copyright
smd_ebook_lbl_create => Create
smd_ebook_lbl_dedication => Dedication
smd_ebook_lbl_description => Book description
smd_ebook_lbl_download => Download
smd_ebook_lbl_epigraph => Epigraph
smd_ebook_lbl_epilogue => Epilogue
smd_ebook_lbl_errata => Errata
smd_ebook_lbl_files => File manager
smd_ebook_lbl_foreword => Foreword
smd_ebook_lbl_generate => (Re)generate
smd_ebook_lbl_glossary => Glossary
smd_ebook_lbl_img_list => Images
smd_ebook_lbl_imprint => Imprint
smd_ebook_lbl_index => Index
smd_ebook_lbl_introduction => Introduction
smd_ebook_lbl_loi => List of illustrations
smd_ebook_lbl_lot => List of tables
smd_ebook_lbl_mgr => E-book
smd_ebook_lbl_mobi => Kindle (mobi)
smd_ebook_lbl_notes => Notes
smd_ebook_lbl_ol => Numeric list
smd_ebook_lbl_preamble => Preamble
smd_ebook_lbl_preface => Preface
smd_ebook_lbl_prf => Settings
smd_ebook_lbl_prologue => Prologue
smd_ebook_lbl_publisher => Publisher
smd_ebook_lbl_report => Build report
smd_ebook_lbl_srp => Price|Currency
smd_ebook_lbl_subject => Subject (genre)
smd_ebook_lbl_text => Welcome
smd_ebook_lbl_title => Book title
smd_ebook_lbl_titlepage => Title
smd_ebook_lbl_toc => Table of contents
smd_ebook_lbl_to_files => Store file
smd_ebook_lbl_tst => Test kindlegen program
smd_ebook_lbl_uid => Unique ID
smd_ebook_lbl_ul => Standard list
smd_ebook_lbl_view => View
smd_ebook_lbl_view_toc => View ToC
smd_ebook_lbl_zip => ePub
smd_ebook_malformed => Could not process HTML from {file}. Malformed?
smd_ebook_not_filed => E-book NOT filed
smd_ebook_not_found => File not found. Check path?
smd_ebook_no_book_types => No book formats available. For mobi output, check that the kindlegen binary has been uploaded correctly and is executable, and your host has not disabled exec(). For ePub, ensure the smd_crunchers plugin is installed
smd_ebook_no_files => No e-book files found
smd_ebook_ok => Everything looks OK
smd_ebook_opf_edit => Groups that can edit .opf
smd_ebook_page_break => Page break character sequence
smd_ebook_permissions_issue => Permissions problem. Is the file executable?
smd_ebook_prefs_deleted => Settings deleted
smd_ebook_preview_prefix => Preview of
smd_ebook_privs => Groups that can publish
smd_ebook_pubfile => Output filename (optional)
smd_ebook_pubset => Publishing
smd_ebook_section => List articles from section
smd_ebook_settings => Plugin configuration
smd_ebook_stylesheet => Stylesheets to include with the book
smd_ebook_tab_name => E-books
smd_ebook_test_complete => Test complete
smd_ebook_textile => Apply Textile to
smd_ebook_tidy => Tidy up temporary e-book files
smd_ebook_toc_class => ToC CSS class name
smd_ebook_toc_wraptag => Render ToC as
smd_ebook_updated => E-book info for file ID {id} updated
smd_ebook_usrset => Rights
EOT;

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
/**
 * smd_ebook
 *
 * A Textpattern CMS plugin for creating e-books (Kindle, ePub) from Txp content
 *  -> Content can be in one article or across many
 *  -> Article image of one of the articles is used as cover art
 *  -> Automatic TOC generation and page breaks from Textiled markup
 *  -> Support for book description and other meta data
 *
 * @author Stef Dawson
 * @link   http://stefdawson.com/
 */

// TODO:
//  * Stop preview CSS affecting Stage 2 UI layout (mobi)
//  * Allow preview CSS to be loaded (epub)

global $smd_ebook_prefs;
smd_ebook_get_prefs();

if (txpinterface === 'admin') {
    load_plugin('smd_crunchers');

    global $smd_ebook_event;

    $smd_ebook_event = 'smd_ebook';

    $pub_prv = get_pref('smd_ebook_privs', $smd_ebook_prefs['smd_ebook_privs']['default']);
    add_privs($smd_ebook_event, '1'. (($pub_prv) ? ','.$pub_prv: '') );
    add_privs('plugin_prefs.'.$smd_ebook_event, '1');

    register_tab('content', $smd_ebook_event, gTxt('smd_ebook_tab_name'));
    register_callback('smd_ebook_dispatcher', $smd_ebook_event);
    register_callback('smd_ebook_inject_css', 'admin_side', 'head_end');
    register_callback('smd_ebook_dispatcher', 'plugin_prefs.'.$smd_ebook_event);
    register_callback('smd_ebook_welcome', 'plugin_lifecycle.'.$smd_ebook_event);
}

// ********************
// ADMIN SIDE INTERFACE
// ********************
// -------------------------------------------------------------
// CSS definitions: hopefully kind to themers
function smd_ebook_get_style_rules()
{
    $smd_ebook_styles = array(
        'cpanel' => '
.smd_hidden { display:none; }
.smd_active { font-weight:bold; }
.smd_clear { clear:both; }
.smd_error { border:1px solid red; }
.smd_preselected { opacity:0.6; font-style:italic; }
.smd_selected { border-top:solid #444; border-bottom:solid #444; }
.smd_important { color:red; }
.smd_inline { display:inline; }
#smd_ebook_preview { display:none; position:absolute; top:1em; left:2.5em; margin:0 auto; text-align:left; border:2px ridge #999; background:#ececec; max-width:680px; min-width:300px; box-shadow: 8px 8px 15px #b9b9b9; }
#smd_ebook_preview_close { float:right; cursor:pointer; margin-left:1em; }
#smd_ebook_preview_content { padding:1em; }
#smd_ebook_preview_titlebar { padding:5px; border-bottom:1px solid black; font-size:120%; background:#ccc; }
#smd_ebook_form { margin:0 auto; width:80%; }
#smd_ebook_form label, #smd_ebook_create { display:block; }
#smd_ebook_prefs input[type="text"] { width:250px; }
.smd_ebook_manager { position:relative; margin:0 auto; width:80%; }
.smd_ebook_report textarea { width:80%; }
#smd_ebook_editor { display:block; width:70%; }
.smd_ebook_file_group { margin:0 0 0.7em; }
.smd_ebook_files { float:left; width:30%; text-align:left; }
.smd_ebook_files ul { display:none; }
.smd_ebook_pub_options { text-align:right; }
.smd_ebook_entity { float:left; margin:1em; }
#smd_ebook_form label, .smd_ebook_report, .smd_ebook_manager { margin-top:1.5em; }
#smd_ebook_type_opts { margin:0.8em 0.5em; }
#smd_ebook_type_opts label { display:inline; }
.smd_ebook_file { line-height:1.5; }
',
    );

    return $smd_ebook_styles;
}

// -------------------------------------------------------------
function smd_ebook_inject_css($evt, $stp)
{
    global $smd_ebook_event, $event;

    if ($event === $smd_ebook_event) {
        $smd_ebook_styles = smd_ebook_get_style_rules();

        echo '<style type="text/css">' .n. $smd_ebook_styles['cpanel'] .n. '</style>';
    }

    return;
}

// Plugin jump off point
function smd_ebook_dispatcher($evt, $stp)
{
    global $smd_ebook_event;

    $available_steps = array(
        'smd_ebook'               => false,
        'smd_ebook_prefs'         => false,
        'smd_ebook_create'        => true,
        'smd_ebook_generate'      => true,
        'smd_ebook_loadfile'      => true,
        'smd_ebook_savefile'      => true,
        'smd_ebook_viewfile'      => true,
        'smd_ebook_viewimage'     => true,
        'smd_ebook_test'          => true,
        'smd_ebook_tidy'          => false,
        'save_pane_state'         => true,
    );

    if ($stp === 'save_pane_state') {
        smd_ebook_save_pane_state();
    } elseif ($stp and bouncer($stp, $available_steps)) {
        $stp();
    } else {
        $smd_ebook_event();
    }
}

// ------------------------
function smd_ebook_welcome($evt, $stp)
{
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
function smd_ebook($evt = '', $stp = '')
{
    smd_ebook_ui();
}

// ------------------------
// Interface for compiling the book
function smd_ebook_ui($msg = '', $listfile = '', $report = '', $retval = '', $ebook_folder = '')
{
    global $smd_ebook_event, $smd_ebook_prefs;

    pagetop(gTxt('smd_ebook_tab_name'), $msg);
    extract(smd_ebook_buttons('mgr'));

    $btnbar = (has_privs('plugin_prefs.'.$smd_ebook_event))? '<div class="txp-buttons">'.$btnMgr.n.$btnPrf.n.$btnCln.'</div>' : '';

    echo n. '<div id="' . $smd_ebook_event . '_control" class="txp-control-panel">' . $btnbar . '</div>';

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
            'uid' => array(
                'html' => 'text_input',
            ),
        );

        $cfs = getCustomFields();

        foreach ($fields as $field => $data) {
            $data['value'] = get_pref('smd_ebook_fld_'.$field, $smd_ebook_prefs['smd_ebook_fld_'.$field]['default']);
            $data['column'] = is_numeric($data['value']) ? 'custom_'.$data['value'] : $data['value'];
            $data['name'] = (is_numeric($data['value']) && isset($cfs[$data['value']])) ? $cfs[$data['value']] : $data['value'];
            $data['content'] = ($data['value'] === 'SMD_FIXED') ? get_pref('smd_ebook_fld_'.$field.'_fixed', '') : '';
            $data['required'] = isset($data['required']) ? $data['required'] : false;
            $data['hide_empty'] = isset($data['hide_empty']) ? $data['hide_empty'] : false;
            ${'ip_'.$field} = '<div class="smd_ebook_entity">' . ( ($data['content'] || ($data['name'] === '' && $data['hide_empty']))
                ? hInput('smd_ebook_fld_'.$field, txpspecialchars($data['content']))
                : '<label for="smd_ebook_fld_'.$field.'">' . gTxt('smd_ebook_lbl_'.$field) . '</label>'
                    . ( ($data['column'])
                        ? hInput('smd_ebook_fld_'.$field, 'SMD_FLD_'.$data['column'])
                            . '<span class="smd_preselected">' . gTxt('smd_ebook_from').' '.str_replace('SMD_FLD_', '', $data['name']) . '</span>'
                        : ( ($data['html'] === 'textarea')
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

        // Set up the book type options (if any)
        $book_types = smd_ebook_kindlegen_available() ? array('mobi' => gTxt('smd_ebook_lbl_mobi')) : array();
        $book_types = array_merge($book_types, smd_ebook_crush_options());
        $num_btypes = count($book_types);

        if ($num_btypes === 0) {
            $btype = graf(gTxt('smd_ebook_no_book_types'), ' class="error"');
        } elseif ($num_btypes === 1) {
            reset($book_types);
            $btype = hInput('smd_ebook_type', key($book_types));
        } else {
            $btype = '<span id="smd_ebook_type_opts">' . radioSet($book_types, 'smd_ebook_type', get_pref('smd_ebook_type', 'mobi')) . '</span>';
        }

        // Build dropdown list of articles: not using selectInput() because it doesn't support multiples
        $alist = array();
        $alist[] = '<select name="smd_ebook_articles[]" id="smd_ebook_articles" class="list multiple" multiple="multiple" size="12" required="">';

        foreach ($articles as $row) {
            $alist[] = '<option value="smd_ebook_article_'.txpspecialchars($row['ID']).'">' . txpspecialchars($row['Title']) . '</option>';
        }

        $alist[] = '</select>';

        echo n.'<div id="'.$smd_ebook_event.'_container" class="txp-container">';
        echo n.'<form id="smd_ebook_form" action="index.php" method="post">';
        echo n.'<div class="smd_ebook_entity"><label for="smd_ebook_articles">' . gTxt('smd_ebook_lbl_articles') . '</label>';
        echo join(n, $alist);
        echo n.'</div>';
        echo n. $ip_description .n. $ip_authornote .n. $ip_title .n. $ip_chaptitle .n. $ip_author .n. $ip_publisher .n.$ip_subject .n. $ip_srp .n. $ip_uid;
        echo n.'<div class="smd_clear"></div>';
        echo n.'<label for="smd_ebook_pubfile">'.gTxt('smd_ebook_pubfile').'</label>'
            . fInput('text', 'smd_ebook_pubfile', '', '', '', '', '', '', 'smd_ebook_pubfile')
            . br . $btype
            . (($num_btypes > 0) ? fInput('submit', 'smd_ebook_create', gTxt('smd_ebook_lbl_create'), 'publish', '', '', '', '', 'smd_ebook_create') : '');
        echo n.eInput($smd_ebook_event);
        echo n.sInput('smd_ebook_create');
        echo n.tInput();
        echo n.'</form>';
        echo n.'</div>';

    } else {
        // Stage 2: Edit the content and generate the ebook file
        $titlePrefix = gTxt('smd_ebook_preview_prefix');
        $ebType = ps('smd_ebook_type');
        $is_epub = ($ebType === 'zip');
        $ebook_path = get_pref('tempdir') . DS . $ebook_folder . DS;

        $qs = array(
            "event" => $smd_ebook_event,
        );
        $qsVars = "index.php".join_qs($qs);

        echo n. <<<EOJS
<script type="text/javascript">
var smd_ebook_currfile;

jQuery(function() {
    // Load a file into the editor
    jQuery('.smd_ebook_files .smd_ebook_edit').click(function(ev) {
        ev.preventDefault();
        var me = jQuery(this).prevAll('div');
        var name = me.text();

        // Spinner and user feedback
        var form = jQuery(this).closest('form');
        form.addClass('busy');
        s = jQuery(ev.currentTarget);
        s.after('<span class="spinner" />');

        smd_ebook_currfile = name;

        jQuery.post('{$qsVars}',
            {
                step: 'smd_ebook_loadfile',
                folder: '{$ebook_folder}',
                name: name,
                _txp_token: textpattern._txp_token
            },
            function(json) {
                jQuery('.smd_ebook_files .smd_ebook_file').removeClass('smd_selected');
                jQuery('#smd_ebook_editor').removeClass('smd_error');
                me.toggleClass('smd_selected');
                jQuery('#smd_ebook_editor').val(json.data);
                form.removeClass('busy');
                jQuery('span.spinner').remove();
            },
            'json'
        );
    });

    // Save the current file back to the file system
    jQuery('.smd_ebook_filesave').click(function(ev) {
        ev.preventDefault();

        // Spinner and user feedback
        var form = jQuery(this).closest('form');
        form.addClass('busy');
        s = jQuery(ev.currentTarget);
        s.after('<span class="spinner" />')

        var content = jQuery('#smd_ebook_editor').val();

        jQuery.post('{$qsVars}',
            {
                step: 'smd_ebook_savefile',
                folder: '{$ebook_folder}',
                name: smd_ebook_currfile,
                data: content,
                _txp_token: textpattern._txp_token
            },
            function(json) {
                if (json.result === 'ok') {
                    jQuery('.smd_ebook_files .smd_ebook_file').removeClass('smd_selected');
                    jQuery('#smd_ebook_editor').removeClass('smd_error');
                    jQuery('#smd_ebook_editor').val('');
                } else {
                    jQuery('#smd_ebook_editor').addClass('smd_error');
                }
                form.removeClass('busy');
                jQuery('span.spinner').remove();
            },
            'json'
        );
    });

    // Preview an html file in its own popup window
    var smd_ebook_previewing = 0;
    jQuery('.smd_ebook_files .smd_ebook_view').click(function(ev) {
        ev.preventDefault();
        var me = jQuery(this).prevAll('div');
        var name = me.text();

        // Spinner and user feedback
        var form = jQuery(this).closest('form');
        form.addClass('busy');
        s = jQuery(ev.currentTarget);
        s.after('<span class="spinner" />')

        jQuery.post('{$qsVars}',
            {
                step: 'smd_ebook_viewfile',
                folder: '{$ebook_folder}',
                name: name,
                _txp_token: textpattern._txp_token
            },
            function(json) {
                // Grab body text and inject it into the preview container
                jQuery('#smd_ebook_preview_content').empty().append(json.data);
                jQuery('#smd_ebook_preview_title').text('{$titlePrefix} ' + name);
                if ((jQuery(ev.target).hasClass('smd_ebook_view')) && !smd_ebook_previewing) {
                    smd_ebook_prevu();
                }
                form.removeClass('busy');
                jQuery('span.spinner').remove();
            },
            'json'
        );
    });

    // Preview an image file in its own popup window
    jQuery('.smd_ebook_files .smd_ebook_image').click(function(ev) {
        ev.preventDefault();
        var me = jQuery(this);
        var name = me.text();

        // Spinner and user feedback
        var form = jQuery(this).closest('form');
        form.addClass('busy');
        s = jQuery(ev.currentTarget);
        s.after('<span class="spinner" />')

        jQuery.post('{$qsVars}',
            {
                step: 'smd_ebook_viewimage',
                folder: '{$ebook_folder}',
                name: name,
                _txp_token: textpattern._txp_token
            },
            function(json) {
                if (json.src !== null) {
                    source = json.src;
                } else {
                    source = 'data:'+json.mime+';base64,'+json.data;
                }

                jQuery('#smd_ebook_preview_content').empty().append(jQuery('<img />').attr({'src': source, 'alt': 'Preview '+name}));
                jQuery('#smd_ebook_preview_title').text('{$titlePrefix} ' + name);

                if ((jQuery(ev.target).hasClass('smd_ebook_image')) && !smd_ebook_previewing) {
                    smd_ebook_prevu();
                }
                form.removeClass('busy');
                jQuery('span.spinner').remove();
            },
            'json'
        );
    });

    // Toggle individual image list on/off
    jQuery('.smd_ebook_files .smd_ebook_image_toggle').click(function(ev) {
        ev.preventDefault();
        jQuery(this).nextAll('ul').toggle('fast');
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
            if(e.keyCode == 27 && smd_ebook_previewing) {
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
            . hed(gTxt('smd_ebook_lbl_report'), 2)
            . '<textarea id="smd_ebook_report" cols=80 rows="6">'.$report.'</textarea>'
            . '</div>';

        echo n.'<div class="smd_ebook_manager">';

        // 'Generate book' and 'download' buttons
        echo '<div class="smd_ebook_pub_options">';
        echo n.fInput('submit', 'smd_ebook_generate', gTxt('smd_ebook_lbl_generate'), 'publish smd_ebook_pub');

        if ($retval <= 1) {
            $info = explode('.', $listfile);
            $basepart = array_slice($info, 0, count($info)-1);
            $outfile = join('', $basepart);

            echo n.hInput('smd_ebook_pubfile', $outfile);
            echo n.hInput('smd_ebook_folder', $ebook_folder);
            echo n.fInput('submit', 'smd_ebook_to_files', gTxt('smd_ebook_lbl_to_files'), 'publish smd_ebook_pub');;
            echo n.fInput('submit', 'smd_ebook_download', gTxt('smd_ebook_lbl_download'), 'publish smd_ebook_pub');;
        }
        echo n.'</div>';

        echo n.hed(gTxt('smd_ebook_lbl_files'), 2);
        echo n.'<div class="smd_ebook_files">';

        $opf_edit = get_pref('smd_ebook_opf_edit', $smd_ebook_prefs['smd_ebook_opf_edit']['default']);
        $opf_allowed = do_list($opf_edit);
        $opf_allowed[] = '1'; // Publishers can always edit .opf
        $can_opf = !empty($GLOBALS['privs']) && in_array($GLOBALS['privs'], $opf_allowed);

        $files = file($ebook_path . $listfile);
        $files = doArray($files, 'trim');
        $image_list = array();
        $content_file = $content_view = $content_edit = '';
        $is_cover = $has_css = false;

        echo n.'<div class="smd_ebook_file_group">';

        foreach ($files as $file) {
            $info = explode('.', $file);
            $lastpart = count($info)-1;
            $ext = $info[$lastpart];
            $img_types = array('jpg', 'jpeg', 'gif', 'png');

            if ($ext === 'opf') {
                echo n.hInput('smd_ebook_opf_file', $file);
                echo n.hInput('smd_ebook_type', $ebType);
            }

            if ($ext !== 'opf' || ($ext === 'opf' && $can_opf)) {
                if (in_array($ext, $img_types)) {
                    $imglink = '<a href="#" class="smd_ebook_image">' . $file . '</a>';
                    if (strpos($file, 'cover') === 0) {
                        $image_list[] = $imglink;
                        $is_cover = true;
                    } else {
                        $image_list[] = '<li>'.$imglink.'</li>';
                        $is_cover = false;
                    }
                } else {
                    $content_file = '<div class="smd_ebook_file">' . $file . '</div>';
                    $content_edit = '[<a href="#" class="smd_ebook_edit">' . gTxt('edit') . '</a>]';
                }

                if ($ext === 'html') {
                    $content_view = '[<a href="#" class="smd_ebook_view">'.gTxt((( (strpos($file, '_toc.html') !== false) || (strpos($file, '_end.html') !== false)) ? 'smd_ebook_lbl_view_toc' : 'smd_ebook_lbl_view')).'</a>]';
                } else {
                    if ($ext === 'css') {
                        $has_css = true;
                    }
                    $content_view = '';
                }

                if (!in_array($ext, $img_types)) {
                    if ($image_list) {
                        if ($is_cover) {
                            if ($has_css) {
                                // Stupid hack because css appears first in the listfile
                                echo n.'</div>';
                                echo n.'<div class="smd_ebook_file_group">';
                            }
                            echo n. join(n, $image_list); // Even though there's only one image
                        } else {
                            echo n. '[<a href="#" class="smd_ebook_image_toggle">' . gTxt('smd_ebook_lbl_img_list') . '</a>]' . tag(join(n, $image_list), 'ul');
                        }
                    }

                    $image_list = array();
                    echo n.'</div>';
                    echo n.'<div class="smd_ebook_file_group">';
                    echo n . $content_file . n . $content_view . n . $content_edit;
                }
            }
        }

        echo n.'</div>';
        echo n.'</div>';

        echo n.'<div class="smd_ebook_editor">';
        echo n.'<textarea id="smd_ebook_editor" cols="60" rows="25"></textarea>';
        echo n.'<button class="smd_ebook_filesave">'.gTxt('save').'</button>';
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
function smd_ebook_loadfile()
{
    $name = sanitizeForFile(ps('name'));
    $folder = sanitizeForFile(ps('folder'));
    $file = file(get_pref('tempdir') . DS . $folder . DS . $name);
    if ($file) {
        echo json_encode(array('data' => join('', $file)));
    }
    exit; // Don't display page_end
}

// ------------------------
function smd_ebook_savefile()
{
    $name = sanitizeForFile(ps('name'));
    $folder = sanitizeForFile(ps('folder'));
    $content = ps('data');
    $fp = fopen(get_pref('tempdir') . DS . $folder . DS . $name, "wb");
    fwrite($fp, trim($content));
    fclose($fp);

    if ($fp) {
        echo json_encode(array('result' => 'ok'));
    } else {
        echo json_encode(array('result' => 'fail'));
    }

    exit; // Don't display page_end
}

// ------------------------
// Extract a subset of the HTML file for display
function smd_ebook_viewfile()
{
    global $path_to_site;

    $name = sanitizeForFile(ps('name'));
    $folder = sanitizeForFile(ps('folder'));
    $file = file_get_contents(get_pref('tempdir') . DS . $folder . DS . $name);

    if ($file) {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // swallow Invalid Entity warnings since .end is XHTML-as-serialization-of-HTML5
        $doc->loadHTML($file);
        $domxpath = new DOMXpath($doc);
        $newDoc = new DOMDocument('1.0', 'UTF-8');

        $nodeStyle = $domxpath->query('//style');
        $nodeList = $domxpath->query('//body');

        // Create a new document and import the document subsets
        if ($nodeStyle->item(0)) {
            $newDoc->appendChild($newDoc->importNode($nodeStyle->item(0), true));
        }
        if ($nodeList->item(0)) {
            $newDoc->appendChild($newDoc->importNode($nodeList->item(0), true));
        }
        $out = $newDoc->saveHTML();

        // Translate any images with absolute paths into URLs
        echo json_encode(array('data' => str_replace($path_to_site.DS, ihu, $out)));
    }

    exit; // Don't display page_end
}

// ------------------------
// Return a base64 representation of an image
// TODO: try getting the image directly via ihu instead of encoding it: faster
function smd_ebook_viewimage()
{
    global $img_dir;

    $name = sanitizeForFile(ps('name'));
    $folder = sanitizeForFile(ps('folder'));
    $fileurl = ihu . $img_dir . DS . $name;

    // Check if remote image file exists
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fileurl);
    curl_setopt($ch, CURLOPT_NOBODY, 1); // Don't download content
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if(curl_exec($ch) !== false) {
        echo json_encode(array('src' => $fileurl));
    } else {
        $filename = get_pref('tempdir') . DS . $folder . DS . $name;

        if (is_readable($filename)) {
            $fh = fopen($filename, 'rb');
            $content = fread($fh, filesize($filename));
            fclose($fh);

            if ($content) {
                $bits = pathinfo($filename);
                $mime_type = (($bits['extension'] === 'jpg' || $bits['extension'] === 'jpeg') ? 'image/jpeg' : (($bits['extension'] === 'gif') ? 'image/gif' : (($bits['extension'] === 'png') ? 'image/png' : '')));

                echo json_encode(array('mime' => $mime_type, 'src' => null, 'data' => base64_encode($content)));
            }
        }
    }

    exit; // Don't display page_end
}

// ------------------------
function smd_ebook_crush_options()
{
    global $plugins;

    $smd_crushers = array();

    if (is_array($plugins) && in_array('smd_crunchers', $plugins)) {
        $crunchers = smd_crunch_capabilities('compress');

        // Although only zip (a.k.a. ePub) is required at present, leave the door
        // open for others in future
        foreach (array('zip') as $cm) {
            if (in_array($cm, $crunchers)) {
                $smd_crushers[$cm] = gTxt('smd_ebook_lbl_' . $cm);
            }
        }
    }

    return $smd_crushers;
}

// ------------------------
function smd_ebook_templates()
{
    // .opf file template
    $template['opf'] = <<<EOOPF
<?xml version="1.0" encoding="{smd_ebook_encoding}"?>
<package xmlns="http://www.idpf.org/2007/opf" version="3.0"{smd_ebook_uid_ref}>
    <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
        {smd_ebook_md_uid}
        {smd_ebook_md_title}
        {smd_ebook_md_lang}
        {smd_ebook_md_creator}
        {smd_ebook_md_date}
        {smd_ebook_md_modified}
        {smd_ebook_md_description}
        {smd_ebook_md_subject}
        {smd_ebook_md_publisher}
        {smd_ebook_md_cover}
        {smd_ebook_md_x}
    </metadata>

    <manifest>
        {smd_ebook_manifest_ncx}
        {smd_ebook_manifest_end}
        {smd_ebook_manifest_cover}
        {smd_ebook_manifest_authornote}
        {smd_ebook_manifest_toc}
        {smd_ebook_manifest_items}
    </manifest>

    <spine {smd_ebook_spine_ncx_ref}>
        {smd_ebook_spine_cover}
        {smd_ebook_spine_authornote}
        {smd_ebook_spine_toc}
        {smd_ebook_spine_items}
    </spine>

    <guide>
        {smd_ebook_guide_cover}
        {smd_ebook_guide_extras}
    </guide>
</package>
EOOPF;

    // non-standard metadata
    $template['xmd'] = <<<EOXMD
<x-metadata>
    <output encoding="{smd_ebook_encoding}" content-type="text/x-oeb1-document"></output>
    {smd_ebook_md_srp}
</x-metadata>
EOXMD;

    // .ncx file template
    $template['ncx'] = <<<EONCX
<?xml version="1.0" encoding="{smd_ebook_encoding}"?>
{smd_ebook_ncx_doctype}
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

    // ePub3 .end file (essentially an HTML ToC. May be derived from .ncx via XSLT)
    $template['end'] = <<<EOEND
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:epub="http://www.idpf.org/2007/ops">
  <head>
    <title>{smd_ebook_toc}</title>
  </head>
  <body>
    <section epub:type="frontmatter toc">
      <header>
        <h1>{smd_ebook_toc}</h1>
      </header>
      <nav epub:type="toc" id="toc">
            {smd_ebook_toc_list}
      </nav>
    </section>
  </body>
</html>
EOEND;

    // ePub3 information jump-off file
    $template['inf'] = <<<EOXML
<?xml version="1.0"?>
<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
    <rootfiles>
        <rootfile full-path="OEBPS/{smd_ebook_opf_file}" media-type="application/oebps-package+xml"/>
    </rootfiles>
</container>
EOXML;

    // navPoint template (a portion of the .ncx file)
    $template['nav'] = <<<EONAV
        <navPoint class="titlepage" id="{smd_ebook_nav_hash}" playOrder="{smd_ebook_nav_idx}">
            <navLabel><text>{smd_ebook_nav_label}</text></navLabel>
            <content src="{smd_ebook_file_name}#{smd_ebook_nav_hash}" />
        </navPoint>
EONAV;

    // TOC template
    $template['toc'] = <<<EOTOC
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{smd_ebook_toc}</title>
    {smd_ebook_stylesheet}
</head>
<body>
    <h1>{smd_ebook_toc}</h1>
    {smd_ebook_toc_list}
</body>
</html>
EOTOC;

    // Landmark template
    $template['lmk'] = <<<EOLAND
<?xml version="1.0" encoding="{smd_ebook_encoding}"?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="{smd_ebook_lang}" xml:lang="{smd_ebook_lang}">
<head>
    <title>{smd_ebook_guide}</title>
    <meta charset="{smd_ebook_encoding}" />
</head>
<body>
    <h1>{smd_ebook_guide}</h1>
    <nav epub:type="landmarks">
    <ol>
        {smd_ebook_landmarks}
    </ol>
    </nav>
</body>
</html>
EOLAND;

    // HTML template
    $template['doc'] = <<<EODOC
{smd_ebook_doctype}
<html{smd_ebook_namespace}>
<head>
    {smd_ebook_charset}
    <title>{smd_ebook_title}</title>
    {smd_ebook_stylesheet}
</head>
<body>
    {smd_ebook_chaptitle}
    {smd_ebook_contents}
</body>
</html>
EODOC;

    // Image template
    $template['img'] = <<<EODOC
<item id="{smd_ebook_image_id}" media-type="{smd_ebook_image_type}" href="{smd_ebook_image_link}" />
EODOC;

    $template['cov'] = <<<EODOC
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Cover</title>
    <style type="text/css">
        img { max-width:100%; }
        body { oeb-column-number:1; }
    </style>
</head>
<body>
    <div id="cover-image">
        <img src="{smd_ebook_image_link}" alt="{smd_ebook_image_alt}" />
    </div>
</body>
</html>
EODOC;

    // Transform .ncx file to .end (ePub3 format ToC). Since an .ncx is made anyway for .mobi
    // format files, and the .ncx is needed for b/c with ePub2, it makes sense to just transform
    // one to the other for ePub3, rather than make brand new templates.
    // Only of use if XSLTProcessor is compiled into PHP, though.
    // Courtesy Liza Daly http://www.ibm.com/developerworks/xml/library/x-richlayoutepub/index.html?ca=drs-
    $template['ncx2end'] = <<<EOXSL
<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
        exclude-result-prefixes="ncx xsl"
        xmlns="http://www.w3.org/1999/xhtml"
        xmlns:ncx="http://www.daisy.org/z3986/2005/ncx/"
        xmlns:epub="http://www.idpf.org/2007/ops"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="ncx:ncx">
        <html>
            <head><title><xsl:apply-templates select="/ncx:ncx/ncx:docTitle/ncx:text"/></title></head>
            <body>
                <xsl:apply-templates />
            </body>
        </html>
    </xsl:template>

    <xsl:template match="ncx:navMap">
        <nav id="toc" epub:type="toc">
            <xsl:copy-of select="@class"/>
            <xsl:choose>
                <xsl:when test="ncx:navLabel">
                    <xsl:apply-templates select="ncx:navLabel" mode="heading"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:if test="self::ncx:navMap">
                        <h1>Table of Contents</h1>
                    </xsl:if>
                </xsl:otherwise>
            </xsl:choose>
            <ol>
                <xsl:apply-templates select="ncx:navPoint|ncx:navLabel"/>
            </ol>
        </nav>
    </xsl:template>

    <xsl:template match="ncx:navPoint">
        <xsl:text>&#10;</xsl:text>
        <li>
            <xsl:copy-of select="@id|@class"/>

            <!-- every navPoint and pageTarget has to have a navLabel and content -->
            <a href="{ncx:content[1]/@src}">
                <xsl:apply-templates select="ncx:navLabel"/>
            </a>

            <!-- Only some navPoints have more navPoints inside them for deep NCXes. pageTargets cannot nest. -->
            <xsl:if test="ncx:navPoint">
                <ol>
                    <xsl:apply-templates select="ncx:navPoint"/>
                </ol>
            </xsl:if>
        </li>
    </xsl:template>

    <xsl:template match="ncx:navLabel|ncx:text">
        <xsl:apply-templates/>
    </xsl:template>

    <!-- Ignore these elements -->
    <xsl:template match="ncx:head|
         ncx:docAuthor|
         ncx:docTitle|
         ncx:pageList/ncx:navLabel"/>
    <xsl:template match="ncx:head/text()|
         ncx:docAuthor/text()|
         ncx:docTitle/text()|
         ncx:navLabel/text()"/>

    <!-- Default rule to catch omissions -->
    <xsl:template match="*">
        <xsl:message terminate="yes">ERROR: <xsl:value-of select="name(.)"/> not matched!
        </xsl:message>
    </xsl:template>

    <xsl:template name="html-head">
        <head>

        </head>
    </xsl:template>
</xsl:stylesheet>
EOXSL;
    return $template;
}

// ------------------------
// Stage 1: create the files necessary for generation of the book.
// The actual generation via kindlegen / zip is a separate step.
function smd_ebook_create()
{
    global $smd_ebook_prefs, $img_dir;

    include_once txpath.'/publish.php'; // For parse() etc.

    $textile = new \Textpattern\Textile\Parser();

    $template = smd_ebook_templates();
    $msg = '';
    $report = $toc = $ncx = $reps = $sheets = $lfout = array();

    // Generate a temporary folder name to store all files in.
    // It's based on the passed params so if you're regenerating the same book
    // and don't alter the POST payload, you'll overwrite stuff in the previous
    // folder for this book. Just keeps the clutter down..
    $ebook_folder = 'smd_ebook_' . substr(md5(serialize($_POST)), 0, 12);
    $ebook_path = get_pref('tempdir') . DS . $ebook_folder . DS;

    if (!is_readable($ebook_path)) {
        mkdir($ebook_path);
    }

    // Store the current type for next time this user creates a book
    $bType = ps('smd_ebook_type');
    set_pref('smd_ebook_type', doSlash($bType), 'smd_ebook', PREF_HIDDEN, 'text_input', 0, PREF_PRIVATE);
    $is_mobi = ($bType === 'mobi');
    $is_epub = ($bType === 'zip');
    $outfile = trim(ps('smd_ebook_pubfile')); // If used, it's without extension

    // Get Textile and encoding options
    $encoding = get_pref('smd_ebook_encoding', $smd_ebook_prefs['smd_ebook_encoding']['default']);
    $which = get_pref('smd_ebook_textile', $smd_ebook_prefs['smd_ebook_textile']['default']);
    $txt_description = in_list('description', $which);
    $txt_authornote = in_list('authornote', $which);

    // Build up a giant replacement table which is then substituted into
    // the various templates before passing to the generator

    // Set up the TOC wrappers
    $toc_wrap = get_pref('smd_ebook_toc_wraptag', $smd_ebook_prefs['smd_ebook_toc_wraptag']['default']);
    $toc_class = get_pref('smd_ebook_toc_class', $smd_ebook_prefs['smd_ebook_toc_class']['default']);
    $wrapit = ($toc_wrap === 'ol') ? '#' : '*';

    $article_cnt = $ncx_cnt = $elem_cnt = $img_cnt = 0;
    $article_refs = $article_spines = $guide_refs = $landmarks = $master_image_refs = array();

    // Page break, stylesheets and heading references
    $pbr = get_pref('smd_ebook_page_break', $smd_ebook_prefs['smd_ebook_page_break']['default']);
    $css = get_pref('smd_ebook_stylesheet', $smd_ebook_prefs['smd_ebook_stylesheet']['default']);
    $hdg = get_pref('smd_ebook_heading_level', $smd_ebook_prefs['smd_ebook_heading_level']['default']);

    if ($css) {
        $css_list = quote_list(do_list($css));
        $sheets = ($css_list) ? safe_rows('name, css', 'txp_css', "name IN (" . join(',', $css_list) . ")") : array();
    }

    $sheetlist = $sheetcontent = array();
    $sheet_count = 0;

    foreach ($sheets as $stylething) {
        $content = trim($stylething['css']);

        if ($is_mobi && $content) {
            // Inline style tags
            $sheetlist[] = '<style type="text/css">' . $content . '</style>';
        } elseif ($is_epub && $content) {
            // External stylesheet references
            $sheetname = $stylething['name'] . '.css';
            $sheetlist[] = '<link rel="stylesheet" media="screen" href="'.$sheetname.'" />';
            $sheetcontent[$stylething['name']] = $content;

            // Add them to the manifest and listfile
            $article_refs[] = '<item id="smd_ebook_style_'.$sheet_count.'" media-type="text/css" href="'.$sheetname.'" />';
            $lfout[] = $sheetname;
        }

        $sheet_count++;
    }

    $sheet = join(n, $sheetlist);

    // The values used in the 'doc' template
    $html_from = array('{smd_ebook_doctype}', '{smd_ebook_namespace}', '{smd_ebook_charset}', '{smd_ebook_encoding}', '{smd_ebook_title}', '{smd_ebook_chaptitle}', '{smd_ebook_stylesheet}', '{smd_ebook_contents}');

    // Loop for each article in the collection
    foreach (ps('smd_ebook_articles') as $artid) {
        $article_cnt++;

        $id = str_replace('smd_ebook_article_', '', $artid);
        $row = safe_row('*, UNIX_TIMESTAMP(Posted) AS uPosted, UNIX_TIMESTAMP(LastMod) AS uModified', 'textpattern', "ID = '" . doSlash($id) . "'");

        if ($row) {
            // Initialize a few things
            $note_content = '';
            $image_list = array();
            $reps['{smd_ebook_file_name}'] = $cur_file = $row['url_title'] . '.html';
            $reps['{smd_ebook_encoding}'] = $encoding;

            // Each of the items starting !isset() are only ever loaded _once_ from the
            // first article in which they are found.
            // Begin by setting up the file names
            if (!isset($firstfile)) {
                $firstfile = $row['url_title'] . '.html';
                $filebase = sanitizeForFile(($outfile === '') ? $row['url_title'] : $outfile);
                $listfile = $filebase . '.smd';
                $notefile = $filebase . '_notes.html';
                $toc_file = $filebase . '_toc.html';
                $ncx_file = $filebase . '.ncx';
                $end_file = $filebase . '_end.html';
                $opf_file = $filebase . '.opf';
                $lmk_file = $filebase . '_landmark.html';
                $cover_file = 'cover.html';
                $container_file = 'container.xml';
                $mimetype_file = 'mimetype';
                $toc_ref = 'toc';
            }

            // Populate the unique ID entries direcly into the .opf template,
            // as they're only used once each
            if (!isset($reps['{smd_ebook_uid_ref}'])) {
                $val = ps('smd_ebook_fld_uid');

                if (strpos($val, 'SMD_FLD_') !== false) {
                    $valfld = str_replace('SMD_FLD_', '', $val);
                    $val = isset($row[$valfld]) ? $row[$valfld] : '';
                }

                $uid = (trim($val) === '') ? smd_ebook_uid() : $val;
                $is_isbn = (smd_ebook_is_isbn($uid));
                $is_uuid = (preg_match('/^[0-9a-f]{8,8}\-[0-9a-f]{4,4}\-[0-9a-f]{4,4}\-[0-9a-f]{4,4}\-[0-9a-f]{12,12}$/i', $uid) === 1);
                $full_uid = $reps['{smd_ebook_uid_ref}'] = ($is_uuid) ? 'urn:uuid:'.$uid : (($is_isbn) ? 'urn:isbn:'.$uid : $uid);

                $template['opf'] = str_replace('{smd_ebook_uid_ref}', (($uid) ? ' unique-identifier="uid"' : ''), $template['opf']);
                $template['opf'] = str_replace('{smd_ebook_md_uid}', (($uid) ? '<dc:identifier id="uid">' . $full_uid . '</dc:identifier>' : ''), $template['opf']);
                $template['ncx'] = str_replace('{smd_ebook_dtb_uid}', (($uid) ? '<meta name="dtb:uid" content="'.$full_uid.'" />' : ''), $template['ncx']);
            }

            // Language
            if (!isset($reps['{smd_ebook_md_lang}'])) {
                $lang = get_pref('language');
                $reps['{smd_ebook_md_lang}'] = '<dc:language>'.$lang.'</dc:language>';
                $reps['{smd_ebook_lang}'] = $lang;
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
                    $val = isset($row[$valfld]) ? ( ($valfld === 'AuthorID') ? get_author_name($row[$valfld]) : $row[$valfld] ) : '';
                }

                if ($val) {
                    $reps['{smd_ebook_md_creator}'] = '<dc:creator>'.$val.'</dc:creator>';
                    $reps['{smd_ebook_creator}'] = $val;
                }
            }

            // Doctype, namespace and charset
            if (!isset($reps['{smd_ebook_doctype}'])) {
                if ($is_mobi) {
                    $reps['{smd_ebook_doctype}'] = '';
                    $reps['{smd_ebook_namespace}'] = '';
                    $reps['{smd_ebook_charset}'] = '<meta http-equiv="Content-Type" content="text/html; charset='.$encoding.'">';
                } elseif ($is_epub) {
                    $reps['{smd_ebook_doctype}'] = '<?xml version="1.0" encoding="'.$encoding.'"?>'.n.'<!DOCTYPE html>';
                    $reps['{smd_ebook_namespace}'] = ' xmlns="http://www.w3.org/1999/xhtml"';
                    $reps['{smd_ebook_charset}'] = '<meta charset="'.$encoding.'" />';
                }
            }

            // Publication / modification date
            if (!isset($reps['{smd_ebook_md_date}'])) {
                $reps['{smd_ebook_md_date}'] = '<dc:date>'.strftime('%Y-%m-%dT%H:%M:%SZ', $row['uPosted']).'</dc:date>';
            }

            if (!isset($reps['{smd_ebook_md_modified}'])) {
                $reps['{smd_ebook_md_modified}'] = '<meta property="dcterms:modified">'.strftime('%Y-%m-%dT%H:%M:%SZ', $row['uModified']).'</meta>';
            }

            // Cover image
            if (!isset($reps['{smd_ebook_md_cover}'])) {
                if (isset($row['Image'])) {
                    $img = safe_row('*', 'txp_image', "id='" . intval($row['Image']) . "'");

                    // Only GIFs, JPGs or PNGs need apply
                    if ($img) {
                        $mime_type = (($img['ext'] === '.jpg' || $img['ext'] === '.jpeg') ? 'image/jpeg' : (($img['ext'] === '.gif') ? 'image/gif' : (($img['ext'] === '.png') ? 'image/png' : '')));

                        if ($mime_type) {
                            $img_file = $img['id'] . $img['ext'];
                            $reps['{smd_ebook_md_cover}'] = '<meta name="cover" content="cover-image" />';

                            if ($is_epub) {
                                $img_dest = 'cover' . $img['ext'];
                                $ret = copy(get_pref('path_to_site') . DS . $img_dir . DS . $img_file, $ebook_path . $img_dest);

                                if ($ret) {
                                    // Write the cover image HTML
                                    $fp = fopen($ebook_path . $cover_file, "wb");
                                    $from = array('{smd_ebook_image_link}', '{smd_ebook_image_alt}');
                                    $to = array($img_dest, $img['alt']);
                                    fwrite($fp, str_replace($from, $to, $template['cov']));
                                    fclose($fp);

                                    $reps['{smd_ebook_manifest_cover}'] = '<item id="cover" media-type="application/xhtml+xml" href="cover.html" />'
                                        .n.t.t.'<item id="cover-image" properties="cover-image" media-type="'.$mime_type.'" href="' . $img_dest . '" />';
                                    $lfout[] = $img_dest;
                                }
                            } else {
                                $reps['{smd_ebook_manifest_cover}'] = '<item id="cover-image" media-type="'.$mime_type.'" href="' . get_pref('path_to_site') . DS . $img_dir . DS . $img_file . '" />';
                            }
                        }
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
                        $var = "txt_$thingy";
                        $content = (isset($$var) && $$var) ? trim($textile->parse($val)) : trim($val);

                        if (!in_array($thingy, $setMany)) {
                            $reps['{smd_ebook_md_'.$thingy.'}'] = '<dc:'.$thingy.'>' . $content . '</dc:'.$thingy.'>';
                        }

                        // There are two titles: one for the metadata and one raw so if the title
                        // has just been found, populate the raw title too
                        if ($thingy === 'title') {
                            $reps['{smd_ebook_title}'] = $content;
                        } elseif ($thingy === 'chaptitle') {
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
            // and needs adding to the .ncx (but not to the ToC)
            if (!isset($reps['{smd_ebook_manifest_authornote}'])) {
                $val = ps('smd_ebook_fld_authornote');

                if (strpos($val, 'SMD_FLD_') !== false) {
                    $valfld = str_replace('SMD_FLD_', '', $val);
                    $val = isset($row[$valfld]) ? $row[$valfld] : '';
                }

                if ($val) {
                    $reps['{smd_ebook_manifest_authornote}'] = '<item id="smd_ebook_notes" media-type="application/xhtml+xml" href="'. $notefile.'" />';
                    $reps['{smd_ebook_spine_authornote}'] = '<itemref idref="smd_ebook_notes" />';
                    $guide_refs['notes'] = '<reference type="notes" title="' . gTxt('smd_ebook_lbl_authornote') . '" href="'.$notefile.'" />';
                    $note_content = '<span id="smd_ebook_notes"></span>' . (($txt_authornote) ? $textile->parse($val) : $val);

                    // While it's 99% likely the actual title used for the eventual book has been found,
                    // there's a slim chance it hasn't. In that case, the current row's title is used as a fallback
                    $note_title = isset($reps['{smd_ebook_title}']) ? $reps['{smd_ebook_title}'] : $row['Title'];
                    $note_content = str_replace($html_from, array($reps['{smd_ebook_doctype}'], $reps['{smd_ebook_namespace}'], $reps['{smd_ebook_charset}'], $encoding, $note_title, '', $sheet, $note_content), $template['doc']);

                    $fp = fopen($ebook_path.$notefile, "wb");
                    fwrite($fp, trim($note_content));
                    fclose($fp);
                    $lfout[] = $notefile;

                    // Add it to the .ncx
                    $ncx_cnt++;
                    $from = array('{smd_ebook_file_name}', '{smd_ebook_nav_label}', '{smd_ebook_nav_hash}', '{smd_ebook_nav_idx}');
                    $to = array($notefile, gTxt('smd_ebook_lbl_authornote'), 'smd_ebook_notes', $ncx_cnt);
                    $ncx[] = str_replace($from, $to, $template['nav']);
                }
            }

            // Note:
            //  1) a full (well-formed, hopefully) HTML file (from <html>...</html>) is generated
            //     here so the loadHTML() method is happy. The body will need reinjecting into
            //     the template after the ToC has been generated.
            //  2) The current HTML file's title is used instead of the overall book title.
            //  3) parse() is called twice to simulate secondpass. TODO: fix this
            $chap_title = isset($reps['{smd_ebook_chaptitle}']) ? $reps['{smd_ebook_chaptitle}'] : '';
            article_format_info($row); // Load article context
            $html_content = str_replace($html_from, array($reps['{smd_ebook_doctype}'], $reps['{smd_ebook_namespace}'], $reps['{smd_ebook_charset}'], $encoding, $row['Title'], $chap_title, $sheet, parse(parse($row['Body_html']))), $template['doc']);

            // Trawl through the HTML content, either:
            //  a) pulling out the given ToC entries.
            //  b) automatically creating ToC entries if the pref allows.
            //  c) finding images to copy into the ePub file structure.
            $autotoc = get_pref('smd_ebook_auto_toc', $smd_ebook_prefs['smd_ebook_auto_toc']['default']);

            // Convert the list of heading numbers into a string of numbers suitable
            // for using inside square brackets of a regex
            $autohed = get_pref('smd_ebook_auto_toc_headings', $smd_ebook_prefs['smd_ebook_auto_toc_headings']['default']);
            $autohed = join('', do_list($autohed));

            $doc = new DOMDocument();

            // Use UNIX line endings to prevent &#13; appearing in the saved XML
            $html_content = preg_replace('/\r\n/', "\n", $html_content);

            $dom_ok = $doc->loadHTML($html_content);

            if ($dom_ok) {
                $items = $doc->getElementsByTagName('*');
                $offset = $toc_cnt = 0;

                foreach ($items as $item) {
                    if ($autotoc && !$item->hasAttribute('id') && preg_match('/h(['.$autohed.'])/i', $item->nodeName, $matches)) {
                        // It's a heading. Make the anchor chain based on the heading level
                        $anchor_parts = array_fill(0, $matches[1], 'sub');
                        $anchor = join('-', $anchor_parts). ++$elem_cnt;
                        $item->setAttribute('id', $anchor);
                    }

                    if ($item->hasAttribute('id')) {
                        $ncx_cnt++;
                        $toc_cnt++;
                        $hashval = $item->getAttribute('id');

                        if ( (!isset($guide_refs['text'])) && ($toc_cnt === 1) ) {
                            $guide_href = $firstfile . (($hashval && $is_mobi) ? '#'.$hashval : '');
                            $guide_refs['text'] = '<reference type="text" title="'.gTxt('smd_ebook_lbl_text').'" href="' . $guide_href . '" />';
                            $landmarks['bodymatter'] = href(gTxt('smd_ebook_lbl_text'), $guide_href, ' epub:type="bodymatter"');
                        }

                        // mb_convert_encoding() seems to bypass the odd behaviour where apostrophes
                        // would appear in the TOC as â€™. This may actually be a band-aid to circumvent
                        // problems with the encoding in DOMDocument: perhaps if appropriate encoding is
                        // used there, this hack won't be necessary
//						$node = mb_convert_encoding(trim($item->nodeValue), 'HTML-ENTITIES', 'utf-8');
                        $node = trim($item->nodeValue);
                        $from = array('{smd_ebook_file_name}', '{smd_ebook_nav_label}', '{smd_ebook_nav_hash}', '{smd_ebook_nav_idx}');
                        $to = array($cur_file, $node, $hashval, $ncx_cnt);
                        $ncx[] = str_replace($from, $to, $template['nav']);

                        // Now it's the turn of the HTML TOC. Utilise Textile here to
                        // create the toc list from ul or ol syntax
                        $hashBits = do_list($hashval, '-');
                        $indent = count($hashBits);

                        if ( ($toc_cnt == 1) && ($indent > 1) ) {
                            // Doesn't start with h1 (begins h2, maybe) so scale back the indent.
                            // Without this, Textile produces invalid markup.
                            $offset = $indent - 1;
                        }

                        $toc_cls = (($toc_cnt == 1) && $toc_class) ? '('.$toc_class.')' : '';
                        $toc[] = str_pad('', max(1, $indent-$offset), $wrapit) . $toc_cls.' ' . href($node, $cur_file.'#'.$hashval);
                    }

                    // For ePub books, images need to be extracted separately
                    if ($is_epub && $item->nodeName === 'img') {
                        $src = $item->getAttribute('src');
                        $bits = pathinfo($src);
                        $mime_type = (($bits['extension'] === 'jpg' || $bits['extension'] === 'jpeg') ? 'image/jpeg' : (($bits['extension'] === 'gif') ? 'image/gif' : (($bits['extension'] === 'png') ? 'image/png' : '')));

                        if ($mime_type) {
                            $img = safe_row('*', 'txp_image', "id='" . intval($bits['filename']) . "'");
                            $img['name'] = trim($img['name']);

                            if ($img) {
                                $ret = copy(get_pref('path_to_site') . DS . $img_dir . DS . $bits['basename'], $ebook_path . $bits['basename']);

                                if ($ret) {
                                    if (!in_array($bits['basename'], $master_image_refs)) {
                                        $from = array('{smd_ebook_image_link}', '{smd_ebook_image_type}', '{smd_ebook_image_id}');
                                        $to = array('images' . DS . $bits['basename'], $mime_type, 'image-' . $img_cnt);
                                        $article_refs[] = str_replace($from, $to, $template['img']);
                                        $master_image_refs[] = $bits['basename'];
                                        $img_cnt++;
                                    }

                                    // Add the file to the list of inline images, destined for the .smd file.
                                    // This list of images is merged with $lfout _after_ the chapter HTML
                                    // content, so a list of images in the chapter appear below it
                                    if (!in_array($bits['basename'], $image_list)) {
                                        $image_list[] = $bits['basename'];
                                    }
                                }
                            }
                        }
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
                $html_content = str_replace($html_from, array($reps['{smd_ebook_doctype}'], $reps['{smd_ebook_namespace}'], $reps['{smd_ebook_charset}'], $encoding, $row['Title'], '', $sheet, $html_content), $template['doc']);
            } else {
                trigger_error(gTxt('smd_ebook_malformed'), E_WARNING);
            }

            $ebook_item = 'smd_ebook_item_' . $article_cnt;

            // Guide items require special dispensation and can override built-in items
            // such as 'text' (a.k.a. bodymatter / start page / welcome / start) and 'toc',
            // but not cover image or notes.
            $valid_guide_refs = array(
                'acknowledgments', 'appendix', 'afterword',
                'bibliography', 'bodymatter',
                'colophon', 'conclusion', 'contributors', 'copyright-page',
                'dedication',
                'epigraph', 'epilogue', 'errata',
                'foreword',
                'glossary',
                'imprint', 'index', 'introduction',
                'loi', // list of illustrations
                'lot', // list of tables
                'other-credits',
                'preamble', 'preface', 'prologue',
                'titlepage', 'toc',
            );

            $val = 'custom_' . get_pref('smd_ebook_fld_guide');
            $val = isset($row[$val]) ? $row[$val] : '';
            $guides = do_list($val);

            foreach ($guides as $guide_ref) {
                $guide_name = do_list($guide_ref, '|');
                $guide_hash = do_list($guide_name[0], '#');
                $guide_ref = $lmk_ref = strtolower($guide_hash[0]);

                if (($guide_ref) && in_array($guide_ref, $valid_guide_refs)) {
                    switch ($guide_ref) {
                        case 'bodymatter':
                            $guide_ref = 'text';
                            break;
                        case 'toc':
                            $toc_file = $cur_file;
                            $toc_ref = $ebook_item;
                            break;
                    }

                    $guide_title = (isset($guide_name[1])) ? $guide_name[1] : gTxt('smd_ebook_lbl_'.str_replace('-', '_', $guide_ref));
                    $guide_href = $cur_file . ((isset($guide_hash[1])) ? '#'.$guide_hash[1] : '');
                    $guide_refs[$guide_ref] = '<reference type="'.$guide_ref.'" title="' . $guide_title . '" href="' . $guide_href . '" />';
                    $landmarks[$lmk_ref] = href($guide_title, $guide_href, ' epub:type="'.$lmk_ref.'"');
                }
            }

            // Write the final HTML document to the file system
            $fp = fopen($ebook_path . $cur_file, "wb");
            fwrite($fp, trim($html_content));
            fclose($fp);

            $lfout[] = $cur_file;
            $lfout = array_merge($lfout, $image_list);

            $article_refs[] = '<item id="'.$ebook_item.'" media-type="application/xhtml+xml" href="'.$row['url_title'].'.html" />';
            $article_spines[] = '<itemref idref="' . $ebook_item . '" />';
        }
    }

    // Ensure any NULL replacements are cleared or throw errors
    $reps['{smd_ebook_opf_file}'] = (!isset($reps['{smd_ebook_opf_file}'])) ? '' : $reps['{smd_ebook_opf_file}'];
    $reps['{smd_ebook_doctype}'] = (!isset($reps['{smd_ebook_doctype}'])) ? '' : $reps['{smd_ebook_doctype}'];
    $reps['{smd_ebook_namespace}'] = (!isset($reps['{smd_ebook_namespace}'])) ? '' : $reps['{smd_ebook_namespace}'];
    $reps['{smd_ebook_charset}'] = (!isset($reps['{smd_ebook_charset}'])) ? '' : $reps['{smd_ebook_charset}'];
    $reps['{smd_ebook_chaptitle}'] = (!isset($reps['{smd_ebook_chaptitle}'])) ? '' : $reps['{smd_ebook_chaptitle}'];
    $reps['{smd_ebook_creator}'] = (!isset($reps['{smd_ebook_creator}'])) ? '' : $reps['{smd_ebook_creator}'];
    $reps['{smd_ebook_md_creator}'] = (!isset($reps['{smd_ebook_md_creator}'])) ? '' : $reps['{smd_ebook_md_creator}'];
    $reps['{smd_ebook_md_description}'] = (!isset($reps['{smd_ebook_md_description}'])) ? '' : $reps['{smd_ebook_md_description}'];
    $reps['{smd_ebook_md_subject}'] = (!isset($reps['{smd_ebook_md_subject}'])) ? '' : $reps['{smd_ebook_md_subject}'];
    $reps['{smd_ebook_md_publisher}'] = (!isset($reps['{smd_ebook_md_publisher}'])) ? '' : $reps['{smd_ebook_md_publisher}'];
    $reps['{smd_ebook_md_srp}'] = (!isset($reps['{smd_ebook_md_srp}'])) ? '' : $reps['{smd_ebook_md_srp}'];

    if (!isset($reps['{smd_ebook_md_cover}'])) {
        $reps['{smd_ebook_md_cover}'] = '';
        $reps['{smd_ebook_manifest_cover}'] = '';
    }

    if (!isset($reps['{smd_ebook_manifest_authornote}'])) {
        $reps['{smd_ebook_manifest_authornote}'] =  '';
        $reps['{smd_ebook_spine_authornote}'] =  '';
    }

    // All the replacements are set up so prepare for book generation
    // First, create the TOC and write it to the filesystem
    if ($toc_cnt > 0) {
        $reps['{smd_ebook_spine_toc}'] = '<itemref idref="'.$toc_ref.'" />';

        if (!isset($guide_refs['toc'])) {
            $reps['{smd_ebook_manifest_toc}'] = '<item id="'.$toc_ref.'" media-type="application/xhtml+xml" href="'.$toc_file.'" />';
            $guide_refs['toc'] = '<reference type="toc" title="' . gTxt('smd_ebook_lbl_toc') . '" href="'.$toc_file.'" />';
            $landmarks['toc'] = href(gTxt('smd_ebook_lbl_toc'), $toc_file, ' epub:type="toc"');
            $html_toc = $textile->parse(join(n, $toc));
            $final_toc = str_replace(array('{smd_ebook_toc_list}', '{smd_ebook_stylesheet}', '{smd_ebook_toc}'), array($html_toc, $sheet, gTxt('smd_ebook_lbl_toc')), $template['toc']);
            $fp = fopen($ebook_path . $toc_file, "wb");
            fwrite($fp, trim($final_toc));
            fclose($fp);
            $lfout[] = $toc_file;
        } else {
            $reps['{smd_ebook_manifest_toc}'] = '';
        }
    } else {
        $reps['{smd_ebook_manifest_toc}'] = '';
        $reps['{smd_ebook_spine_toc}'] = '';
    }

    // Add the ncx waypoints to the reps array and generate the .ncx file
    if ($ncx_cnt > 0) {
        $reps['{smd_ebook_ncx_doctype}'] = ($is_mobi) ? '<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">' : '';
        $reps['{smd_ebook_ncx_map}'] = join(n, $ncx);
        $reps['{smd_ebook_manifest_ncx}'] = '<item id="ncx" media-type="application/x-dtbncx+xml" href="'.$ncx_file.'" />';
//		$reps['{smd_ebook_spine_ncx}'] = '<itemref idref="ncx" />';
        $reps['{smd_ebook_spine_ncx_ref}'] = 'toc="ncx"';
        $ncx_file_content = trim(strtr($template['ncx'], $reps));
        $fp = fopen($ebook_path . $ncx_file, "wb");
        fwrite($fp, $ncx_file_content);
        fclose($fp);
        $lfout[] = $ncx_file;
    } else {
        $reps['{smd_ebook_manifest_ncx}'] = '';
//		$reps['{smd_ebook_spine_ncx}'] = '';
        $reps['{smd_ebook_spine_ncx_ref}'] = '';
    }

    if ($is_epub) {
        // Create supplemental files for ePub format
        // First the mimetype
        $fp = fopen($ebook_path . $mimetype_file, "wb");
        fwrite($fp, 'application/epub+zip');
        fclose($fp);

        // Then the container
        $fp = fopen($ebook_path . $container_file, "wb");
        $from = array('{smd_ebook_opf_file}');
        $to = array($opf_file);
        fwrite($fp, str_replace($from, $to, $template['inf']));
        fclose($fp);

        // Then the landmarks
        $lmk_list = array();
        foreach ($landmarks as $type => $landmark) {
            $lmk_list[] = tag($landmark, 'li');
        }
        $fp = fopen($ebook_path . $lmk_file, "wb");
        $from = array('{smd_ebook_guide}', '{smd_ebook_encoding}', '{smd_ebook_lang}', '{smd_ebook_landmarks}');
        $to = array(gTxt('smd_ebook_guide'), $encoding, $lang, join(n.t, $lmk_list));
        fwrite($fp, str_replace($from, $to, $template['lmk']));
        fclose($fp);
        $article_refs[] = '<item id="landmarks" media-type="application/xhtml+xml" href="'.$lmk_file.'" />';
        $lfout[] = $lmk_file;

        // Then any stylesheets
        foreach ($sheetcontent as $sheet => $content) {
            $fp = fopen($ebook_path . $sheet.'.css', "wb");
            fwrite($fp, $content);
            fclose($fp);
        }

        // Transform .ncx to .end, if necessary, and generate manifest + spine entries.
        // END (Epub Navigation Document) supersedes the deprecated NCX in ePub3.
        if ($ncx_cnt > 0) {
            if (class_exists('XSLTProcessor')) {
                $xslt = new XSLTProcessor();
                $xslt->importStylesheet(new SimpleXMLElement($template['ncx2end']));
                $ebook_end = $xslt->transformToXml(new SimpleXMLElement($ncx_file_content));
            } else {
                $ebook_end = str_replace(array('{smd_ebook_toc_list}', '{smd_ebook_toc}'), array($html_toc, gTxt('smd_ebook_lbl_toc')), $template['end']);
            }
            $fp = fopen($ebook_path . $end_file, "wb");
            fwrite($fp, trim($ebook_end));
            fclose($fp);
            $lfout[] = $end_file;
        }

        $reps['{smd_ebook_md_x}'] = '';
        $reps['{smd_ebook_guide_cover}'] = '<reference type="cover" title="Cover" href="cover.html" />';
        $reps['{smd_ebook_spine_cover}'] = '<itemref idref="cover" linear="no" />';
        $reps['{smd_ebook_manifest_end}'] = '<item id="end" properties="nav" href="'.$end_file.'" media-type="application/xhtml+xml" />';
    } elseif ($is_mobi) {
        $reps['{smd_ebook_md_x}'] = str_replace(array('{smd_ebook_encoding}', '{smd_ebook_md_srp}'), array($encoding, $reps['{smd_ebook_md_srp}']), $template['xmd']);
        $reps['{smd_ebook_guide_cover}'] = '';
        $reps['{smd_ebook_spine_cover}'] = '';
        $reps['{smd_ebook_manifest_end}'] = '';
        $reps['{smd_ebook_landmark_nav}'] = '';
    }

    // Build the remaining manifest replacements and generate the OPF
    $reps['{smd_ebook_guide_extras}'] = ($guide_refs) ? join(n.t.t, $guide_refs) : '';
    $reps['{smd_ebook_manifest_items}'] = join(n.t.t, $article_refs);
    $reps['{smd_ebook_spine_items}'] = join(n, $article_spines);

    $opf_file_content = strtr($template['opf'], $reps);
    $fp = fopen($ebook_path . $opf_file, "wb");
    fwrite($fp, trim($opf_file_content));
    fclose($fp);
    $lfout[] = $opf_file;

    // Write the listfile, which contains a list of all the files used in this stage
    $fp = fopen($ebook_path . $listfile, "wb");
    fwrite($fp, join(n, $lfout));
    fclose($fp);

    // Hand off to Stage 2 to do the deed
    smd_ebook_generate($listfile, $opf_file, $bType, $ebook_folder);
}

// ------------------------
// Stage 2 only: Pre-requisites are that the necessary files (toc, .html, ncx + opf)
// have already been generated by the previous stage. If called directly via the
// GUI, the hidden form value containing the OPF file is read.
function smd_ebook_generate($listfile='', $opf_file='', $booktype='', $ebook_folder='')
{
    global $smd_ebook_prefs, $img_dir;

    $report = array();
    $retval = NULL;

    // Use passed in values in lieu of the one in the form
    $opf_file = ($opf_file) ? $opf_file : ps('smd_ebook_opf_file');
    $listfile = ($listfile) ? $listfile : ps('smd_ebook_listfile');
    $booktype = ($booktype) ? $booktype : ps('smd_ebook_type');
    $ebook_folder = ($ebook_folder) ? $ebook_folder : ps('smd_ebook_folder');
    $is_mobi = ($booktype === 'mobi');
    $is_epub = ($booktype === 'zip');

    // File credentials
    $outpath = get_pref('tempdir') . DS . $ebook_folder . DS;
    $outfile = ps('smd_ebook_pubfile');

    if (empty($outfile)) {
        $info = explode('.', $listfile);
        $basepart = array_slice($info, 0, count($info)-1);
        $outfile = join('', $basepart);
    }

    $outfile .= (($is_epub) ? '.epub' : (($is_mobi) ? '.mobi' : ''));

    $downloadit = ps('smd_ebook_download');
    $fileit = ps('smd_ebook_to_files');

    if ($downloadit) {
        smd_ebook_download($outpath . $outfile);
    } elseif ($fileit) {

        @include_once txpath.'/include/txp_file.php';

        // Copy the file to the files area
        $destfilepath = get_pref('file_base_path') . DS . $outfile;
        $filesize = filesize($outpath . $outfile);
        copy($outpath . $outfile, $destfilepath);

        // Get the file category
        $filecat = get_pref('smd_ebook_file_cat', $smd_ebook_prefs['smd_ebook_file_cat']['default']);

        // Read description and title from .opf
        $doc = new DOMDocument();
        $content = file_get_contents(get_pref('tempdir') . DS . $ebook_folder . DS . $opf_file);
        $dom_ok = $doc->loadXML($content);

        $description = $title = '';
        if ($dom_ok) {
            $items = $doc->getElementsByTagName('*');

            foreach ($items as $item) {
                if ($item->nodeName === 'dc:title') {
                    $title = $item->nodeValue;
                }

                if ($item->nodeName === 'dc:description') {
                    $description = $item->nodeValue;
                }
            }
        }

        $curid = safe_field('id', 'txp_file', "filename='".doSlash($outfile)."'");

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
                $msg = gTxt('smd_ebook_updated', array('{id}' => $curid));
            } else {
                $msg = gTxt('smd_ebook_not_filed');
            }

        } else {
            // Make a new entry in the database for it
            $newid = file_db_add(doSlash($outfile), doSlash($filecat), '', doSlash($description), doSlash($filesize), doSlash($title));

            if ($newid) {
                $msg = gTxt('smd_ebook_filed', array('{id}' => $newid));
            } else {
                $msg = gTxt('smd_ebook_not_filed');
            }
        }

    } else {
        // (Re)generate the book
        $msg = '';
        $master_img_list = array();

        switch ($booktype) {
            case 'mobi':
                list($report, $retval) = smd_ebook_kindlegen($opf_file, $ebook_folder);

                if ($retval > 1) {
                    $msg = gTxt('smd_ebook_generate_failed', array('{code}' => $retval));
                } else {
                    $msg = gTxt('smd_ebook_generate_ok');
                }

                break;

            case 'zip':
                // All the files are currently in a flat file structure (for ease of browsing/editing).
                // To add them to the zip, they need to be put in a specific file tree.
                $base_dir = get_pref('tempdir') . DS . $ebook_folder . DS;
                $dest_dir = $base_dir . 'zipped' . DS;
                $meta_dir = $dest_dir . 'META-INF';
                $oebps_dir = $dest_dir . 'OEBPS';
                $oebps_img_dir = $oebps_dir . DS . 'images';

                $report[] = 'Files in base folder: ' . $base_dir;

                $zip = new smd_crunch_dZip($outpath . $outfile);

                // Add the static files and folder structure
                $static_files = array(
                    'mimetype'      => $dest_dir,
                    'container.xml' => $meta_dir . DS,
                    'cover.html'    => $oebps_dir . DS,
                    ''              => $oebps_img_dir . DS, // No file added yet, just create the folder
                );

                foreach ($static_files as $fn => $to) {
                    if (!is_readable($to)) {
                        if (mkdir($to)) {
                            $report[] = 'Created folder: ' . $to;
                        } else {
                            $report[] = 'Failed to create folder: ' . $to;
                        }
                    }

                    $add_to_zip = str_replace($dest_dir, '', $to);
                    if ($add_to_zip !== '') {
                        $zip->addDir($add_to_zip);
                    }

                    if ($fn !== '') {
                        if (copy($base_dir . $fn, $to . $fn)) {
                            $destfile = str_replace($dest_dir, '', $to) . $fn;
                            $zip->addFile($to . $fn, $destfile);
                            $report[] = 'Added file: ' . $destfile;
                        } else {
                            $report[] = 'Failed to add file: ' . $destfile;
                        }
                    }
                }

                // Add each file given in the .smd master file
                $files = file($base_dir . $listfile);
                $files = doArray($files, 'trim');

                foreach ($files as $file) {
                    $info = explode('.', $file);
                    $lastpart = count($info)-1;
                    $ext = trim($info[$lastpart]);

                    switch ($ext) {
                        case 'html':
                        case 'css':
                        case 'ncx':
                        case 'opf':
                            $destfile = $oebps_dir . DS . $file;
                            if (copy($base_dir . $file, $destfile)) {

                                // Translate fixed (image) paths into relative ones
                                if ($ext === 'html') {
                                    $content = file_get_contents($destfile);
                                    $content = str_replace(get_pref('path_to_site') . DS . $img_dir . DS, 'images' . DS, $content);
                                    $fh = fopen($destfile, 'w');
                                    fwrite($fh, $content);
                                    fclose($fh);
                                }

                                $zip->addFile($oebps_dir . DS . $file, 'OEBPS' . DS . $file);
                                $report[] = 'Added file: OEBPS' . DS . $file;
                            }

                            break;
                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                        case 'png':
                            if (strpos($file, 'cover') === 0) {
                                $picdir = $oebps_dir;
                            } else {
                                $picdir = $oebps_img_dir;
                            }

                            $destfile = $picdir . DS . $file;
                            $rel_dir = str_replace($dest_dir, '', $picdir);
                            if (!file_exists($destfile) && copy($base_dir . $file, $destfile)) {
                                $report[] = 'Added file: ' . $rel_dir . DS . $file;
                            }

                            // Guard against adding the same image twice
                            if (!in_array($file, $master_img_list)) {
                                $zip->addFile($destfile, $rel_dir . DS . $file);
                                $master_img_list[] = $file;
                            }

                            break;
                    }
                }

                $zip->save();
                $report[] = 'Generated final ePub file: ' . $outpath . $outfile;
                $msg = gTxt('smd_ebook_generate_ok');
                $retval = 0; // Success! TODO: trap errors and report failure

                break;
        }
    }

    smd_ebook_ui($msg, $listfile, join(n, $report), $retval, $ebook_folder);
}

// ------------------------
function smd_ebook_download($fullpath)
{
    $filesize = filesize($fullpath);
    $outfile = basename($fullpath);

    ob_clean();
    header('Content-Description: File Download');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$outfile.'"; size = "'.$filesize.'"');
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: no-cache, must-revalidate, max-age=60");
    header("Expires: Sat, 01 Jan 2000 12:00:00 GMT");
    header('Cache-Control: private');
    set_time_limit(0);

    if ($file = fopen($fullpath, 'rb')) {
        while(!feof($file) and (connection_status() == 0)) {
            echo fread($file, 1024*64);
            ob_flush();
            flush();
        }

        fclose($file);
    }

    exit; // Don't call page end
}

// ------------------------
// Interface with kindlegen to generate the .mobi file.
function smd_ebook_kindlegen($opf, $folder)
{
    global $smd_ebook_prefs;

    $kgen = get_pref('smd_ebook_kindlegen_path', $smd_ebook_prefs['smd_ebook_kindlegen_path']['default']);
    $command = $kgen . ' ' . get_pref('tempdir') . DS . $folder . DS . $opf;
    exec($command, $output, $result);

    return array($output, $result);
}

// ------------------------
// Common buttons for the interface
function smd_ebook_buttons($curr='mgr')
{
    global $smd_ebook_event;

    $ret = array (
        'btnMgr' => sLink($smd_ebook_event, '', gTxt('smd_ebook_lbl_mgr'), 'navlink' . ($curr === 'mgr' ? ' smd_active' : '')),
        'btnPrf' => sLink($smd_ebook_event, 'smd_ebook_prefs', gTxt('smd_ebook_lbl_prf'), 'navlink' . ($curr === 'prf' ? ' smd_active' : '')),
        'btnCln' => sLink($smd_ebook_event, 'smd_ebook_tidy', gTxt('smd_ebook_lbl_cln'), 'navlink' . ($curr === 'cln' ? ' smd_active' : '')),
        'btnTst' => href(gTxt('smd_ebook_lbl_tst'), 'index.php?event='.$smd_ebook_event.a.'step=smd_ebook_test'.a.'_txp_token='.form_token(), ' class="navlink"'),
    );

    return $ret;
}

// ------------------------
// Tidy up the temp dir
function smd_ebook_tidy($msg='')
{
    global $smd_ebook_event;

    require_privs('plugin_prefs.'.$smd_ebook_event);

    if (ps('smd_ebook_cleanup')) {
        $to_delete = ps('smd_ebook_files');

        foreach($to_delete as $del) {
            $path = realpath(get_pref('tempdir') . DS . $del);
            unlink($path);
        }

        $msg = gTxt('smd_ebook_deleted');
    }

    pagetop(gTxt('smd_ebook_tab_name'), $msg);
    extract(smd_ebook_buttons('cln'));

    $btnbar = (has_privs('plugin_prefs.'.$smd_ebook_event))? '<span class="smd_ebook_buttons">'.$btnMgr.n.$btnPrf.n.$btnCln.'</span>' : '';

    $filelist = array();
    $valid = array('mobi', 'html', 'ncx', 'opf', 'smd', 'xml');
    $tmp = get_pref('tempdir') . DS;

    // Grab all files then remove unnecessary ones: faster than multiple globs
    // for each file type and more robust than relying on GLOB_BRACE support
    $allfiles = glob($tmp.'smd_ebook_*/*.*');

    foreach ($allfiles as $file) {
        $info = explode('.', $file);
        $lastpart = count($info)-1;
        $ext = trim($info[$lastpart]);
        if (in_array($ext, $valid)) {
            $filelist[] = $file;
        }
    }

    echo n.'<div id="' . $smd_ebook_event . '_control" class="txp-control-panel">' . $btnbar . '</div>';

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
            $selout[] = t.'<option value="'.$key.'">'.txpspecialchars($leaf).'</option>'.n;
        }

        $selout[] = '</select>';
        $filesel = join(n, $selout);
    }

    echo n.'<div class="txp-list">';
    echo n.startTable();
    echo n.'<form method="post" action="?event='.$smd_ebook_event.'">';
    echo n.tr(tda(strong(gTxt('smd_ebook_tidy'))));
    echo ($filesel) ? n.tr(tda($filesel)) : n.tr(tda(gTxt('smd_ebook_no_files')));
    echo n.tr(tda(fInput('submit', 'smd_ebook_cleanup', gTxt('delete'), 'publish'), ' class="noline"'));
    echo n.sInput('smd_ebook_tidy');
    echo n.tInput();
    echo n.'</form>';
    echo n.endTable();
    echo n.'</div>';
}

// ------------------------
// Handle the prefs panel
function smd_ebook_prefs($msg='')
{
    global $smd_ebook_event, $smd_ebook_prefs, $step;

    require_privs('plugin_prefs.'.$smd_ebook_event);

    if (ps('smd_ebook_pref_save')) {
        foreach ($smd_ebook_prefs as $idx => $prefobj) {
            $val = ps($idx);
            $val = (is_array($val)) ? join(', ', $val) : $val;
            set_pref($idx, doSlash($val), 'smd_ebook', $prefobj['type'], $prefobj['html'], $prefobj['position']);
        }

        $msg = gTxt('preferences_saved');
    }

    pagetop(gTxt('smd_ebook_tab_name'), $msg);
    extract(smd_ebook_buttons('prf'));

    $btnbar = (has_privs('plugin_prefs.'.$smd_ebook_event))? '<span class="smd_ebook_buttons">'.$btnMgr.n.$btnPrf.n.$btnCln.'</span>' : '';

    echo n. <<<EOJS
<script type="text/javascript">
jQuery(function() {
    jQuery("select[name='smd_ebook_fld_uid'], select[name='smd_ebook_fld_title'], select[name='smd_ebook_fld_chaptitle'], select[name='smd_ebook_fld_author'], select[name='smd_ebook_fld_description'], select[name='smd_ebook_fld_authornote'], select[name='smd_ebook_fld_subject'], select[name='smd_ebook_fld_publisher'], select[name='smd_ebook_fld_srp']").change(function() {
        var xtra = jQuery(this).attr('name') + '_fixed';
        if (jQuery('option:selected', this).val() === 'SMD_FIXED') {
            jQuery("input[name='"+xtra+"']").parent().parent().show('normal');
        } else {
            jQuery("input[name='"+xtra+"']").parent().parent().hide('fast');
        }
    }).change();
});
</script>
EOJS;
    echo n.'<div id="'.$smd_ebook_event.'_control" class="txp-control-panel">' . $btnbar . '</div>';

    $out = array();
    $out[] = n.'<div class="plugin-column">';
    $out[] = '<form name="smd_ebook_prefs" id="smd_ebook_prefs" class="prefs-form" action="index.php" method="post">';
    $out[] = '<div class="txp-layout-textbox">';
    $out[] = eInput($smd_ebook_event);
    $out[] = sInput('smd_ebook_prefs');
    $grpout = array();

    foreach ($smd_ebook_prefs as $idx => $prefobj) {
        $val = get_pref($idx, $prefobj['default'], 1);
        $vis = (isset($prefobj['visible']) && !$prefobj['visible']) ? 'smd_hidden' : '';

        switch ($prefobj['html']) {
            case 'text_input':
                $grpout[$prefobj['group']][] = inputLabel($idx, fInput('text', $idx, $val, '', '', '', '', '', $idx), $idx, '', $vis);
                break;
            case 'yesnoradio':
                $grpout[$prefobj['group']][] = inputLabel($idx, yesnoRadio($idx, $val), '', '', $vis);
                break;
            case 'radioset':
                $grpout[$prefobj['group']][] = inputLabel($idx, radioSet($prefobj['content'], $idx, $val), '', '', $vis);
                break;
            case 'checkboxset':
                $vals = do_list($val);
                $lclout = array();
                foreach ($prefobj['content'] as $cb => $val) {
                    $checked = in_array($cb, $vals);
                    $lclout[] = checkbox($idx.'[]', $cb, $checked). gTxt($val);
                }
                $grpout[$prefobj['group']][] = inputLabel($idx, join(n, $lclout), '', '', $vis);
                break;
            case 'selectlist':
                $grpout[$prefobj['group']][] = inputLabel($idx, selectInput($idx, $prefobj['content'][0], $val, $prefobj['content'][1], '', $idx), $idx, '', $vis);
                break;
            default:
                if ( strpos($prefobj['html'], 'smd_ebook_') !== false && is_callable($prefobj['html']) ) {
                    $grpout[$prefobj['group']][] = inputLabel($idx, $prefobj['html']($idx, $val), $idx, '', $vis);
                }
                break;
        }
    }

    foreach ($grpout as $grp => $content) {
        $out[] = '<div role="region" id="smd_ebook_group_' . $grp . '" class="txp-details" aria-labelledby="smd_ebook_group_' . $grp . '-label">'.
                n. '<h3 id="smd_ebook_group_' . $grp . '-label" class="lever txp-summary' . (get_pref('pane_'.$grp.'_visible') ? ' expanded' : '') . '">'.
                n. '<a href="#' . $grp . '" role="button">' . gTxt($grp) . '</a>'.
                n. '</h3>'.
                n. '<div id="' . $grp . '" class="toggle" role="group" style="display:' . (get_pref('pane_'.$grp.'_visible') ? 'block' : 'none') . '">';

        foreach ($content as $row) {
            $out[] = $row;
        }

        $out[] = '</div>';
        $out[] = '</div>';
    }

    if (smd_ebook_kindlegen_available()) {
        $out[] = graf($btnTst);
    }

    if ($step === 'smd_ebook_test') {
        $out[] = graf(text_area('smd_ebook_test_results', 150, 200, ps('smd_ebook_test_output')));
    }

    $out[] = graf(fInput('submit', 'smd_ebook_pref_save', gTxt('save'), 'publish'));
    $out[] = tInput();
    $out[] = '</div></form></div>';

    echo join(n, $out);
}

// ------------------------
// Delete plugin prefs
function smd_ebook_prefs_remove($showpane=1)
{
    $message = '';

    safe_delete('txp_prefs', "name like 'smd_ebook_%'");

    if ($showpane) {
        $message = gTxt('smd_ebook_prefs_deleted');
        smd_ebook($message);
    }
}

// ------------------------
// Mini diagnostics to see if the kindlegen program can be run on this host.
function smd_ebook_test()
{
    global $smd_ebook_event, $smd_ebook_prefs;

    require_privs('plugin_prefs.'.$smd_ebook_event);

    $out = '';
    $kgen = get_pref('smd_ebook_kindlegen_path', $smd_ebook_prefs['smd_ebook_kindlegen_path']['default']);

    exec($kgen, $output, $retval);

    if ($retval != 0) {
        switch ($retval) {
            case 126:
                $out = gTxt('smd_ebook_permissions_issue');
                break;
            case 127:
                $out = gTxt('smd_ebook_not_found');
                break;
            default:
                $out = gTxt('smd_ebook_error_code', array('{code}' => $retval));
                break;
        }

        $out = print_r($output, true);
    } else {
        $out = gTxt('smd_ebook_ok');
    }

    $_POST['smd_ebook_test_output'] = $out;
    $msg = gTxt('smd_ebook_test_complete');
    smd_ebook_prefs($msg);
}

// ------------------------
// List of numbers for heading levels
function smd_ebook_number($name, $val='')
{
    // Can't use range() since it creates indices starting at 0
    $nums = array();
    for ($idx = 1; $idx <= 6; $idx++) {
        $nums[$idx] = $idx;
    }
    return selectInput($name, $nums, $val, false, '', $name);
}

// ------------------------
// List of current file categories
function smd_ebook_file_cat_list($name, $val='')
{
    $rs = getTree('root', 'file');
    if ($rs) {
        return treeSelectInput($name, $rs, $val, $name);
    }
}

// ------------------------
// Multi-select list of current stylesheets
function smd_ebook_style_list($name, $val='')
{
    $styles = safe_column('name', 'txp_css', '1=1');
    $sels = do_list($val);

    $ulist = array();
    $ulist[] = '<select name="'.$name.'[]" id="'.$name.'" class="list multiple" multiple="multiple" size="6">';
    $ulist[] = '<option value=""></option>';

    foreach ($styles as $style) {
        $selected = in_array($style, $sels) ? ' selected="selected"' : '';
        $ulist[] = '<option value="'.$style.'"'.$selected.'>' . txpspecialchars($style) . '</option>';
    }

    $ulist[] = '</select>';

    return join(n, $ulist);
}

// ------------------------
// List of current sections
// TODO: multiple select?
function smd_ebook_section_list($name, $val='')
{
    $secs = safe_column('name', 'txp_section', '1=1');

    return selectInput($name, $secs, $val, true, '', $name);
}

// ------------------------
// Select list of custom fields
function smd_ebook_cf_list($name, $val='')
{
    $cfs = getCustomFields();

    return selectInput($name, $cfs, $val, true, '', $name);
}

// ------------------------
// Select list of custom fields with a few extras
function smd_ebook_fld_list($name, $val='')
{
    $cfs = getCustomFields();
    $cfs['Title'] = gTxt('title');
    $cfs['Excerpt_html'] = gTxt('excerpt');
    $cfs['SMD_FIXED'] = gTxt('smd_ebook_fixed');

    return selectInput($name, $cfs, $val, true, '', $name);
}

// ------------------------
// Select list of custom fields with a few more extras
function smd_ebook_fld_list_plus($name, $val='')
{
    $cfs = getCustomFields();
    $cfs['Title'] = gTxt('title');
    $cfs['Excerpt_html'] = gTxt('excerpt');
    $cfs['Category1'] = gTxt('category1');
    $cfs['Category2'] = gTxt('category2');
    $cfs['Section'] = gTxt('section');
    $cfs['SMD_FIXED'] = gTxt('smd_ebook_fixed');

    return selectInput($name, $cfs, $val, true, '', $name);
}

// ------------------------
// List of custom fields
function smd_ebook_fld_list_author($name, $val='')
{
    $cfs = getCustomFields();
    $cfs['Title'] = gTxt('title');
    $cfs['Excerpt_html'] = gTxt('excerpt');
    $cfs['AuthorID'] = gTxt('author');
    $cfs['SMD_FIXED'] = gTxt('smd_ebook_fixed');

    return selectInput($name, $cfs, $val, true, '', $name);
}

// ------------------------
// Multi-select list of privilege levels
function smd_ebook_priv_list($name, $val='')
{
    $grps = get_groups();
    unset($grps['0']); // Remove 'none'
    unset($grps['1']); // Remove publishers -- they get access to everything already

    $sels = do_list($val);

    $ulist = array();
    $ulist[] = '<select name="'.$name.'[]" id="'.$name.'" class="list multiple" multiple="multiple" size="6">';

    foreach ($grps as $lvl => $grp) {
        $selected = in_array($lvl, $sels) ? ' selected="selected"' : '';
        $ulist[] = '<option value="'.$lvl.'"'.$selected.'>' . txpspecialchars($grp) . '</option>';
    }

    $ulist[] = '</select>';

    return join(n, $ulist);
}

// -------------------------------------------------------------
// Frankenteined from http://www.php.net/manual/en/function.uniqid.php#88400
function smd_ebook_uid()
{
    $pr_bits = '';

    $fp = @fopen('/dev/urandom', 'rb');

    if ($fp !== false) {
        $pr_bits .= @fread($fp, 16);
        fclose($fp);
    } else {
        for ($idx = 0; $idx < 16; $idx ++) {
            $pr_bits .= chr(mt_rand(0, 255));
        }
    }

    $time_low = bin2hex(substr($pr_bits, 0, 4));
    $time_mid = bin2hex(substr($pr_bits, 4, 2));
    $time_hi_and_version = bin2hex(substr($pr_bits, 6, 2));
    $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits, 8, 2));
    $node = bin2hex(substr($pr_bits, 10, 6));
    $time_hi_and_version = hexdec($time_hi_and_version);
    $time_hi_and_version = $time_hi_and_version >> 4;
    $time_hi_and_version = $time_hi_and_version | 0x4000;
    $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

    return sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
}

/**
 * Checks whether the given string is a valid ISBN.
 *
 * @copyright  Copyright (c) 2009 Martijn Korse (http://devshed.excudo.net)
 * @param string $isbn	The number to check. Can be a 10- or 13-digit string, with or without space/hyphens betwwn digit groups
 *
 * @return bool
 */
function smd_ebook_is_isbn($isbn)
{
    // Remove spaces/dashes
    $isbn = preg_replace('/[\s\-]/', '', $isbn);

    if (strlen($isbn) == 10) {

        $subTotal = 0;
        $mpBase = 10;

        for ($x=0; $x<=8; $x++) {
            $mp = $mpBase - $x;
            $subTotal += ($mp * $isbn[$x]);
        }

        $rest = $subTotal % 11;
        $checkDigit = $isbn[9];

        if (strtolower($checkDigit) === 'x') {
            $checkDigit = 10;
        }

        return ($checkDigit == (11 - $rest));

    } elseif (strlen($isbn) == 13) {
        $subTotal = 0;

        for ($x=0; $x<=11; $x++) {
            $mp = ($x + 1) % 2 == 0 ? 3 : 1;
            $subTotal += $mp * $isbn[$x];
        }

        $rest = $subTotal % 10;
        $checkDigit = $isbn[12];

        if (strtolower($checkDigit) === 'x') {
            $checkDigit = 10;
        }

        return ($checkDigit == (10 - $rest));

    } else {
        return false;
    }
}

// -------------------------------------------------------------
// Modified from http://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
function smd_ebook_kindlegen_available()
{
    global $smd_ebook_prefs;

    static $available;

    if (!isset($available)) {
        // Has the kindlegen file been uploaded, configured and permissions set correctly?
        $kgen = get_pref('smd_ebook_kindlegen_path', $smd_ebook_prefs['smd_ebook_kindlegen_path']['default']);
        $available = (is_executable($kgen));

        if (ini_get('safe_mode')) {
            $available = false;
        } else {
            $d = ini_get('disable_functions');
            $s = ini_get('suhosin.executor.func.blacklist');
            if ("$d$s") {
                $array = preg_split('/,\s*/', "$d,$s");
                if (in_array('exec', $array)) {
                    $available = false;
                }
            }
        }
    }

    return $available;
}

// -------------------------------------------------------------
function smd_ebook_save_pane_state()
{
    $panes = array('smd_ebook_usrset', 'smd_ebook_pubset', 'smd_ebook_settings');
    $pane = gps('pane');

    if (in_array($pane, $panes))
    {
        set_pref("pane_{$pane}_visible", (gps('visible') === 'true' ? '1' : '0'), 'smd_ebook', PREF_HIDDEN, 'yesnoradio', 0, PREF_PRIVATE);
        send_xml_response();
    } else {
        send_xml_response(array('http-status' => '400 Bad Request'));
    }
}

// ------------------------
// Set up the global prefs for the plugin
function smd_ebook_get_prefs()
{
    global $smd_ebook_prefs;

    $sitepath = get_pref('path_to_site');

    $smd_ebook_prefs = array(
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
            'content'  => array('ul' => gTxt('smd_ebook_lbl_ul'), 'ol' => gTxt('smd_ebook_lbl_ol')),
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
            'default'  => $sitepath.DS.'kindle'.DS.'kindlegen',
            'group'    => 'smd_ebook_settings',
        ),
        'smd_ebook_auto_toc' => array(
            'html'     => 'yesnoradio',
            'type'     => PREF_HIDDEN,
            'position' => 10,
            'default'  => '1',
            'group'    => 'smd_ebook_pubset',
        ),
        'smd_ebook_auto_toc_headings' => array(
            'html'     => 'text_input',
            'type'     => PREF_HIDDEN,
            'position' => 15,
            'default'  => '1,2,3',
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
        'smd_ebook_fld_uid' => array(
            'html'     => 'smd_ebook_fld_list',
            'type'     => PREF_HIDDEN,
            'position' => 105,
            'default'  => '',
            'group'    => 'smd_ebook_pubset',
        ),
        'smd_ebook_fld_uid_fixed' => array(
            'html'     => 'text_input',
            'type'     => PREF_HIDDEN,
            'position' => 110,
            'default'  => '',
            'group'    => 'smd_ebook_pubset',
            'visible'  => false,
        ),
        'smd_ebook_fld_guide' => array(
            'html'     => 'smd_ebook_cf_list',
            'type'     => PREF_HIDDEN,
            'position' => 115,
            'default'  => '',
            'group'    => 'smd_ebook_pubset',
        ),
        'smd_ebook_heading_level' => array(
            'html'     => 'smd_ebook_number',
            'type'     => PREF_HIDDEN,
            'position' => 120,
            'default'  => '2',
            'group'    => 'smd_ebook_pubset',
        ),
        'smd_ebook_file_cat' => array(
            'html'     => 'smd_ebook_file_cat_list',
            'type'     => PREF_HIDDEN,
            'position' => 130,
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

# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. smd_ebook

There are a few ways to create e-books suitable for e-readers like Kindle / Kobo / Nook / etc:

* "Install Calibre":http://calibre-ebook.com/ and use the software to guide you towards creating your book.
* Install a plugin for Adobe InDesign and let it help you create the book from your DTP files.
* Download the "command-line Kindlegen":http://www.amazon.com/gp/feature.html?ie=UTF8&docId=1000234621 tool, create all the input files manually and hope.
* Use this plugin to convert one or more articles into an e-book.

The last one is of course the focus of this plugin! Features:

* Choose articles to be converted -- order of articles in final book is alphabetical by URL title.
* Standard Textile formatting governs the (multi-level) table of contents and document entry points (plugin will automatically create ToC entry points if you choose).
* Set cover art as article image.
* Enter Description, Publisher, Genre, Author notes, ISBN and Price in plugin or use article fields (useful for publisher sites to allow authors to publish their own content).
* Tweak and preview files if necessary before final e-book generation.
* Download files for distribution via third party sites, or send them to Txp's Files tab ready for direct download by others.
* .mobi (for Kindle) or .ePub v3 (for everything else) books can be created.

h2. Installation / uninstallation

p(important). Requires Textpattern 4.5.0+. "smd_crunchers":http://stefdawson.com/smd_crunchers required for ePub generation.

Download the plugin from either "textpattern.org":http://textpattern.org/plugins/NNNN/smd_ebook, or the "software page":http://stefdawson.com/sw, paste the code into the Txp _Admin->Plugins_ pane, install and enable the plugin. Visit the "forum thread":http://forum.textpattern.com/viewtopic.php?id=YYYYY for more info or to report on the success or otherwise of the plugin.

To remove the plugin, simply delete it from the _Admin->Plugins_ pane.

h2. Setting up for Kindle (mobi)

# Obtain the "kindlegen program":http://www.amazon.com/gp/feature.html?ie=UTF8&docId=1000234621 that is compatible with your web host -- most likely the Linux version. While you're there you might as well grab the (huge!) Kindle Previewer too as it's very handy to test files made with this plugin.
# Upload kindlegen via your FTP program *as binary* to a location of your choosing on your web host; preferably outside document root so it can't be run by other people. Double check it is uploaded as binary -- some FTP software (e.g. FileZilla) is set to auto-negotiate the file type and often gets it wrong. If the plugin doesn't work, this is the most likely source of failure.
# Visit the _Content->E-books_ panel with a Publisher level account and hit the _Settings_ button. Configure the _Path to kindlegen executable_ to reflect the location of your uploaded kindlegen file. Set up any other relevant settings while you are here and save them.
# *After saving the settings* you can click the _Test kindlegen program_ link to check that the program is uploaded correctly and the plugin can find it. If everything is OK, you will be told so in a text box that appears below the link. If the kindlegen file produces errors or cannot be found, the error messages will be shown instead.

h2. Setting up for ePub

* Download "smd_crunchers":http://stefdawson.com/smd_crunchers.
* Install and activate the plugin.

h2. Writing content suitable for E-readers

While the technology and tools are improving, there are some guidelines and things to be aware of when creating content in Textpattern that will translate well into a good e-reader experience:

* Use headings to create chapters or logical breaks in your prose. You can create many articles if you wish -- perhaps one article per chapter -- and create a single file from them, or create the entire book in one article.
* Supply cover art. This must be a GIF or JPG image of dimensions 600 (w) x 800 (h) pixels. Assign the ID of the cover image uploaded to Textpattern in the _Article Image_ field of the first chapter.
* Create stylesheet(s) to lay out your table of contents or alter facets of your book. Formatting is often hit and miss because e-readers use their own internal styles, but some things can be influenced with a stylesheet. Tinker with it to see what effects you can create.
* "Inline images":#smd_ebook_images cannot flow around text in early devices -- they always appear block style.
* Add author notes to a field in your first article -- such notes appear after the cover image and before the ToC. Copyright info and acknowledgements are useful here. See the setting _Get author notes from field_.

h3. Formatting for Table of Contents (ToC)

The concept of a ToC maps nicely in Textile / HTML to the @<h1>@ - @<h6>@ tags, although you do not have to stick to that convention. Any anchor with an HTML ID will be converted into a ToC entry point by the plugin.

There are two primary methods for creating a table of contents:

# Use @h1.@ to @h6.@ Textile tags and set the plugin to automatically create ToC entries from headings.
# Manually add @id@ attributes to some/all headings (in Textile: @h2(#some-id). My Great Heading@) or other anchors.

Any anchors you miss off will be automatically assigned by the plugin if you have elected to permit this behaviour (see the plugin settings).

Use hyphens in the ID to create nested menu structures: each hyphen creates one 'level', e.g.:

bc. h2(#l1). Level 1 heading
h2(#some_heading). Another level 1 heading
h2(#l1-subbie). A sub-heading beneath the previous heading
h2(#some-name). Another sub-heading beneath @#some_heading@

Note that the names don't have to have the same prefix or follow any pattern (though it makes sense to do so for sanity's sake!), nor does the heading level bear any influence. The number of hyphens overrides the heading level to govern the nesting levels. If you don't like this feature, use underscores to separate words in your IDs, or leave the plugin to create ToC entry points for you.

h3(#smd_ebook_pbr). Page breaks

Page breaks normally occur in e-books before chapter headings. To insert page breaks into the document you have three options:

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

; *Page break character sequence*
: Use this string in your article text to denote where a page break should occur. You may "use CSS":#smd_ebook_pbr if you prefer.
: Default: @~~~~~@ (five tilde characters)
; *Render ToC as*
: Choose between creating the Table of Contents markup as:
:: Standard (@<ul>@)
:: Numeric (@<ol>@)
: Default: Standard
; *ToC CSS class name*
: The CSS class to apply to the table of contents.
: Default: @smd_ebook_toc@
; *List articles from section*
: Limit the articles in the select list to the ones in the nominated section. Otherwise, all sections are considered.
; *Stylesheets to include with the book*
: Choose Textpattern stylesheets that will be inserted into each article in the book to govern formatting.
; *Apply Textile to*
: Choose whether to pass the checked content through Textile. If the field you are choosing to represent the content has already been Textiled, it's probably not a good idea to do it a second time.
; *Character set of document*
: The character set to use in the final document. Useful values are usually @utf-8@ or @iso-8859-1@.
: Default: @utf-8@
; *Path to kindlegen executable*
: The full system file path to the kindlegen program that you uploaded to your web host (*as a binary file*!)
: It is preferable to make this a non-web-accessible location.

h3. Publishing

; *Automatically create ToC anchors on headings*
: Set to @Yes@ to automatically create anchors from all heading tags in your article(s). If you have already put some in the document, the plugin will only auto-generate ones you have missed.
: Default: Yes
; *Get book title from field*
: Nominate an article field to hold the book's title. Leave this item empty to force the title to be entered at book compilation time, or choose _Static text_ and enter a title that will be applied to all created books.
: Default: article's @Title@ field
; *Get chapter titles from field*
: Nominate an article field to hold the chapter titles. Leave this item empty to set chapters manually in the body text, or choose _Static text_ and enter a title that will be applied to every chapter.
: Any chosen item will be wrapped with HTML heading tags of the level given in the _Chapter heading level_ setting.
; *Get author from field*
: Nominate an article field to hold the author of the work. Leave this item empty to allow the author to be entered at book compilation time, or choose _Static text_ and enter an author that will be applied to all created books.
: The @Author@ entry in the list will read the Author from the first article in the book.
: Default: @Author@
; *Get description from field*
: Nominate an article field to hold the book's description. Leave this item empty to allow the description to be entered at book compilation time, or choose _Static text_ and enter a description that will be applied to all created books.
: This field will be Textiled if you have checked it in the _Apply Textile to_ setting.
; *Get author notes from field*
: Nominate an article field to hold the author notes (acknowledgements, copyright, etc). Leave this item empty to allow the notes to be entered at book compilation time, or choose _Static text_ and enter content that will be applied to all created books.
: This field will be Textiled if you have checked it in the _Apply Textile to_ setting.
; *Get subject (genre) from field*
: Nominate an article field to determine the subject or genre of the article. Leave this item empty to allow the genre to be entered at book compilation time, or choose _Static text_ and enter content that will be applied to all created books.
; *Get publisher from field*
: Nominate an article field to set the publisher of the article. Leave this item empty to allow the info to be entered at book compilation time, or choose _Static text_ and enter a publisher name that will be applied to all created books.
; *Get SRP (price) from field*
: Nominate an article field to set the download price of the article. Leave this item empty to allow the info to be entered at book compilation time, or choose _Static text_ then enter a price and optional three-letter currency code (e.g. USD, GBP, EUR), separated by a pipe symbol, that will be applied to all created books. If the currency is not supplied it will be taken from the setting _Default three-letter currency code_.
; *Default three-letter currency code*
: The three-letter "currency code":http://www.xe.com/iso4217.php of the default currency to use for the book's price.
: Default: @EUR@
; *Get unique ID from field*
: Nominate an article field to set a unique reference identifier for the book. Leave this item empty to allow the info to be entered at book compilation time, or choose _Static text_ and enter a value that will be applied to all created books (although this is not recommended for creating multiple books as each "UUID":http://en.wikipedia.org/wiki/Universally_unique_identifier code *must* be unique). You may use this field to store the e-book's ISBN. The plugin will recognise these and output the appropriate markup.
; *Guide tags defined in field*:
: To help people jump between structural components of e-books, you can 'tag' (or label / reference / whatever you want to call it) sections of the book as containing certain types of content. For example, the ToC, an acknowledgements page, glossary, index, bibliography, and so on. If you would like to take advantage of this, specify a custom field here that may contain comma-separated identifiers for the plugin to treat as Guide tags. It works well if you reuse the same custom field as __Get unique ID from field__. The following are valid identifiers to put in this field:
:: *acknowledgments* (note: spelling)
:: *appendix*
:: *afterword*
:: *bibliography*
:: *colophon*
:: *conclusion*
:: *contributors*
:: *copyright-page*
:: *dedication*
:: *epigraph*
:: *epilogue*
:: *errata*
:: *foreword*
:: *glossary*
:: *imprint*
:: *index*
:: *introduction*
:: *loi* (list of illustrations)
:: *lot* (list of tables)
:: *other-credits*
:: *preamble*
:: *preface*
:: *prologue*
:: *start-page* (not normally needed as the plugin automatically assigns it)
:: *titlepage*
:: *toc* (can be used to override the auto-generated table of contents with content of your choosing)
: If you need to reference an anchor in your content, add it after a @#@ symbol like this: @bibliography#bibli@
: If you wish to customise the item's title you may do so by adding a pipe (|) symbol after the item and typing your own title (e.g. @foreword|Foreword by Sam Hotshot@). If you don't add your own title, the plugin uses its internal Textpack.
: You can list as many identifiers as you like and freely mix @#@ and @|@ characters to build up navigable jump-to points within your e-book.
; *Chapter heading level*
: If your chapter headings are being read from an article field, they will automatically be wrapped with HTML @<hN>...</hN>@ tags, where @N@ is the value in this setting.
: Default: 2
; *Store files in category*
: If you elect to store your completed e-books in Textpattern's Files tab, this is the category to which they'll be assigned.

h3. Rights

; *Groups that can publish*
: Select the user groups that are permitted to publish e-books. Users in these groups will see a _Content->E-books_ panel but will not be permitted to alter the Settings.
; *Groups that can edit .opf*
: The .opf is the master file that governs e-book creation. Publisher account holders can always edit this file but if you have preset some of the content using the _Static text_ publishing options, being able to alter the .opf would allow someone to change the presets. For this reason you can use this setting to govern which user groups you trust to edit the .opf and potentially override the settings.

h2. Creating a book

The creation process takes place in two stages, although the plugin will have a stab at creating everything in one step if it can.

To kick things off, visit the _Content->E-books_ panel. Choose one or more articles from the first select list to create into a book. When choosing multiple files, the order they will appear in the book is the same order as they are in this list. It is governed by URL title so if you want things to appear in a different order, alter the url-only title of your articles, e.g.:

* chapter01-the-bell-tolls
* chapter02-trousers-on-fire
* chapter03-false-alarm
...

You may optionally fill in any of the remaining content fields that are presented. Only Title is mandatory. Some may already be filled with content from the indicated fields in your selected article(s). Note that such information is *only taken from the first article in which that field has data*. Thus when compiling multiple articles into a single book, it's a good idea to put all such meta data -- including article image (which is used for the cover artwork) -- in the first chapter. Once a piece of info is set, it is not altered in subsequent files that make up the same book, even if data is present in the nominated fields.

You may specify an output filename for your final masterpiece if you wish. If you do not, the filename will be that of the first chapter.

A note about the Price field: you specify up to two pieces of info in this field, separated by a pipe symbol (@|@). First the price itself and then the three-letter currency code (e.g. GBP, EUR, USD, AUD, etc). If the currency code is not supplied, the setting _Default three-letter currency code_ will be used.

After clicking _Create_, the plugin will collate all the selected articles and meta data and try to produce a complete e-book. If you chose Kindle, this will be a .mobi file; for ePub format, it tries to create a zip file if the "smd_crunchers":http://stefdawson.com/smd_crunchers plugin is installed. The success or otherwise of the process is shown in the _Build report_ box. Scroll through this info to find any errors. You may need to go back to your source documents to fix them. Alternatively you may be able to fix the problems by manually editing the various files that make up the project.

All files are listed on the left hand side of the screen. Although each project is different, the various components that may be present are:

* One .html file for each article you selected.
* Any images you have used in the book. Reveal them by clicking the [Images] link below the chapter in which they are used.
* An HTML Table of Contents with the filename of the first article in the project plus @_toc.html@.
* An author notes page (filename of first article plus @_notes.html@).
* A .ncx file which is a special (XML) waypoint and navigation aid that allows e-readers to show chapter points in a timeline and permit jumping to various parts of the document (cover, author notes, chapters, etc) from the context menu key on the reader.
* A .opf file (which may not be displayed depending on the rights assigned to your user account) which is the master (XML) record that ties all the other files together to create the e-book experience.

You can click [Edit] under any file name to open it for editing in the adjacent box, make changes to the markup and _Save_ the alterations. Do this as often as you like and, when completed, hit the _(Re)generate_ button to tell the plugin to try to create the book again. If you wish to preview any of the HTML files (for example, to check your stylesheet is applying appropriate rules) click the @[View]@ or [View ToC] link beneath the file's title. Click the 'X' in the top-right corner of the preview window, or hit the ESCape key to dismiss the preview. Images can also be previewed by clicking their filename.

If you wish to create an article with a specific language string you can do one of two things:

# Manually alter the .ncx file's @xml:lang@ attribute and the .opf file's @<dc:language>@ markup.
# Change Textpattern's admin side language from the _Admin->Preferences_ tab, then regenerate your book.

Upon successful completion of the process you can choose whether to:

* Click the _Store file_ button to copy the complete e-book file to Textpattern's Files tab. If the file does not exist, it will be created. If it exists, it will be updated with the new details (title and description as you entered them in the input boxes when the book was created, and the category as set in the plugin settings).
* Click the _Download_ button to download a copy of the complete e-book to your computer, whereby it's a good idea to test it in Kindle Previewer or Calibre, or transfer it to your real e-reader and check the navigation and formatting are to your satisfaction.

If you download your content, it is strongly suggested that you try the e-book on as many devices as you can to check for formatting errors. Amazon have the Kindle Previewer, and Calibre has a Viewer tool. Other software may exist too. Note that since kindlegen handles formatting and conversion, any generation warnings or errors are displayed in the report. But for ePub files, only broad checks are made as the package is built. It may be advantageous, therefore, to run them through a validator such as "ePubCheck":http://code.google.com/p/epubcheck/ or an "online checker":http://validator.idpf.org/.

h2. Tidying up

The plugin uses Textpattern's @tmp@ directory to store its files as it creates them. Since the editorial process may involve editing or tweaking them and so forth, the files are left in situ even after the e-book has been created.

It is up to the site admin to keep things tidy and, to this end, there's a helpful extra panel under the e-books panel called 'Tidy up'. Click that button to be shown a list of possible e-book-ish files in the tmp directory. In addition to the files that are editable after creation of an e-book, one other special file -- a .smd file -- is something the plugin uses to keep track of which files are in each project. Once an e-book has been created, this file is also no longer of any use.

Select the files you want to delete and hit the _Delete_ button. No warning is given: they are deleted immediately.

h2. Author and credits

Plugin written by "Stef Dawson":http://stefdawson.com/contact. For other software by me, or to make a donation, see the "software page":http://stefdawson.com/sw.

While the code to glue all the various parts together is mine, the various websites, blogs and forums I had to trawl to gather the info are many. Thank you to anybody who has posted Kindle / ePub / .mobi / e-reader tricks, tips and guides. Without you I could not have completed this plugin because official documentation on the kindlegen program is surprisingly lacking. Thanks also to Amazon techs for writing the kindlegen program.

h2. Changelog

* 19 Mar 2012 | 0.10 | Beta release
* 13 Jun 2013 | 0.20 | For textpattern 4.5.x; added ePub support; allowed images to be previewed
# --- END PLUGIN HELP ---
-->
<?php
}
?>