<?php
/**
 * Plugin Name: QuizExt
 * Plugin URI: http://portfolio.appcloud.su/
 * Description: Quiz extensions - provide additional types of questions (including student code 
 * compilation/running/result verification), group/course reports and WYSIWYG possibility for lesson editing
 * Version: 1.0.12
 * Author: SuperAppps
 * Author URI: http://portfolio.appcloud.su/
 * Text Domain: qe-quizext
 * Domain Path: /lang/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.0
 * Tested up to: 4.7.2
 *
 * @package     QuizExt
 * @category 	Core
 * @author 	SuperAppps
 */

/*  Copyright 2017  Alexey Utkin  (email: superappps@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Restrict direct access
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }


/*
  0. INCLUDES

  1. HOOKS

  2. SHORTCODES

  3. FILTERS

  4. EXTERNAL SCRIPTS

  5. ACTIONS

  6. HELPERS

  7. CUSTOM POST TYPES

  8. ADMIN PAGES

  9. SETTINGS
*/



/*  0. INCLUDES  */

// Not used in the current version
// function qe_includes () 
// {
  //  include_once( 'includes/qe-classes.php');        
  //  include_once( 'includes/qe-test-functions.php');        
// }
// qe_includes();



/*  1. HOOKS  */

// BuddyPress Groups - hide public type of groups
add_filter('groups_allowed_status', 'qe_groups_allowed_status_ex');
function qe_groups_allowed_status_ex($status)
{
    if ($key = array_search('public', $status)) {
        unset($status[$key]);
    }
    return $status;
}

// Add vertical table headers (convinient group report view)
function qe_custom_reporting_style() {
  echo '<style>
.qe_vertical_th:not(.id):not(.name):not(.registered):not(.progress):not(.grade):not(.updated):not(.status):not(.completed):not(.quiz):not(.points):not(.question):not(.selected):not(.correct):not(.enrolled):not(.image):not(.earned):not(.related):not(.last_lesson):not(.trigger):not(.actions):not(.progress):not(.grade):not(.enrollments):not(.completions):not(.certificates):not(.achievements):not(.memberships):not(.notification):not(.configure)
{
	height: 250px;
	white-space: nowrap;
}

th:not(.id):not(.name):not(.registered):not(.progress):not(.grade):not(.updated):not(.status):not(.completed):not(.quiz):not(.points):not(.question):not(.selected):not(.correct):not(.enrolled):not(.image):not(.earned):not(.related):not(.last_lesson):not(.trigger):not(.actions):not(.progress):not(.grade):not(.enrollments):not(.completions):not(.certificates):not(.achievements):not(.memberships):not(.notification):not(.configure) > .qe_vertical_div
{
transform: 
	/* Magic Numbers  translate(25px, 115px) */
    translate(41px, 105px)
    /* 45 is really 360 - 45 rotate(315deg);*/
    rotate(330deg);
	width: 45px;
    } 
.qe_vertical_span {
  border-bottom: 1px solid #ccc;
  padding: 7px 0px !important;
}
.llms-table {
    border: 1px solid #888;
/* border-collapse: separate; */
}
.llms-table td, .llms-table th:not(.qe_vertical_th) {
    border-right: 1px solid #cecece;
}

.llms-table.zebra tbody tr:nth-child(even) td, .llms-table.zebra tbody tr:nth-child(even) th {
    background-color: #f6f6ff;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(204, 239, 255, 0.83);
    border: 1px solid #0089d3;
}
  </style>';
}
add_action('admin_head', 'qe_custom_reporting_style');


// Load Font Awesome
// Not used in curret version, dedicated plugin used instead
// function qe_admin_enqueue_awesome() {
//    wp_enqueue_style( 'prefix-font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css', array(), '4.0.3' );
//}
//add_action( 'admin_enqueue_scripts', 'qe_admin_enqueue_awesome' );


