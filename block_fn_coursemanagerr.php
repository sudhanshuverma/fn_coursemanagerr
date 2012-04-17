<?PHP

//$Id: block_admin.php,v 1.2 2004/04/26 09:03:40 defacer Exp 

class block_fn_coursemanagerr extends block_base {

    function init() {
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', $this->blockname);
    }

    function instance_allow_multiple() {
        return false;
    }

    function applicable_formats() {
        return array('all' => false,
            'course-view-weeks' => true,
            'course-view-fntabs' => true,
            'course-view-topics' => true,
            'mod' => true);
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        $isediting = $this->page->user_is_editing();
       
        // when user is editing then show the blocks
        if (!$isediting) {
            return NULL;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }
       
        $this->course = $this->page->course;
        $applicableformat = array('weeks', 'topics', 'fntabs');

        if (!in_array($this->course->format, $applicableformat)) {
            return NULL;
        }
        

        if (file_exists("$CFG->dirroot/blocks/fn_coursemanagerr/tab.php")) {

            // show tabs on edit page
            require("$CFG->dirroot/blocks/fn_coursemanagerr/tab.php");
            $module = array('name' => 'block_fncoursemanagers_maketabs', 'fullpath' => '/blocks/fn_coursemanagerr/maketab.js', 'requires' => array('core_dock', 'io', 'node', 'dom', 'event-custom', 'event'));
            $this->page->requires->js_init_call('M.block_fncoursemanagers_maketabs.create_tab', $jsdata, true, $module);

            //hide all section of setting except general section  when edit content tab is selected
            $jsmodule = array(
                'name' => 'M.block_fncoursemanagers_Show_Edit_Content',
                'fullpath' => '/blocks/fn_coursemanager/show_only_general_section.js',
                'requires' => array('base', 'node')
            );
            $jsdata1 = array(
                'show' => $jsdata['onlygeneral']
            );
            $this->page->requires->js_init_call('M.block_fncoursemanagers_Show_Edit_Content.init', $jsdata1, true, $jsmodule);
        }

        $this->content = new stdClass();
        $this->content->text = $this->build_activity_tree(); //$this->build_menu();
        $this->content->footer = '';

        return $this->content;
    }

