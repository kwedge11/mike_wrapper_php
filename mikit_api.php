<?php
class mikit_api {
    //Ref - https://app.sednanetwork.com/mikit/2020-07-07/definitions.schema.json

    private const VERSION = '2020-07-07'; //MIKIT version
    private $mikit; //Mikit object to build and return

    function __construct($in_href = '', $in_trace = '') {
        //in_url - an optional url pointing to the third-party application - with http://
        //in_trace - a unique id for this particular request

        if ($in_href == '') $in_href = 'null';

        $this->mikit = array(
            'version' => $this::VERSION,
            'trace' => $in_trace,
            'href' => $in_href,
            'blocks' => array()
        );
        
        return $this->mikit;
    }

	//Check headers to see if the call is coming from MIKIT
	public function isMikit() {
		$apache_headers = apache_request_headers();
		$user_agent = $apache_headers['User-Agent'];
	
		if ($user_agent == 'SednaApplication (messageInteractionClient)') return true;
		return false;
	}

    public function setURL($in_url) {
        $this->mikit['href'] = $in_url;
    }

    //Button menu
    public function buttonMenu($in_items,$in_current_selection,$in_style = 'primary', $in_size = 'small', $in_align = 'left') {
        /*
            $in_items is an array of button_id's and labels.

            E.g. $in_items = array(
                            array('id'=>'button_1','label' = 'Button 1'),
                            array('id'=>'button_2','label' = 'Button2'),
                            etc.
                        )
            
            The buttons will be created in rows of max three buttons per row.

            $in_current_selection will be matched against the id's. The match will be excluded so
            as not to show the menu option currently selected.

            All will be submit buttons.
            
            Returns as an array of row objects. Calling routine expected to break out array.
        */

        //Define the inverted button style.
        switch(strtolower($in_style)) {
            case 'primary': $inverted_style = 'secondary'; break;
            case 'secondary': $inverted_style = 'primary'; break;
            default: $inverted_style = $in_style; //Covers link style
        }

        $b = array();
        foreach($in_items as $ix=>$item) {

            //Set the first button to selected if none defined
            if ($ix == 0 and $in_current_selection == '') $in_current_selection = $item;

            //If style is primary or secondary (not link) use that style to indicate current option
            //and the opposite style for the others not selected.
            if ($item == $in_current_selection) {
                $this_style = $in_style;
            } else {
                $this_style = $inverted_style;
            }

            //Add button
            $b[] = $this->button($item,$item,$this_style,$in_size,'reset');

            //Break at 3 buttons which is max
            if (count($b) == 3) {
                $r[] = $this->row($b,$in_align); //Add row to rows array
                $b = array(); //Reset buttons array for next row
            }
        }

        //If any items left make last row.
        if (count($b)) $r[] = $this->row($b,$in_align);
        return $r;
    }

    //Footer menu
    public function footerMenu($in_items, $in_current_selection, $in_style='primary', $in_size = 'small') {
        /*
        Just a wrapper around footer creating a simpler call.

        $in_items is array of button names to put in the footer. Max 3.

        The id will be the same as the name so keep them unique in the application.

        Returns a footer object in array.
        */

        //TODO - use the inverted style instead? Note only 3 options allowed (but check that)
        //Don't show the current selection in the menu
        if ($in_current_selection) {
            if (($key = array_search($in_current_selection,$in_items)) !== false) {
                unset($in_items[$key]);
            }
        }

        //Only three items allowed.
        if (count($in_items) > 3) {
            $this->error('footerMenu','max number of items is 3.');
            return array();
        }

        //Create the buttons
        foreach($in_items as $ix=>$item) {
            $b[] = $this->button($item,$item,$in_style,$in_size,'submit');
        }

        //Return the footer object.
        return $this->footer($b);
    }

    //Text display
    public function textDisplay($in_value = '', $in_style = 'text') {
     
        $styles = array('label','text','subtext','title','heading','subheading');

        $err = "";
        $in_style = strtolower($in_style);
        if (!in_array($in_style,$styles)) $err = "in_style must be in " . implode(',',$styles);
        if ($err) {
            $this->error('textDisplay',$err);
            return;
        }

        $m = array(
            'type' => 'textDisplay',
            'style' => $in_style,
            'value' => $in_value
        );
        return $m;
    }

    //Text input
    public function textInput($in_id,$in_label,$in_value='',$in_placeholder='',$in_required=false,$in_error='') {

        $m = array(
            'type' => 'textInput',
            'id' => $in_id,
            'label' => $in_label,
            'value' => $in_value,
            'placeholder' => $in_placeholder,
            'error' => $in_error
        );
        return $m;
    }