// Use relative URLs for images in editor
// (Correct default behaviour with absolute paths)
function qe_switch_to_relative_url($html, $id, $caption, $title, $align, $url, $size, $alt)
{
	$imageurl = wp_get_attachment_image_src($id, $size);
	$relativeurl = wp_make_link_relative($imageurl[0]);   
	$html = str_replace($imageurl[0], $relativeurl, $html);
      
	return $html;
}
add_filter('image_send_to_editor', 'qe_switch_to_relative_url', 10, 8);


// Customize TinyMCE - add more buttons for WYSIWYG lesson editing mode
function my_mce_buttons_2( $buttons ) {	
	// Add in a core button that's disabled by default
	$buttons[] = 'superscript';
	$buttons[] = 'subscript';
	$buttons[] = 'backcolor';
	$buttons[] = 'underline';
	$buttons[] = 'copy';
	$buttons[] = 'paste';
	$buttons[] = 'cut';
	$buttons[] = 'fontselect';
	$buttons[] = 'fontsizeselect';
  
	return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );


// Add our own actions to TinyMCE (specific buttons for quick actions)
function qe_tinymce_button() {
  if ( current_user_can( 'edit_posts' ) 
  		// && current_user_can( 'edit_pages' )
		) {
          add_filter( 'mce_buttons', 'qe_register_tinymce_button' );
          add_filter( 'mce_external_plugins', 'qe_add_tinymce_button' );
     }
}
add_action( 'admin_head', 'qe_tinymce_button' );

function qe_register_tinymce_button( $buttons ) {
     array_push( $buttons, "qe_button_code", "qe_button_tab", "qe_button_example", "qe_button_info", 
				"qe_button_attention", "qe_button_definition" );
     return $buttons;
}

function qe_add_tinymce_button( $plugin_array ) {
     $plugin_array['qe_button_script'] = plugins_url('/js/qe-editor-buttons.js', __FILE__);
     return $plugin_array;
}


// Provide missing styles in admin mode (WYSIWYG for lessons editing in visual mode)
function qe_editor_css() {
  //  wp_enqueue_style('qe_editor', plugins_url('/css/qe-editor-style.css', __FILE__));
  add_editor_style (plugins_url('/css/qe-editor-style.css', __FILE__));
}
add_action( 'admin_init', 'qe_editor_css' );


// Register custom admin columns headers
// Not used in the current version
// add_filter ('manage_edit-qe_question_columns', 'qe_question_column_headers');

// Register custom admin columns data
// Not used in the current version
// add_filter ('manage_qe_question_posts_custom_column', 'qe_question_column_data', 1, 2);


// Add handlers for code checkers
add_action ('wp_ajax_nopriv_qe_run_code', 'qe_run_code');
add_action ('wp_ajax_qe_run_code', 'qe_run_code');


// Add public scripts
add_action('wp_enqueue_scripts', 'qe_public_scripts');


// Add admin scripts
add_action('admin_enqueue_scripts', 'qe_admin_scripts');


// For quick and dirty debug
// Not used in the current version
function qe_modify_post_content( $content )
{
  $extra1 = '<p>QE-QE-Begin</p>';
  $extra2 = '<p>QE-QE-End</p>';
  $new_content = $extra1 . $content . $extra2;
  return $new_content;
}
//add_filter('the_content', 'qe_modify_post_content');


/**
 * Allow svg-files in media library
 * Not used in the current version
 */
function qe_cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
// add_filter('upload_mimes', 'qu_cc_mime_types');


/**
 * Allow WP users with the "qe_edit_courses" capability to access the main LifterLMS menu
 * @param  string $capability   default capability for access ("manage_options")
 * @return string               modified capability for access
 */
function my_llms_menu_access($capability ) {
	// you can define any valid WordPress capability here
	// see http://codex.wordpress.org/Roles_and_Capabilities#Capabilities for a comprehensive list of capabilties
	$capability = 'qe_edit_courses'; 
	return $capability;
  
}
add_filter( 'lifterlms_admin_menu_access', 'my_llms_menu_access', 10, 1 ); // display the main menu!


/**
 * Allow WP users with the "qe_edit_courses" capability to access the custom post types
 * @param  string $capability   default capability for access ("manage_options")
 * @return string               modified capability for access
 */
