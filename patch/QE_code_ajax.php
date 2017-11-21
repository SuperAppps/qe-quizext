<?php
/**
 * @author 	SuperAppps
 * @package 	lifterLMS/Templates (QE patch)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$quiz = new LLMS_Quiz( $args['quiz_id'] );
$quiz_obj = $quiz;

$question = new LLMS_Question( $args['question_id'] );


$options = $question->get_options();

$question_key = isset( $quiz ) ? $quiz->get_question_key : 0;

// $quiz_session = $quiz = LLMS()->session->get( 'llms_quiz' );

$answer = '';
if ( ! empty( $quiz_session->questions ) ) {
	foreach ( $quiz_session->questions as $q ) {

		if ( $q['id'] == $question->id ) {
			$answer = $q['answer'];
		}

	}
}
?>
<div class="clear"></div>
<div class="llms-question-wrapper">

  <div class="llms-option_<?php echo $question_key; ?>">
		<label class="llms-question-label">
			<input type="hidden" name="question_type" id="question-type" value="code" />
			<input type="hidden" name="question_id" id="question-id" value="<?php echo $question->id ?>" />
			<input type="hidden" name="quiz_id" id="quiz-id" value="<?php echo $quiz->get_id(); ?>" />
  
	<?php
  
echo do_shortcode ( '[qe_form id="0" lang-samples="cpp|pascal|csharp|python2|python3|java" enable-run="1"]
[/qe_form]' );
  
  
	?>
  <br>
  <br>
		</label>
	</div>
      
</div>

