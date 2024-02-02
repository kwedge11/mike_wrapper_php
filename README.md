# mikit_wrapper_php
This is a PHP wrapper around the SEDNA MIKIT API - https://github.com/sednasystems/sedna-mikit-sample-node

The purpose is to simplify creating MIKIT applications.

The MIKIT application will live at the endpoint specified in the Sedna MIKIT application setup. When Sedna opens a MIKIT sidebar, and http call will be made to the application.

The text output from the application will be sent back to the caller.

Note - only MIKIT syntax to be returned (no echoing for debugging, etc).

Example
-------
```php

function hello_world() {

	//To read POST data
	$post_data = file_get_contents('php://input'); //Read the post data
	$post_data = json_decode($post_data,true); //Change to PHP array

	//Create an mikit object with a url for the top right icon
	$url = 'http://app.sednanetwork.com/mikit/2020-07-07/definitions.schema.json';
	$mikit = new mikit_api($url);

	//Create a text display - type header
	$hello_world_text = $mikit->textDisplay('Hello there World','heading');

	//Add to the mikit object
	$mikit->add_to_blocks($hello_world_text);

	//Return the mikit object (json)
	return $mikit->render();
}
```

Output
------
```json
{
   "version":"2020-07-07",
   "trace":"",
   "href":"http:\/\/app.sednanetwork.com\/mikit\/2020-07-07\/definitions.schema.json",
   "blocks":[
      {
         "type":"textDisplay",
         "style":"heading",
         "value":"Hello there World"
      }
   ]
}
```

Functions
---------

    public function isMikit() {
    public function setURL($in_url) {
    public function buttonMenu($in_items,$in_current_selection,$in_style = 'primary', $in_size = 'small', $in_align = 'left') {
    public function footerMenu($in_items, $in_current_selection, $in_style='primary', $in_size = 'small') {
    public function textDisplay($in_value = '', $in_style = 'text') {
    public function textInput($in_id,$in_label,$in_value='',$in_placeholder='',$in_required=false,$in_error='') {
    public function hiddenInput($in_id,$in_value) {
    public function card($in_title,$in_texts,$in_actions) {
    public function footer($in_items) {
    public function alert($in_text,$in_level) {
    public function button($in_id,$in_label,$in_style='primary',$in_size='small',$in_action='submit') {
    public function dropdown($in_id,$in_label,$in_options,$in_value,$in_required=false,$in_searchable=true) {
    public function checkbox($in_id,$in_label,$in_checked = 'false') {
    public function textArea($in_id,$in_label,$in_value,$in_rows) {
    public function calendar($in_id,$in_label,$in_value) {
    public function link($in_label,$in_url,$in_style = 'primary',$in_size = 'small') {
    public function imageDisplay($in_url,$in_description = '') {
    public function attachmentInsert($in_label,$in_url,$in_filename) {
    public function textInsert($in_label,$in_content) {
    public function textInsertByPath($in_label,$in_path) {
    public function row($in_items,$in_align = 'left') {
    public function set_blocks($in_item) {
    public function add_to_blocks($in_item) {
    public function render($in_debug = false) {
    public function get_inputs($in_data) {
    public function get_value_from_object($in_data) {
    public function error($in_function,$in_error) {
    public function blocksToErrorLog() {