function my_llms_custom_post_type_access( $capability ) {
	// you can define any valid WordPress capability here
	// see http://codex.wordpress.org/Roles_and_Capabilities#Capabilities for a comprehensive list of capabilties
	$capability = 'qe_edit_courses'; 
	return $capability;
}
/*
The following post types can be managed with this filter:

Achievements (lifterlms_admin_achievements_access)
Certificates (lifterlms_admin_certificates_access)
Coupons (lifterlms_admin_coupons_access)
Courses (lifterlms_admin_courses_access)
Emails (lifterlms_admin_emails_accesss)
Engagements (lifterlms_admin_engagements_access)
Memberships (lifterlms_admin_membership_access)
Order (lifterlms_admin_order_access)
Reviews (lifterlms_admin_reviews_access)
Vouchers (lifterlms_admin_vouchers_access)
*/
add_filter( 'lifterlms_admin_achievements_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_certificates_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_coupons_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_courses_access', 'my_llms_custom_post_type_access', 10, 1 ); // display the {$post_type} screen
add_filter( 'lifterlms_admin_emails_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_engagements_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_membership_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_order_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_reviews_access', 'my_llms_custom_post_type_access', 10, 1 );
add_filter( 'lifterlms_admin_vouchers_access', 'my_llms_custom_post_type_access', 10, 1 );



/**
 * Allow WP users with the "qe_edit_courses" capability to access the various LifterLMS settings pages
 * @param  string $capability   default capability for access ("manage_options")
 * @return string               modified capability for access
 */
function my_llms_settings_access( $capability ) {
	// you can define any valid WordPress capability here
	// see http://codex.wordpress.org/Roles_and_Capabilities#Capabilities for a comprehensive list of capabilties
	$capability = 'qe_edit_courses'; 
	return $capability;
}
/*
The following settings screens can be managed with this filter:

General Settings (lifterlms_admin_settings_access)
Reporting (lifterlms_admin_reporting_access)
System Report (lifterlms_admin_system_report_access)
Import / Export (lifterlms_admin_import_access)
*/
add_filter( 'lifterlms_admin_settings_access', 'my_llms_settings_access', 10, 1 );
add_filter( 'lifterlms_admin_reporting_access', 'my_llms_settings_access', 10, 1 );



/* 2. SHORTCODES  */

// Register shortcodes
qe_register_shortcodes ();

function qe_register_shortcodes () {
  
  add_shortcode('qe_form', 'qe_form_shortcode');
  
  // For test purposes:
  // add_shortcode( 'lorem', 'lorem_function' );
  // add_shortcode( 'qe_test', 'qe_quizext_test' );
}


// The 'no_texturize_shortcodes' filter allows you to specify which shortcodes
// should not be run through the wptexturize() function.
function qe_shortcodes_to_exempt_from_wptexturize( $shortcodes ) {
    $shortcodes[] = 'qe_form';
    return $shortcodes;
}
add_filter( 'no_texturize_shortcodes', 'qe_shortcodes_to_exempt_from_wptexturize' );


