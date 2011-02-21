<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:bb_etherpad/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';

t3lib_extMgm::addPlugin(Array('LLL:EXT:bb_etherpad/locallang_tca.php:bb_etherpad', $_EXTKEY.'_pi1'));
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:bb_etherpad/flexform_ds.xml');

#t3lib_extMgm::allowTableOnStandardPages("tx_veguestbook_entries");
#t3lib_extMgm::addToInsertRecords('tx_veguestbook_entries');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_bbetherpad_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_bbetherpad_pi1_wizicon.php';
}
?>