<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_wikiatt.php 61885 2017-03-27 00:54:32Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$wikilib = TikiLib::lib('wiki');
$auto_query_args = array('sort_mode', 'page');

$find = '';
$offset = 0;
$sort_mode = 'created_desc';

if ($access->ticketMatch()) {
	if (isset($_REQUEST['action']) and isset($_REQUEST['attId'])) {
		$item = $wikilib->get_item_attachment($_REQUEST['attId']);
		if ($_REQUEST['action'] == 'move2db') {
			$wikilib->file_to_db($prefs['w_use_dir'] . $item['path'], $_REQUEST['attId']);
		} elseif ($_REQUEST['action'] == 'move2file') {
			$wikilib->db_to_file($item['filename'], $_REQUEST['attId']);
		}
	}

	if (isset($_REQUEST['all2db'])) {
		$attachements = $wikilib->list_all_attachements();
		for ($i = 0; $i < $attachements['cant']; $i++) {
			if ($attachements['data'][$i]['path']) {
				$wikilib->file_to_db($prefs['w_use_dir'] . $attachements['data'][$i]['path'], $attachements['data'][$i]['attId']);
			}
		}
	} elseif (isset($_REQUEST['all2file'])) {
		$attachements = $wikilib->list_all_attachements();
		for ($i = 0; $i < $attachements['cant']; $i++) {
			if (!$attachements['data'][$i]['path']) {
				$wikilib->db_to_file($attachements['data'][$i]['filename'], $attachements['data'][$i]['attId']);
			}
		}
	}

	if (isset($_REQUEST['find'])) {
		$find = $_REQUEST['find'];
	}
	if (isset($_REQUEST['offset'])) {
		$offset = $_REQUEST['offset'];
	}
	if (isset($_REQUEST['sort_mode'])) {
		$sort_mode = $_REQUEST['sort_mode'];
	}
}

$smarty->assign_by_ref('find', $find);
$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$attachements = $wikilib->list_all_attachements($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $attachements['cant']);
$smarty->assign_by_ref('attachements', $attachements['data']);
$urlquery['find'] = $find;
$urlquery['page'] = 'wikiatt';
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