// Create dynamic form object inside the lesson (multiple forms allowed, for details about parameters
// see learning course, "Code question type")
function qe_form_shortcode ($args, $content = "") {

  $qe_id = 0;
  if (isset( $args['id'] )) {
    $qe_id = (int) $args['id'];
  } 

  if (isset( $args['lang'] )) {
    $qe_lang_string = $args['lang'];
  } else {
    $qe_lang_string = 'undefined';
  }
  $qe_lang_array = explode ('|', $qe_lang_string);
  $qe_lang_count = count ( $qe_lang_array );

  $qe_lang_default = ( isset ( $qe_lang_array[0] ) && ( $qe_lang_array[0] != 'undefined' ) ) ? $qe_lang_array[0] : 'cpp';
  if (isset( $args['lang-default'] )) {
    $qe_lang_default = $args['lang-default'];
  }
  
  $qe_lang_samples_string = '';
  if (isset( $args['lang-samples'] )) {
    $qe_lang_samples_string = $args['lang-samples'];
  } 
  $qe_lang_samples_array = explode ('|', $qe_lang_samples_string);
  $qe_lang_samples_count = count ( $qe_lang_samples_array );

  
  $qe_enable_run = TRUE;
  if (isset( $args['enable-run'] )) {
    $qe_enable_run = ( TRUE == $args['enable-run'] );
  }
     
  $qe_code_string = $content; 
  
   
  // TO REVIEW: replace only at the beginning?
  $qe_code_string = str_replace ("</p>", "", $qe_code_string);
  $qe_code_string = str_replace ("<pre><code>", "", $qe_code_string);
  
  // TO REVIEW: replace only at the end?
  $qe_code_string = str_replace ("<p>", "", $qe_code_string);
  
  $qe_code_string = trim($qe_code_string, '"');
  $qe_code_string = trim($qe_code_string);
  $qe_code_string = str_replace ("</code></pre>", "", $qe_code_string);

  // Avoid extra new line after code separator
  $qe_code_string = str_replace ("//////////QE-CODE-SEPARATOR==========\r\n", "//////////QE-CODE-SEPARATOR==========", $qe_code_string);
  
  // Dump (for debug)
  // var_dump("---<div><pre><code>" . esc_html ($qe_code_string). "</code></pre></div>---");
  
  $qe_code_array = explode ('//////////QE-CODE-SEPARATOR==========', $qe_code_string);
  
  $qe_input_string = "10\n20\n30";
  if (isset( $args['input'] )) {
	   $qe_input_string = $args['input'];
  }
  
  // Dump (for debug)
  // var_dump("---<div><pre><code>" .$qe_input_string. "</code></pre></div>---");

  
  $output = '
  <script src="/wp-content/plugins/qe-quizext/js/ace1.2.8/ace.js"></script>
  
  <div class="qe_code_tabs">
  
<ul class="tab qe_code" style="list-style-type: none; margin: 0; padding: 0; overflow: hidden; border: 1px solid #ccc; background-color: #f1f1f1;">';
  if ( ( FALSE !== array_search ('cpp', $qe_lang_samples_array) ) || ( FALSE !== array_search ('cpp', $qe_lang_array) ) ) {
    $output .= '<li style="float: left; margin-top: 0px;"><a ' . ($qe_lang_default == 'cpp' ? 'id="qe_defaultOpen" ' : '' ). 'href="javascript:void(0)" class="qe_tablinks qe_code_cpp" onclick="qe_openCode(event, \'qe_code_cpp\')" style="display: inline-block; color: black; text-align: center; padding: 8px 16px; text-decoration: none; transition: 0.3s; font-size: 16px; background-color: #ccc;">C++</a></li>';
  }
  if ( ( FALSE !== array_search ('pascal', $qe_lang_samples_array) ) || ( FALSE !== array_search ('pascal', $qe_lang_array) ) )  {
    $output .= '<li style="float: left; margin-top: 0px;"><a ' . ($qe_lang_default == 'pascal' ? 'id="qe_defaultOpen" ' : '' ). 'href="javascript:void(0)" class="qe_tablinks qe_code_pascal" onclick="qe_openCode(event, \'qe_code_pascal\')" style="display: inline-block; color: black; text-align: center; padding: 8px 16px; text-decoration: none; transition: 0.3s; font-size: 16px;">Pascal</a></li>';
  }
  if ( ( FALSE !== array_search ('csharp', $qe_lang_samples_array) ) || ( FALSE !== array_search ('csharp', $qe_lang_array) ) ) {
    $output .= '<li style="float: left; margin-top: 0px;"><a ' . ($qe_lang_default == 'csharp' ? 'id="qe_defaultOpen" ' : '' ). 'href="javascript:void(0)" class="qe_tablinks qe_code_csharp" onclick="qe_openCode(event, \'qe_code_csharp\')" style="display: inline-block; color: black; text-align: center; padding: 8px 16px; text-decoration: none; transition: 0.3s; font-size: 16px;">C#</a></li>';
  }
  if ( ( FALSE !== array_search ('python2', $qe_lang_samples_array) ) || ( FALSE !== array_search ('python2', $qe_lang_array) ) ) {
    $output .= '<li style="float: left; margin-top: 0px;"><a ' . ($qe_lang_default == 'python2' ? 'id="qe_defaultOpen" ' : '' ). 'href="javascript:void(0)" class="qe_tablinks qe_code_python2" onclick="qe_openCode(event, \'qe_code_python2\')" style="display: inline-block; color: black; text-align: center; padding: 8px 16px; text-decoration: none; transition: 0.3s; font-size: 16px;">Python 2</a></li>';
  }
  if ( ( FALSE !== array_search ('python3', $qe_lang_samples_array) ) || ( FALSE !== array_search ('python3', $qe_lang_array) ) ) {
    $output .= '<li style="float: left; margin-top: 0px;"><a ' . ($qe_lang_default == 'python3' ? 'id="qe_defaultOpen" ' : '' ). 'href="javascript:void(0)" class="qe_tablinks qe_code_python3" onclick="qe_openCode(event, \'qe_code_python3\')" style="display: inline-block; color: black; text-align: center; padding: 8px 16px; text-decoration: none; transition: 0.3s; font-size: 16px;">Python 3</a></li>';
  }
  if ( ( FALSE !== array_search ('java', $qe_lang_samples_array) ) || ( FALSE !== array_search ('java', $qe_lang_array) ) ) {
    $output .= '<li style="float: left; margin-top: 0px;"><a ' . ($qe_lang_default == 'java' ? 'id="qe_defaultOpen" ' : '' ). 'href="javascript:void(0)" class="qe_tablinks qe_code_java" onclick="qe_openCode(event, \'qe_code_java\')" style="display: inline-block; color: black; text-align: center; padding: 8px 16px; text-decoration: none; transition: 0.3s; font-size: 16px;">Java</a></li>';
  }
  
  $output .= '</ul>
  
</div>
  
  
  <div id="qe_code_cpp' .$qe_id. '" class="qe_tabcontent" style="height:10px;">' .(
//    $qe_lang == 'cpp' ? $qe_code : 
    ( FALSE !== ($qe_found = array_search ('cpp', $qe_lang_array)) )   ?   ( isset ( $qe_code_array[$qe_found] ) ? $qe_code_array[$qe_found] : '' )   :    
'// SAMPLE CODE!
#include &lt;iostream&gt;
using namespace std;

int main()
{
&#9;int a, b, c;
&#9;cin >> a;
&#9;cin >> b;
&#9;cin >> c;
&#9;cout << "Hello World! a + b + c = " << (a + b + c);
&#9;return 0;
}
' ). '</div>
<div id="qe_code_pascal' .$qe_id. '" class="qe_tabcontent" style="height:10px;">' .(
//    $qe_lang == 'pascal' ? $qe_code : 
    ( FALSE !== ($qe_found = array_search ('pascal', $qe_lang_array)) )   ?   ( isset ( $qe_code_array[$qe_found] ) ? $qe_code_array[$qe_found] : '' )   : 
'// SAMPLE CODE!
Program simple;
var x, y: Integer;
begin
&#9;readln(x);
&#9;readln(y);
&#9;writeln(&#39;x * y = &#39;, x * y);
end.
' ). '</div>
<div id="qe_code_csharp' .$qe_id. '" class="qe_tabcontent" style="height:10px;">' .(
// $qe_lang == 'csharp' ? $qe_code : 
    ( FALSE !== ($qe_found = array_search ('csharp', $qe_lang_array)) )   ?   ( isset ( $qe_code_array[$qe_found] ) ? $qe_code_array[$qe_found] : '' )   : 
'// SAMPLE CODE!
// Rextester.Program.Main is the entry point for your code.
// Don\'t change it.
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text.RegularExpressions;
namespace Rextester
{
&#9;public class Program
&#9;{
&#9;&#9;public static void Main(string[] args)
&#9;&#9;{
&#9;&#9;&#9;// Your code goes here
&#9;&#9;&#9;Console.WriteLine(&quot;Hello, world!&quot;);
&#9;&#9;&#9;String s = Console.ReadLine();
&#9;&#9;&#9;int i;
&#9;&#9;&#9;if (! int.TryParse (s, out i)) {
&#9;&#9;&#9;&#9;Console.WriteLine(&quot;Not a number!&quot;);
&#9;&#9;&#9;} else {
&#9;&#9;&#9;&#9;Console.WriteLine(&quot;The number is: &quot; + i);
&#9;&#9;&#9;}
&#9;&#9;}
&#9;}
}
' ). '</div><div id="qe_code_python2' .$qe_id. '" class="qe_tabcontent" style="height:10px;">' .(
// $qe_lang == 'python2' ? $qe_code : 
    ( FALSE !== ($qe_found = array_search ('python2', $qe_lang_array)) )   ?   ( isset ( $qe_code_array[$qe_found] ) ? $qe_code_array[$qe_found] : '' )   :     
'# SAMPLE CODE!
#python 2.7
print &quot;Hello, world!&quot;
var1 = int(raw_input())
var2 = int(raw_input())
print &quot;var1 + var2 = &quot;, var1 + var2
' ). '</div><div id="qe_code_python3' .$qe_id. '" class="qe_tabcontent" style="height:10px;">' .(
// $qe_lang == 'python3' ? $qe_code : 
    ( FALSE !== ($qe_found = array_search ('python3', $qe_lang_array)) )   ?   ( isset ( $qe_code_array[$qe_found] ) ? $qe_code_array[$qe_found] : '' )   :     
'# SAMPLE CODE!
#python 3.5
print (&quot;Hello, world!&quot;)
var1 = int(input())
var2 = int(input())
print (&quot;var1 + var2 = &quot;, var1 + var2)
' ). '</div><div id="qe_code_java' .$qe_id. '" class="qe_tabcontent" style="height:10px;">' .(
//$qe_lang == 'java' ? $qe_code : 
    ( FALSE !== ($qe_found = array_search ('java', $qe_lang_array)) )   ?   ( isset ( $qe_code_array[$qe_found] ) ? $qe_code_array[$qe_found] : '' )   :     
'// SAMPLE CODE!
// main() method must be in a class Rextester.
import java.util.*;
import java.lang.*;
class Rextester
{
&#9;public static void main(String args[])
&#9;{
&#9;&#9;System.out.println(&quot;Hello, World!&quot;);
&#9;&#9;Scanner input = new Scanner(System.in);
&#9;&#9;int m = input.nextInt();
&#9;&#9;int n = input.nextInt();
&#9;&#9;int o = input.nextInt();
&#9;&#9;System.out.println(m * n * o);
&#9;}
}
'  ). '</div>
 
<div id="qe_code_area' .$qe_id. '"> 
</div>';
  
  // Allow code running, if allowed 
  if ( $qe_enable_run ) {
    // Use dynamic forms (actually divs)
    $output .= 
//    '<form id="qe_form" name="qe_form" class="qe-form" method="post"
// action="/wp-admin/admin-ajax.php?action=qe_run_code">
    '<div id="qe_form' .$qe_id. '" name="qe_form" class="qe-form">

    <p class="qe-input-container">
			<input type="hidden" name="qe_id" id="qe_id' .$qe_id. '" value="' .$qe_id. '">
			<input type="hidden" name="qe_code_string" id="qe_code_string' .$qe_id. '" value="">
			<input type="hidden" name="qe_lang_string" id="qe_lang_string' .$qe_id. '" class="qe_lang_string" value="' .$qe_lang_default. '">
			<input type="hidden" name="qe_input_string" id="qe_input_string' .$qe_id. '" value="">
		</p>
		<button id="qe_run_button' .$qe_id. '" class="qe_run_button" type="button" value="Выполнить" style="background-color: #785ab4; color: #fff">Выполнить</button>	
	</div>
Input:
<textarea id="qe_input' .$qe_id. '" class="qe_input" rows="4">' .$qe_input_string. '</textarea>
Output:<pre id="qe_output' .$qe_id. '" class="qe_output">Waiting...</pre>';
  }
  
  $output .= '<script src="/wp-content/plugins/qe-quizext/js/qe-quizext.js"></script>';
	  
  return $output;
	  
}


