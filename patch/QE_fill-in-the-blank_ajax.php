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
  
/*  
	if ( $quiz_obj->get_show_random_answers()) {
		llms_shuffle_assoc( $options );
	}
*/
  
//	foreach ($options as $key => $value) :
  // QE - take only first option
  $key = array_shift(array_keys($options)); 
  $value = array_shift(array_values($options)); 
  
  if (isset( $value )) :
    $option = $value['option_text'];

    $checked = 'checked';
    $answers = '';
    if ('' !== $answer ) {
      $answers = json_decode( stripslashes( $answer) );
    }
        
    $input_suffix = -1;
    $pattern = "/{{([^}]*)}}/";
    $replacement1 = "<input type='text' size='7' style='width:auto; display:inline;'  name='user_field_";
    $replacement2 = "' value='";
    $replacement3 = "'>";
    $subject = wp_kses_post( $option );
    
    $text_with_inputs = preg_replace_callback($pattern,
        function($found) use (&$pattern, &$replacement1, &$replacement2, &$replacement3, &$input_suffix, &$answers) {
                $input_suffix++;
                if (isset ($answers[$input_suffix]) ) {
                  $field_value = $answers[$input_suffix];
                } else {
                  $field_value = '';                              
                }
                return preg_replace($pattern, $replacement1 . $input_suffix . $replacement2 . $field_value . $replacement3, reset($found) );
        }, $subject);
    
  
   
    // $s = "Label is {input:inputvalue} and date is {date:2013-2-2}";
    // print preg_replace( "/{([^:}]*):?([^}]*)}/", "<input name='\\1' value='\\2'>", $s );
    

	?>
	<div class="llms-option_<?php echo $question_key; ?>">
		<label class="llms-question-label">
			<input type="checkbox" name="llms_option_selected" id="question-answer" value="<?php echo $key; ?>" style="display:none;" <?php echo $checked; ?>/>
			<input type="hidden" name="question_type" id="question-type" value="fill_in_the_blank" />
			<input type="hidden" name="question_id" id="question-id" value="<?php echo $question->id ?>" />
			<input type="hidden" name="quiz_id" id="quiz-id" value="<?php echo $quiz->get_id(); ?>" />
			<?php echo $text_with_inputs; ?>
		</label>
	</div>
	
  <?php
  endif;
//	endforeach;
	?>
  
</div>