    //Hidden input
    public function hiddenInput($in_id,$in_value) {

        $m = array(
            'type' => 'hiddenInput',
            'value' => $in_value,
            'id' => $in_id
        );
        return $m;
    }

    //Card
    public function card($in_title,$in_texts,$in_actions) {
        /*
            $in_texts - array of textDisplay objects
            $in_actions - array of buttons or links - max 3
        */

        $m = array(
            'type' => 'card',
            'title' => $in_title,
            'blocks' => $in_texts,
            'actions' => $in_actions
        );
        return $m;
    }

    //Footer
    public function footer($in_items) {
        //Can be used as a bottom menu
        //$in_items array of up to 3 buttons and links

        $m = array(
            'type' => 'footer',
            'blocks' => $in_items
        );
        return $m;
    }

    //Alert
    public function alert($in_text,$in_level) {

        $levels = array('info','error');

        $err = "";
        if (!in_array($in_level,$levels)) $err = "in_level must be in " . implode(',',$levels);
        if ($err) {
            $this->error('alert',$err);
        }
        
        $m = array(
            'type' => 'alert',
            'level' => $in_level,
            'text' => $in_text
        );
        return $m;
    }

    //Button
    public function button($in_id,$in_label,$in_style='primary',$in_size='small',$in_action='submit') {

        $styles = array('primary','secondary','link');
        $sizes = array('small','medium');
        $actions = array('submit','reset','cancel');

        $err = "";
        if (!in_array($in_style,$styles)) $err = "in_style must be in " . implode(',',$styles);
        if (!in_array($in_size,$sizes)) $err = "in_size must be in " . implode(',',$sizes);
        if (!in_array($in_action,$actions)) $err = "in_action must in in " - implode(',',$actions);
        if ($err) {
            $this->error('button',$err);
            return "";
        }

        $m = array(
            'type'=>'button',
            'id'=>$in_id,
            'label'=>$in_label,
            'style'=>$in_style,
            'size'=>$in_size,
            'acion'=>$in_action
        );
        return $m;
    }

    //Dropdown
    public function dropdown($in_id,$in_label,$in_options,$in_value,$in_required=false,$in_searchable=true) {

        /*
            $in_options array like:
                array(
                    array('id'=>'id1','label'=>'label1'),
                    array('id'=>'id2','label'=>'label2'),
                    etc.
                )
        */

        $m = array(
            'type' => 'dropdown',
            'id' => $in_id,
            'label' => $in_label,
            'options' => $in_options,
            'value' => $in_value,
            'required' => $in_required,
            'searchable' => $in_searchable
        );
        return $m;
    }

    //Checkbox
    public function checkbox($in_id,$in_label,$in_checked = 'false') {

        //$in_checked - true or false as string

        $m = array(
            'type' => 'checkbox',
            'id' => $in_id,
            'label' => $in_label,
            'checked' => $in_checked
        );
        return $m;
    }

    //Text area
    public function textArea($in_id,$in_label,$in_value,$in_rows) {

        //Note - rows currently ignored - makes this kind of useless.

        $m = array(
            'type' => 'textArea',
            'id' => $in_id,
            'label' => $in_label,
            'value' => $in_value,
            'rows' => $in_rows
        );
        return $m;
    }

    //Calendar
    public function calendar($in_id,$in_label,$in_value) {

        $m = array(
            'type' => 'calendar',
            'id' => $in_id,
            'label' => $in_label,
            'value' => $in_value
        );
        return $m;
    }

    //Link
    public function link($in_label,$in_url,$in_style = 'primary',$in_size = 'small') {

        $styles = array('primary','secondary','link');
        $sizes = array('small','medium');

        $err = "";
        if (!in_array($in_style,$styles)) $err = "in_style must be in " . implode(',',$styles);
        if (!in_array($in_size,$sizes)) $err = "in_size must be in " . implode(',',$sizes);
        if ($err) {
            $this->error('button',$err);
            return "";
        }

        $m = array(
            'type' => 'link',
            'label' => $in_label,
            'href' => $in_url,
            'style' => $in_style,
            'size' => $in_size
        );
        return $m;
    }

    //Image
    public function imageDisplay($in_url,$in_description = '') {
        $m = array(
            'type' => 'imageDisplay',
            'href' => $in_url,
            'description' => $in_description
        );
        return $m;
    }

    //Attachment insert
    public function attachmentInsert($in_label,$in_url,$in_filename) {
        //$in_filename should be the of the url

        $m = array(
            'type' => 'attachmentInsert',
            'label' => $in_label,
            'href' => $in_url,
            'filename' => $in_filename
        );
        return $m;
    }

