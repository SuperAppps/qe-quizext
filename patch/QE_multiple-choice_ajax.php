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
	<?php
	if ( $quiz_obj->get_show_random_answers()) {
		llms_shuffle_assoc( $options );
	}
	foreach ($options as $key => $value) :
		if (isset( $value )) :
			$option = $value['option_text'];

      $checked = '';    
      if ('' !== $answer ) {
        $answers = json_decode( stripslashes( $answer) );
        if ( in_array ( $key, $answers ) ) {
          $checked = 'checked';
        } 				
      }

	?>
	<div class="llms-option_<?php echo $question_key; ?>">
		<label class="llms-question-label">
			<input type="checkbox" name="llms_option_selected" id="question-answer" value="<?php echo $key; ?>" <?php echo $checked; ?>/>
			<input type="hidden" name="question_type" id="question-type" value="multiple_choice" />
			<input type="hidden" name="question_id" id="question-id" value="<?php echo $question->id; ?>" />
			<input type="hidden" name="quiz_id" id="quiz-id" value="<?php echo $quiz->get_id(); ?>" />
			<?php echo wp_kses_post( $option ); ?>
		</label>
	</div>
	<?php
		endif;
	endforeach;
	?>
</div>