/* 3. FILTERS */

// Not used in the current version
function qe_question_column_headers ( $columns ) {

  $columns = array (
    'cb' => '<input type="checkbox" />',
    'title' => __('Title'),
    'qe_question_type' => __('Question Type'),
    'date' => __('Date')
	);
  
  return $columns;
}


// Not used in the current version
function qe_question_column_data ( $column, $post_id ) {
  
  $output = '';

  switch ($column) {
  case 'qe_question_type':
    $q_type = get_field ('qe_question_type', $post_id);
    $output .= $q_type;
  break;
  }

  echo $output;
}


/*  4. EXTERNAL SCRIPTS  */

// Public scripts
function qe_public_scripts () {
  
  // Register scripts with WordPress's internal library
  wp_register_script('qe-js-public', plugins_url('/js/qe-quizext.js', __FILE__),
					array('jquery'), '', true); 
  

  // Register styles with WordPress's internal library
  wp_register_style('qe-css-public', plugins_url('/css/qe-quizext.css', __FILE__)); 

  
  
  // Add to queue of scripts that get loaded into every page
  wp_enqueue_script('qe-js-public');
 
  // Add to queue of styles that get loaded into every page
  wp_enqueue_style('qe-css-public');

}

// Admin scripts
function qe_admin_scripts () {
  
  // Register scripts with WordPress's internal library
  wp_register_script('qe-js-admin', plugins_url('/js/qe-quizext.js', __FILE__),
					array('jquery'), '', true); 
  
  // Add to queue of scripts that get loaded into every page
  wp_enqueue_script('qe-js-admin');
}