    function build_activity_tree() {
        global $CFG, $COURSE, $SESSION, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        $default_tab = optional_param('setdefaulttab', '0', PARAM_TEXT);
        if ($default_tab) {
            $SESSION->DEFAULT_TAB[$COURSE->id] = $default_tab;
        }


        $jssrc = "$CFG->wwwroot/blocks/fn_coursemanagerr/dtree.js";
        $csshref = "$CFG->wwwroot/blocks/fn_coursemanagerr/dtree.css";
        $blockimgdirurl = "$CFG->wwwroot/blocks/fn_coursemanagerr/images/";
        $blockimgdirurl = "'" . $blockimgdirurl . "'";

        ob_start();
        $output = "";
        $output .="<link rel='StyleSheet' href=$csshref type='text/css' />";
        $output .= "<script type='text/javascript' src=$jssrc></script>";
        $output .= "<script type='text/javascript'>//<![CDATA[    
                d = new dTree('d');
                var albumTree_images = $blockimgdirurl;               
                d.icon = {
                    root            : albumTree_images + 'memon.gif',
                    folder          : albumTree_images + 'folder.gif',
                    folderOpen      : albumTree_images + 'imgfolder.gif',
                    node            : albumTree_images + 'imgfolder.gif',
                    empty           : albumTree_images + 'empty.gif',
                    line            : albumTree_images + 'line.gif',
                    join            : albumTree_images + 'join.gif',
                    joinBottom      : albumTree_images + 'joinbottom.gif',
                    plus            : albumTree_images + 'plus.gif',
                    plusBottom      : albumTree_images + 'plusbottom.gif',
                    minus           : albumTree_images + 'minus.gif',
                    minusBottom     : albumTree_images + 'minusbottom.gif',
                    nlPlus          : albumTree_images + 'nolines_plus.gif',
                    nlMinus         : albumTree_images + 'nolines_minus.gif'
                };
                d.config.useLines = true;
                d.config.useIcons = true;
                d.config.useCookies = true;
                d.config.closeSameLevel = false;
                ";

        if (($COURSE->format == 'topics') || ($COURSE->format == 'weeks') || ($COURSE->format == 'fntabs')) {
            if ($COURSE->format == 'weeks') {
                $viewalltext = get_string('allweeks', $this->blockname);
                $courseFormat = 'week';
            } else if ($COURSE->format == 'topics') {
                $viewalltext = get_string('alltopics', $this->blockname);
                $courseFormat = 'topic';
            } else if ($COURSE->format == 'fntabs') {
                $viewalltext = get_string('allfntabs', $this->blockname);
                $courseFormat = 'selected_week';
            } else {
                $viewalltext = '';
                $courseFormat = '';
            }
            $allweekurl = "$CFG->wwwroot/course/view.php?id={$COURSE->id}&$courseFormat=all";
            $nodename = $viewalltext;
            $url = $allweekurl;
            $icon = "$CFG->wwwroot/blocks/fn_coursemanagerr/pix/memon.gif";
            $output .= "
            var topnodeid = 'course'+$COURSE->id;
            d.add(topnodeid, -1, '$nodename', '$url','$nodename' ,'','$icon');";

            $genericName = get_string("name" . $COURSE->format, $this->blockname);
            $allSections = get_all_sections($COURSE->id);
            $modinfo = get_fast_modinfo($COURSE);

            if ($allSections) {
                foreach ($allSections as $k => $section) {
                    if ($k == 0) {
                        continue;
                    }

                    if ($k <= $COURSE->numsections) {
                        $weekcss = $section->visible ? '' : 'dimmed';
                        if (!empty($section)) {
                            if (!empty($section->name)) {
                                $strsummary = trim($section->name);
                                $strsummary = $this->fn_truncate_description($strsummary, 12);
                            } else {
                                if ($COURSE->format == 'fntabs') {
                                    $fnsectionname = $DB->get_field('course_config_fn', 'value', array('courseid' => $COURSE->id, 'variable' => 'topicheading'));
                                    if ($fnsectionname) {
                                        $strsummary = ucwords($fnsectionname) . " " . $k; // name that is set in the database
                                    } else {
                                        $strsummary = ucwords(get_string('defaulttopicheading', 'block_fn_coursemanager')) . " " . $k; // name that is set in the database
                                    }
                                } else {
                                    $strsummary = ucwords($genericName) . " " . $k; // just a default name
                                }
                            }

                            $sectionname = "$strsummary";
                            $sectionurl = "$CFG->wwwroot/course/view.php?id={$COURSE->id}&$courseFormat=$k" . '" title="' . $courseFormat . $k . '" alt="' . $courseFormat . $k . '" class="' . $weekcss;
                            $count = 0;
                            $output .= "
                            var sectionnodeid = 'section'+$k;
                            d.add(sectionnodeid, topnodeid, '$sectionname', '$sectionurl','$sectionname', '', '', '', '', '$weekcss');";

                            $count = 0;
                            if (!empty($modinfo->sections[$k])) {
                                foreach ($modinfo->sections[$k] as $cmid) {
                                    $cm = $modinfo->cms[$cmid];
                                    if (!$cm->uservisible) {
                                        continue;
                                    }
                                    list($content, $instancename) = get_print_section_cm_text($cm, $COURSE);
                                    if (($cm->modname != 'label')) {
                                        $count++;
                                        if (!($url = $cm->get_url())) {
                                            $linkcss = $cm->visible ? '' : 'dimmed';                                           
                                            $icon = $cm->modname . ".gif";
                                            $output .= "
                                                var iconname = '$icon';
                                                var activitynodeid = 'activity'+$cm->id;
                                                d.add(activitynodeid, sectionnodeid, '$content', '','' ,'','$icon');";
                                        } else {
                                            $linkcss = $cm->visible ? '' : 'dimmed';
                                            $sesskey = sesskey();
                                            if (isset($SESSION->DEFAULT_TAB[$COURSE->id])) {
                                                if ($SESSION->DEFAULT_TAB[$COURSE->id] == 'preview') {
                                                    $modurl = $url;
                                                } else if ($SESSION->DEFAULT_TAB[$COURSE->id] == 'settings') {
                                                    $modurl = "$CFG->wwwroot/course/mod.php?update=$cm->id&sesskey=$sesskey";
                                                } else if ($SESSION->DEFAULT_TAB[$COURSE->id] == 'editcontent') {
                                                    $modurl = "$CFG->wwwroot/course/modedit.php?update=$cm->id&return=0&onlygeneral=show";
                                                } else {
                                                    $modurl = "";
                                                }
                                            } else {
                                                $modurl = "$CFG->wwwroot/course/mod.php?update=$cm->id&sesskey=$sesskey";
                                            }
                                            $modurl = $modurl;
                                            $truncated_name = $this->fn_truncate_description($instancename, 12);
                                            $title = htmlspecialchars($instancename, ENT_QUOTES);
                                            $icon = $cm->modname . ".gif";
                                            $output .= "
                                                var iconname = '$icon'; 
                                                var titlename = '$title';
                                                var activitynodeid = 'activity'+$cm->id;
                                                var activityinstancename = '$truncated_name';
                                                var activityurl =  '$modurl';
                                                var cls = '$linkcss';
                                                d.add(activitynodeid, sectionnodeid, activityinstancename, activityurl, titlename ,'', albumTree_images + iconname, '', '', cls);";
                                        }
                                    }
                                }
                                if ($count < 1) {
                                    $text = 'No activities';
                                    $htmlid = 'fn_coursemanagerr_' . uniqid();
                                    $output .= "
                                               var iconname = ''; 
                                                var titlename = '$text';
                                                var activitynodeid = '$htmlid';
                                                var activityinstancename = '$text';
                                                var activityurl =  '$modurl';
                                                d.add(activitynodeid, sectionnodeid, activityinstancename, '', titlename ,'', albumTree_images + iconname);";
                                }
                            } else {
                                $text = 'No activities';                                
                                $htmlid = 'fn_coursemanagerr_' . uniqid();
                                    $output .= "                                              
                                                var iconname = ''; 
                                                var titlename = '$text';
                                                var activitynodeid = '$htmlid';
                                                var activityinstancename = '$text';
                                                var activityurl =  '$modurl';
                                                d.add(activitynodeid, sectionnodeid, activityinstancename, '', titlename ,'', albumTree_images + iconname);";
                            }
                        }
                    }
                }
            }
        }

        $output .= " 
              document.write(d);
                    //]]>
                    </script>";

        $output .= ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function fn_truncate_description($string, $max_size=20, $trunc = '...') {
        $split_tags = array('<br>', '<BR>', '<Br>', '<bR>', '</dt>', '</dT>', '</Dt>', '</DT>', '</p>', '</P>', '<BR />', '<br />', '<bR />', '<Br />');
        $temp = $string;

        foreach ($split_tags as $tag) {
            list($temp) = explode($tag, $temp, 2);
        }
        $rstring = strip_tags($temp);

        $rstring = html_entity_decode($rstring);

        if (strlen($rstring) > $max_size) {
            $rstring = chunk_split($rstring, ($max_size - strlen($trunc)), "\n");
            $temp = explode("\n", $rstring);
            // catches new lines at the beginning
            if (trim($temp[0]) != '') {
                $rstring = trim($temp[0]) . $trunc;
            } else {
                $rstring = trim($temp[1]) . $trunc;
            }
        }
        if (strlen($rstring) > $max_size) {
            $rstring = substr($rstring, 0, ($max_size - strlen($trunc))) . $trunc;
        } elseif ($rstring == '') {
            // we chopped everything off... lets fall back to a failsafe but harsher truncation
            $rstring = substr(trim(strip_tags($string)), 0, ($max_size - strlen($trunc))) . $trunc;
        }

        // single quotes need escaping
        return str_replace("'", "\\'", $rstring);
    }

}

?>