    //Text insert
    public function textInsert($in_label,$in_content) {
        //$in_content can contain html - e.g. <pre>text</pre>

        $m = array(
            'type' => 'textInsert',
            'label' => $in_label,
            'content' => $in_content
        );
        return $m;
    }

    //Text insert - by path
    public function textInsertByPath($in_label,$in_path) {
        //For calling in content from other scripts
        //$in_path - e.g./insert_scripts/insert_vessel_description.php

        $m = array(
            'type' => 'textInsertByPath',
            'label' => $in_label,
            'path' => $in_path
        );
        return $m;
    }

    //Rows
    public function row($in_items,$in_align = 'left') {

        //in_items can contain up to 3 of - button, link, textDisplay

        $aligns = array('left','center','right','align');

        $err = "";
        if (!in_array($in_align,$aligns)) $err = "in_align must be in " . implode(',',$aligns);
        if (!is_array($in_items)) $err = "in_items must be array";
        if (is_array($in_items) and count($in_items) == 0) $err = "in_items is empty";

        if ($err) {
            $this->error('row',$err);
            return "";
        }

        $m = array(
            'type' => 'row',
            'align' => $in_align,
            'blocks' => $in_items
        );
        return $m;
    }

    //Set blocks
    public function set_blocks($in_item) {
        unset($this->mikit['blocks']);

        $this->add_to_blocks($in_item);
    }

    //Add an object to the end of the blocks section
    public function add_to_blocks($in_item) {
        //$in_item can an array for a single object or array of objects
		//Note - this fails if adding a dropdown as a single item. Need to fix TODO
		//Reason is that objects like dropdown are nexted arrays.
		//for time being only add arrays of obects 

        //Don't add if $in_item is empty.
        if (!$in_item or empty($in_item)) {
            $this->error('add_to_blocks','skipping adding empty in_item');
            return;
        }

        //If single item in array
        if (count($in_item) == count($in_item, COUNT_RECURSIVE)) {
            $this->mikit['blocks'][] = $in_item;
            return;
        }

        //Assume multiple items in array if get to here. Add all.
        foreach ($in_item as $key => $item) {
            $this->mikit['blocks'][] = $item;
        }
    }

    //Return json_encoded mikit object
    public function render($in_debug = false) {
        if ($in_debug) error_log(json_encode($this->mikit,JSON_PRETTY_PRINT));
        return json_encode($this->mikit,JSON_PRETTY_PRINT);
    }

    //Get updated objects in posted data and return as array keyed by id.
	//Loop $in_data['blocks'] recursively looking for components with values.
    //Assume max two levels which is current case in MIKIT.
    public function get_inputs($in_data) {
        //In data is the whole object posted by MIKIT.

        $inputs = array();
        foreach($in_data['blocks'] as $key1=>$data1) {

            if (isset($data1['blocks'])) {
                //This applies to row, card, and footer - sub-array
                foreach($data1['blocks'] as $key2=>$data2) {
                    $id = $data2['id'] ?? '';
                    $value = $this->get_value_from_object($data2);
                    if ($value) $inputs[$id] = $value;
                }
            } else {
                //Everything else at top level
                $id = $data1['id'] ?? '';
                if ($id) {
                    $value = $this->get_value_from_object($data1);
                    #if ($value) $inputs[$id] = $value; //Replaced with below because get no array item if field value = 0 (e.g. freight rate)
                    $inputs[$id] = $value;
                }
            }
        }
        #if (isset($inputs)) error_log(print_r($inputs,true));

        return $inputs;
    }

    //Get value from components used as input.
    public function get_value_from_object($in_data) {

        $type = $in_data['type']; //Component type

        $value = '';
        switch($type) {
            case 'textInput':
            case 'dropdown':
            case 'hiddenInput':
            case 'calendar':
                $value = $in_data['value'] ?? '';
                break;
            case 'checkbox':
                $value = $in_data['checked'] ?? '';
                $value = ($value) ? "true" : "false";
                break;
            case 'textArea':
                $value = $in_data['value'] ?? '';
                #$value = str_replace("\n","\\n",$value);
                break;
            default:
                $value = '';
        }
        return $value;
    }

    public function error($in_function,$in_error) {
        error_log("mikit_api->$in_function - $in_error");
        $b[] = $this->alert("Error - $in_function - $in_error.",'error');
        $this->set_blocks($b);
        echo $this->render();
    }

    public function blocksToErrorLog() {
        error_log(print_r($this->mikit,true));
    }

}
?>