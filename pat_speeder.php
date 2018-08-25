<?php
/**
 * @name	  pat_speeder
 * @description	  Display page source on one line of code
 * @link 	  http://pat-speeder.cara-tm.com
 * @author	  Patrick LEFEVRE
 * @author_email  <patrick[dot]lefevre[at]gmail[dot]com>
 * @type:         Admin + Public
 * @prefs:        no prefs
 * @order:        5
 * @version:      1.1
 * @license:      GPLv2
 */

/**
 * This plugin tag registry
 *
 */
if (class_exists('\Textpattern\Tag_Registry')) {
	Txp::get('\Textpattern\Tag\Registry')
		->register('pat_speeder');
}


/**
 * This plugin lifecycle
 *
 */
if (txpinterface == 'admin')
{
	register_callback('_pat_speeder_prefs', 'plugin_lifecycle.pat_speeder', 'installed');
	register_callback('_pat_speeder_cleanup', 'plugin_lifecycle.pat_speeder', 'deleted');
}


/**
 * This plugin tag with attributes
 *
 * @param  array    Tag attributes
 * @return boolean  Call for main function
 */
function pat_speeder($atts)
{

	extract(lAtts(array(
		'enable'  => false,
		'gzip'    => get_pref('pat_speeder_gzip'),
		'code'    => get_pref('pat_speeder_tags'),
		'compact' => get_pref('pat_speeder_compact'),
	),$atts));

	if (
		(get_pref('pat_speeder_enable_live_only') and get_pref('production_status') === 'live')
			or
		(get_pref('pat_speeder_enable_live_only') == '0' and
			(get_pref('pat_speeder_enable') or ($enable and get_pref('pat_speeder_enable')))
		)
	) {
		ob_start(function($buffer) use ($gzip, $code, $compact) {
			return _pat_speeder_go($buffer, $gzip, $code, $compact);
		});
	}

}

/**
 * Main function
 * @param string $buffer
 * @return string HTML compressed content
 */

function _pat_speeder_go($buffer, $gzip, $code, $compact)
{
	// Sanitize the list: no spaces
	$codes = preg_replace('/\s*/m', '', $code);
	// ... and no final comma. Convert into a pipes separated list
	$codes = str_replace(',', '|', rtrim($codes, ','));
	// Set the replacement mode
	$compact = ($compact ? '' : ' ');

	// Remove uncessary elements from the source document (especially: from 2 and more spaces between tags). But keep safe excluded tags
	$buffer = preg_replace('/(?imx)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!\/?(?:textarea|'.$codes.')\b))*+)(?:<(?>textarea|'.$codes.')\b| \z))/u', $compact, $buffer);
	// Remove all comments except google ones and IE conditional comments, too
	$buffer = preg_replace('/<!--([^<|\[|>|go{2}gleo]).*?-->/s', '', $buffer);

	// Server side compression if available
	if (get_pref('pat_speeder_gzip') and $gzip) {
		// Check server config
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && false == ini_get('zlib.output_compression')) {
			$encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];
				if(function_exists('gzencode') && preg_match('/gzip/i', $encoding)) {
					header('Content-Encoding: gzip');
					$buffer = gzencode($buffer);
			} elseif (function_exists('gzdeflate') && preg_match('/deflate/i', $encoding)) {
				header('Content-Encoding: deflate');
				$buffer = gzdeflate($buffer);
			}
		}
	}

	// Return the result
	return $buffer;
	// Send the buffer
	ob_end_flush();
	// Empty the buffer
	ob_end_clean();
}


/**
 * Plugin prefs.
 *
 * @param
 * @return Insert this plugin prefs into 'txp_prefs' table.
 */
function _pat_speeder_prefs()
{

	if (!safe_field ('name', 'txp_prefs', "name='pat_speeder_enable'"))
		safe_insert('txp_prefs', "name='pat_speeder_enable', val='0', type=1, event='admin', html='yesnoradio', position=24");

	if (!safe_field ('name', 'txp_prefs', "name='pat_speeder_enable_live_only'"))
		safe_insert('txp_prefs', "name='pat_speeder_enable_live_only', val='1', type=1, event='admin', html='yesnoradio', position=25");

	if (!safe_field ('name', 'txp_prefs', "name='pat_speeder_gzip'"))
		safe_insert('txp_prefs', "name='pat_speeder_gzip', val='0', type=1, event='admin', html='yesnoradio', position=26");

	if (!safe_field ('name', 'txp_prefs', "name='pat_speeder_tags'"))
		safe_insert('txp_prefs', "name='pat_speeder_tags', val='script,svg,pre,code', type=1, event='admin', html='text_input', position=27");

	if (!safe_field ('name', 'txp_prefs', "name='pat_speeder_compact'"))
		safe_insert('txp_prefs', "name='pat_speeder_compact', val='0', type=1, event='admin', html='yesnoradio', position=28");

	safe_repair('txp_prefs');
	safe_repair('txp_plugin');

}


/**
 * Delete plugin prefs & language strings.
 *
 * @param
 * @return Delete this plugin prefs.
 */
function _pat_speeder_cleanup()
{

	$tables = array('pat_speeder_enable', 'pat_speeder_gzip', 'pat_speeder_tags', 'pat_speeder_compact');
	foreach ($tables as $val) {
		safe_delete('txp_prefs', "name='".$val."'");
	}
	safe_delete('txp_lang', "owner='pat_speeder'");

	safe_repair('txp_prefs');
	safe_repair('txp_plugin');

}

?>
