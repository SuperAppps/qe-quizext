<?php
/*
 * Just for simple testing and demonstration purposes
 */

function qe_quizext_test() {
  $output = "";
    
  
  $TestUser = new qe_User('Alexey', 'pass1234');
  //  $TestUser->register();
  $TestUser->login();


  $TestPost = new qe_Post();
  $TestPost->name = 'NAME';
  echo $TestPost->name;
  var_dump (isset($TestPost->name));
  
  
  // Exit here
  return;
  
  
  $name = "Alexey";
  $varName = "name";

  $myNumber = 39;
  $newNumber = $myNumber * 20 - 7.5;

  $myBool1 = true;
  $myBool2 = false;

  $output .= "<p>Hello ".$name."!</p>";
  $output .= "<p>".$newNumber."</p>";
  $output .= "<p>".$myBool1."-".$myBool2."</p>";
  $output .= "<p>".$$varName."</p>";

  $array = array("1", "2", "3", "4");
  $output .= "<p>Array:".print_r($array, true).".</p>";
  $output .= "<p>Array[1]:".$array[1].".</p>";

  $array2["string_index"] = "si";
  $array2[5] = "5";
  $output .= "<p>array2:".print_r($array2, true).".</p>";

  $array3 = array(
	"test" => "test-item",
	0 => "zero", 
	"0" => "zero-as-string");
/*  unset($array3["0"]);  */
  $array3[] = "tail";
  $output .= "<p>array3:".print_r($array3, true).".</p>";
  $output .= "<p>sizeof(array3):".sizeof($array3).".</p>";

  if ((1 > 2) == true) {
    $output .= "<p>1 > 2: true</p>";
  } else {
    $output .= "<p>1 > 2: false</p>";
  }

  $user = "Alexey";
  if ($user == "Alexey") {
    $output .= "<p>Hello, Alexey!</p>";
  } else {
    $output .= "<p>I don't know you.</p>";
  }

  for ($i = 0; $i < 10; $i++) {
    $res = $i * $i;
    $output .= "<p>".$i." * ".$i." = ".$res."</p>";
  }

  for ($i = 0; $i < sizeof($array3); $i++) {
    $output .= "<p>array3[".$i."] = ".$array3[$i]."</p>";
  }


  foreach ($array3 as $key => $value) {
    $output .= "<p>array3[".$key."] = ".$value."</p>";
  }


  return $output;
}

function lorem_function() {
  return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec nulla vitae lacus mattis volutpat eu at sapien. Nunc interdum congue libero, quis laoreet elit sagittis ut. Pellentesque lacus erat, dictum condimentum pharetra vel, malesuada volutpat risus. Nunc sit amet risus dolor. Etiam posuere tellus nisl. Integer lorem ligula, tempor eu laoreet ac, eleifend quis diam. Proin cursus, nibh eu vehicula varius, lacus elit eleifend elit, eget commodo ante felis at neque. Integer sit amet justo sed elit porta convallis a at metus. Suspendisse molestie turpis pulvinar nisl tincidunt quis fringilla enim lobortis. Curabitur placerat quam ac sem venenatis blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam sed ligula nisl. Nam ullamcorper elit id magna hendrerit sit amet dignissim elit sodales. Aenean accumsan consectetur rutrum.';
}