/* 5. ACTIONS */

// Run single code checker (for single input)
// Data is in the POST
function qe_run_code () {

  $result = array (
    'status' => 0,
    'message' => 'Error in code'
	);
  
  try {
	
    $qe_id = $_POST['qe_id'];

    $code = $_POST['qe_code_string'];
    $code = stripslashes($code);

    $input = $_POST['qe_input_string'];
    $input = stripslashes($input);

    $lang = $_POST['qe_lang_string'];
    $lang = stripslashes($lang);

    $result['status'] = 1;
    $result['message'] = qe_rextester_curl_ajax_request($code, $input, $lang);

	
  } catch (Exception $e) {
    
    $result['error'] = 'Caught exception' .$e->getMessage();
  }
  
  qe_return_json($result);
}



/* 6. HELPERS */

// Run code tests (REVIEW NEEDED, after several iterations)
// Returns: 
// false - in case of errors or "no result"
// program output - otherwise
function qe_run_code_tests ($code, $lang, $input, $output) {

  $msg = qe_rextester_curl_ajax_request($code, $input, $lang);
   	
  // Parse a JSON string 
  $obj = json_decode($msg);
  
    
  if( isset($obj->Errors) || 
      ( !isset($obj->Result) ) 
    ) {

    // Errors, or no result
    return false;
  } else {
    
    // Return result
    $prog_output = $obj->Result;
    
    return $prog_output;
    
    // return ( $prog_output == $output );
    // qe_res_str += ("Program output is:\n" + obj.Result.replace(/\r/g, ""));
  }  
}

