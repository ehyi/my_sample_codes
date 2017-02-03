<?php


$surveyd['lead'] = $domain.' Website - Contact Us';
$surveyd['lead_description'] = 'This customer responded to the “Contact Us” survey on our website. Please review the Survey Results section below to review their specific needs. Please follow up with the customer to ask additional questions, offer solutions, and provide digital or print samples of our programs
';
$surveyd['survey_title'] = 'Request a Sample';
$surveyd['survey_subtitle'] = ''; 

$surveyd['intro'] = <<<'EOD'
EOD;

	$quest=array();
	$quest['question'] = 'What specific needs do you have for instructional materials?';
	$quest['type'] = 'textarea';
$surveyd['questions'][]=$quest; 

	$quest=array();
	$quest['question'] = 'What title, course, or content area are you interested in reviewing?';
	$quest['type'] = 'textarea'; 
$surveyd['questions'][]=$quest; 

	$quest=array();
	$quest['question'] = 'When will you make your decision?';
	$quest['type'] = 'text'; 
$surveyd['questions'][]=$quest; 

	$quest=array();
	$quest['question'] = 'How many students will these materials serve?';
	$quest['type'] = 'select'; 
	$quest['answers'][] = "1-50";
	$quest['answers'][] = "50-200";
	$quest['answers'][] = "200-500";
	$quest['answers'][] = "500 or more";
$surveyd['questions'][]=$quest; 

/*
$quest=array();
$quest['question'] = 'This is the second question';
$quest['type'] = 'radio'; 
$quest['answers'][] = 'Answer 1';
$quest['answers'][] = 'Answer 2';
$quest['answers'][] = 'Answer 3';
$quest['answers'][] = 'Answer 4';
$surveyd['questions'][$qi++]=$quest; 

$quest=array();
$quest['question'] = 'What subject areas are you interested in';
$quest['type'] = 'checkbox'; 
$quest['answers'][] = 'Answer 1';
$quest['answers'][] = 'Answer 2';
$quest['answers'][] = 'Answer 3';
$quest['answers'][] = 'Answer 4';
$surveyd['questions'][$qi++]=$quest; 
*/



