<?php
/**
 * @author 	SuperAppps
 * @package 	lifterLMS/Templates (QE patch)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$quiz = new LLMS_Quiz( $args['quiz_id'] );
$quiz_obj = $quiz;

$question = new LLMS_Question( $args['question_id'] );

?>

<?php 

$question_type = get_post_meta ($question->id, '_llms_question_type', true);
if ($question_type == "") {
  $question_type = "single_choice";
}

switch ($question_type) {
    case "single_choice":
        llms_get_template( 'quiz/single-choice_ajax.php', $args );
        break;
    case "multiple_choice":
        llms_get_template( 'quiz/QE_multiple-choice_ajax.php', $args );
        break;
    case "fill_in_the_blank":
        llms_get_template( 'quiz/QE_fill-in-the-blank_ajax.php', $args );
        break;
    case "code":
        llms_get_template( 'quiz/QE_code_ajax.php', $args );
        break;
}

?>