<?php

require_once("$CFG->dirroot/config.php");
global $CFG, $OUTPUT, $FULLME, $PAGE, $COURSE, $DB;

$update = optional_param('update', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$extra = optional_param('extra', null, PARAM_TEXT);
$type = optional_param('type', '', PARAM_ALPHANUM);
$add = optional_param('add', '', PARAM_ALPHA);
$pagetype = optional_param('pagetype', null, PARAM_TEXT);
$onlygeneral = optional_param('onlygeneral', 0, PARAM_TEXT);

$patharray = pathinfo($FULLME);

if (isset($patharray['dirname'])) {
    $basename = basename($patharray['dirname']);
}

if (isset($patharray['filename'])) {
    $filename = $patharray['filename'];
}

$modnamearray = $DB->get_records('modules', null);
$modnamearr = array();

foreach ($modnamearray as $modnamevalue) {
    $modnamearr[] = $modnamevalue->name;
}

$tabs = $row = array();

if (!empty($update) && ($patharray['filename'] === 'modedit') && ($basename === 'course') && !$onlygeneral) {

    $modid = $update;
    $selected = "Settings";

    $cmobject = $DB->get_record('course_modules', array('id' => $update));
    $modname = $DB->get_field('modules', 'name', array('id' => $cmobject->module));

    $modnameexistinmodnamearr = in_array($basename, $modnamearr);
    $cm = get_coursemodule_from_id('', $modid, 0, false, MUST_EXIST);

    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $hascontext = get_context_instance(CONTEXT_MODULE, $cm->id);
    $haseditingcapability = has_capability('moodle/course:manageactivities', $context);

//    require_login($course, false, $cm); // needed to setup proper $COURSE
} elseif (!empty($id) && empty($update) && ($patharray['filename'] === 'view') && in_array($basename, $modnamearr)) {

    $modid = $id;
    $selected = 'Preview';

    $cmobject = $DB->get_record('course_modules', array('id' => $modid));
    $modname = $DB->get_field('modules', 'name', array('id' => $cmobject->module));

    $modnameexistinmodnamearr = in_array($basename, $modnamearr);
    $cm = get_coursemodule_from_id('', $modid, 0, false, MUST_EXIST);

    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $haseditingcapability = has_capability('moodle/course:manageactivities', $context);
} elseif (($patharray['filename'] === 'modedit') && ($basename === 'course') && $onlygeneral) {

    $modid = $update;
    $selected = 'Edit Content';

    $cmobject = $DB->get_record('course_modules', array('id' => $modid));
    $modname = $DB->get_field('modules', 'name', array('id' => $cmobject->module));

    $modnameexistinmodnamearr = in_array($basename, $modnamearr);
    $cm = get_coursemodule_from_id('', $modid, 0, false, MUST_EXIST);

    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $haseditingcapability = has_capability('moodle/course:manageactivities', $context);
} else {
    $modid = "";
    $selected = "";
    $modname = "";
    $modnameexistinmodnamearr = "";
    $haseditingcapability = false;
}

$jsdata = array(
    'modid' => $modid,
    'filename' => $filename,
    'modnamearr' => $modnamearr,
    'basename' => $basename,
    'modname' => $modname,
    'modname_in_modarray' => $modnameexistinmodnamearr,
    'selected' => $selected,
    'add' => $add,
    'capability' => $haseditingcapability,
    'onlygeneral' => $onlygeneral
);