// Get json form of data
function qe_return_json ($php_array) {
 
  $json_result = json_encode($php_array);
  
  die ($json_result);
  
  exit;
}


// Just for testing of CURL
function qe_curl_request($url) {
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    return($response); 
}   

// Perform AJAX request to the external service
function qe_rextester_curl_ajax_request ($code, $input, $lang = 'cpp') {

  # An HTTP POST request
  # a pass-thru script to call server-side code
  
  # the POST data we receive
  // 7 - Cpp, 9 - Pascal, 1 - CSharp, 5 - Python2, 24 - Python3, 4 - Java
  switch ($lang) {
    case 'cpp':
    default:
      $LanguageChoice = "7";
      break;
    case 'pascal':
      $LanguageChoice = "9";
      break;
    case 'csharp':
      $LanguageChoice = "1";
      break;
    case 'python2':
      $LanguageChoice = "5";
      break;
    case 'python3':
      $LanguageChoice = "24";
      break;
    case 'java':
      $LanguageChoice = "4";
      break;
  }
  $Program = $code;
  $Input = $input;
  $CompilerArgs = "-o a.out source_file.cpp";
  
  # data needs to be POSTed to the Rextester url as JSON.
  $data = array("LanguageChoice" => "$LanguageChoice", "Program" => "$Program",
				"Input" => "$Input", "CompilerArgs" => "$CompilerArgs");
  $data_string = json_encode($data);
  
  $ch = curl_init('http://rextester.com/rundotnet/api');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Content-Length: ' . strlen($data_string))
			 );
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  
  // execute post
  $result = curl_exec($ch);
  
  // close connection
  curl_close($ch);
  
  return $result;
}


// Just for testing of CURL
function qe_test_curl() {
	$user = wp_get_current_user();
    $result = qe_curl_request ("http://www.example.com");
    $pageparts = explode ("<body>", $result);
    $body = explode ("</body>", $pageparts[1]);
	echo "Welcome back, " . $user->display_name . "!<br><br>";
    echo "<div>" . $body[0] . "</div>";
}



/**
 * Retrieve Groups (BuddyPress), for dynamic selection of groups in reports
 *
 * Used by Select2 AJAX functions to load paginated group results
 *
 * @return   json
 */
function qe_query_groups() {

  // Get all groups of the user
  $qe_groups = BP_Groups_Member::get_group_ids( get_current_user_id() );  
  $qe_groups_res = array();

  // Array ( [groups] => Array ( [0] => 1 
  foreach ($qe_groups["groups"] as $qe_group_id) {
    $qe_group = groups_get_group ( $qe_group_id );
    
    
    // Only show groups where admin or moderator
    if  (
        ( groups_is_user_mod( get_current_user_id(), $qe_group_id ) || groups_is_user_admin( get_current_user_id(), $qe_group_id ) ) 
    ) {

      $qe_group_res = (object) ['id' => $qe_group_id];
      $qe_group_res->name = $qe_group->name;

      $qe_groups_res[] = $qe_group_res;
    }
  }

  echo json_encode( array(
    'items' => $qe_groups_res,
    'more' => false,
    'success' => true,
  ) );

  wp_die();

}
add_action( 'wp_ajax_qe_query_groups', 'qe_query_groups' );



//
// Constants
//

const QE_QUIZEXT_PLUGIN_ID = "qe_quizext";
const QE_QUIZEXT_PLUGIN_NAME = "QE_QUIZEXT";
const QE_QUIZEXT_PARAM1_OPTION = "qe_quizext_param1";


define('QE_QUIZEXT_DIR', plugin_dir_path(__FILE__));
define('QE_QUIZEXT_URL', plugin_dir_url(__FILE__));

/**
 * Autoloader
 */

register_activation_hook(__FILE__, 'qe_quizext_activation');
register_deactivation_hook(__FILE__, 'qe_quizext_deactivation');
 
function qe_quizext_activation() {
 
    // Activation
    
    // Register action on uninstall
    register_uninstall_hook(__FILE__, 'qe_quizext_uninstall');
}
 
function qe_quizext_deactivation() {
    // Deactivation
}

function qe_quizext_uninstall() {
    // Uninstall
}


// Not used in the current version
/*
add_action ('admin_menu', 'qe_quizext_settingsmenu');
function qe_quizext_settingsmenu () {
    
    add_options_page (
        'Settings for '.QE_QUIZEXT_PLUGIN_NAME,
        QE_QUIZEXT_PLUGIN_NAME,
        8,  // for admins
        QE_QUIZEXT_PLUGIN_ID,
        'render_qe_quizext_settings_page'
    );
}

function render_qe_quizext_settings_page () {
  include ('includes/qe-settings.php');        
}
*/


