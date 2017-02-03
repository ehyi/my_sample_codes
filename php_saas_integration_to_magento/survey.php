<?php
///////////////////////////////////////////////////////////////////////////////
// mydomain.com/survey/?survey
// mydomain.com/survey/?survey_key=<survey name>
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
require_once "forms/includes/class.php";

///////////////////////////////////////////////////////////////////////////////
$domain_name = "abc";
$max_textarea_chars = 300;

///////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST['survey_key'])) {
    $survey_key = $_REQUEST['survey_key'];
}

$SE = new SurveyEngine($_REQUEST['survey_key'], $domain_name, $_POST, $_REQUEST['thanks']);
$error_text = $SE->error_message;
$survey_key = $SE->survey_key;
$survey_title = $SE->survey_title;
$survey_subtitle = $SE->survey_subtitle;
$intro_text = $SE->intro_text;
$survey_questions = $SE->survey_questions;
$user_answers = $SE->answers;
?>
<?php
if (!isset($_REQUEST['thanks'])) {
?>
<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.8/jquery.validate.min.js"></script>
<script type="text/javascript" src="/forms/survey.js"></script>
<?php
}
?>
<div id="survey-header-wrapper">
    <div id="survey-header">
        <h1><?php echo $survey_title ?></h1>
        <h2><?php echo $survey_subtitle ?></h2>
    </div>

<?php
if (!isset($_REQUEST['thanks'])) {
?>

    <form id="survey" action="" method="post">
        <input type="hidden" name="survey_key" value="<?php echo $survey_key?>">
<?php 
    if ($intro_text != '') {
?>
        <p id="survey-intro"><?php echo $intro_text ?></p>
<?php 
    }
?>
<?php 
    if ($error_text != '') { 
?>
        <p><?php echo $error_text ?></p>
<?php 
    }
?>
<?php 
    $qind = 1; 
    $otherstr = 'Other'; 
?> 
        <div id="survey-questions">
            <br />
<?php 
    foreach ($survey_questions as $question) { 
?> 
            <div class="survey-question">
                <p>
                    <span class="bold"><?php echo $question->question ?> 
<?php 
        if ($question->question_type == 'checkbox') {
?>Check all that apply.
<?php 
        } elseif ($question->question_type == 'checkbox-other') { 
?>Check all that apply.
<?php 
        }
?>
                    </span>
                </p>
                <input type="hidden" name="data[<?php echo $qind ?>][q]" value="<?php echo $question->question; ?>" />
<?php 
        if ($question->question_type == 'text') {
?>
                <p><input type="text" name="data[<?php echo $qind ?>][a]" size="60" value="<?php $user_answers[$qind]?>" class="input-text" /></p>
<?php 
        } elseif ($question->question_type=='textarea') {
?>
                <p><textarea name="data[<?php echo $qind ?>][a]" cols="60" rows="5" maxlength="<?php echo $max_textarea_chars?>"><?php echo $user_answers[$qind] ?></textarea><div align="left">(Max. <?php echo $max_textarea_chars?> characters)</div></p>
<?php 
        } elseif ($question->question_type == 'radio') {
?>       
                <span class="survey-question">
                    <p>
<?php 
            foreach ($question->answers as $answer) { 
?>     
                        <input type="radio" name="data[<?php echo $qind ?>][a]" id="data<?php echo $qind ?>a" value="<?php echo $answer ?>" class="input-text" />&nbsp;<?php echo $answer ?></br>
<?php 
            }
?>
                    </p>
                </span>
<?php 
        } elseif ($question->question_type == 'checkbox') {
?>
                <span class="survey-question">
                    <p>    
<?php 
            foreach ($question->answers as $answer) { 
?>
                        <input type="checkbox" name="data[<?php echo $qind ?>][a][]" value="<?php echo $answer ?>" <?php if ($user_answers[$qind][$answer] == $answer) { ?> checked <?php } ?> class="input-text" />&nbsp;<?php echo $answer?></br>
<?php 
        }
?>
                    </p>
                </span>
<?php 
        } elseif ($question->question_type == 'checkbox-other') {
?>
                <span class="survey-question">
                    <p>
<?php 
            foreach ($question->answers as $answer) {
?>
                        <input type="checkbox" name="data[<?php echo $qind ?>][a][]" value="<?php echo $answer ?>" <?php if ($user_answers[$qind][$answer] == $answer) { ?> checked <?php } ?> class="input-text" />&nbsp;<?php echo $answer?> </br>
<?php 
            }
?>
                        <input type="checkbox" name="data[<?php echo $qind ?>][a][]" value="Other" <?php if ($user_answers[$qind][$otherstr] == "Other") { ?> checked <?php } ?> class="input-text" />&nbsp;Other (please specify) 
                        <input type="text" name="data[<?php echo $qind?>][o]" value="<?php echo $user_answers[0][$qind]?>" class="input-text" /></br>
                    </p>
                </span>
     
<?php 
        } else { 
?>
                <span class="survey-question">
                    <p>
                        <select name="data[<?php $qind?>][a]" class="input-text">
<?php 
            foreach ($question->answers as $answer) {
?>
                            <option value="<?php echo $answer?>" <?php if ($user_answers[$qind] == $answer) { ?> selected <?php } ?>><?php echo $answer?></option>
<?php 
            }
?>
                        </select>
                    </p>
                </span>
<?php 
        }
?>
<?php 
        $qind++;
?>
            </div>
<?php 
    }
?>
            <br />
            <p style="margin: 0 0 1em;"><strong>Please complete the following information to help us serve you.</strong></p>
            <div>
                <label for="zip" class="required">School Zip Code <em>*</em></label>
                <input type="text" name="Zip Code" id="zip" class="input-text required" minlength="5" maxlength="5" value="<?php echo $user_answers[99][1] ?>"><span id="loadingzip" style="display: none;" /><img src="/forms/images/zip-loader.gif" /></span>
            </div>
            <div class="clear-both" style="padding-bottom:8px;"></div>
            <div>                
                <label for="school" class="input-text required">Your School <em>*</em></label>
                <span id="zip-load">
                    <select name="schooltemp" id="school" class="input-text required" disabled="">
						<option value="">Enter zip code first</option>
					</select>
					<input type="hidden" name="schidtemp" id="schidtemp" value="<?php echo $user_answers[99][2] ?>">
				</span>
            </div>
            <div class="clear-both" style="padding-bottom:8px;"></div>
            <div class="field">
                <div id="phone-load"></div>
            </div>
            <div>
                <label for="firstname" class="required">First Name <em>*</em></label>
                <input type="text" name="First Name" id="firstname" value="<?php echo $user_answers[99][8] ?>" class="input-text required" />
            </div>
            <div class="clear-both" style="padding-bottom:8px;"></div>
            <div>
                <label for="lastname" class="required">Last Name <em>*</em></label>
                <input type="text" name="Last Name" id="lastname" value="<?php echo $user_answers[99][9] ?>" class="input-text required" />
            </div>
            <div class="clear-both" style="padding-bottom:8px;"></div>
            <div>
                <label for="title" class="required">Title <em>*</em></label>
                <input type="text" name="Designation" id="title" value="<?php echo $user_answers[99][10] ?>" class="input-text required" />
            </div>
            <div class="clear-both" style="padding-bottom:8px;"></div>
            <div>
                <label for="email" class="required">Email <em>*</em></label>
                <input type="text" name="Email" id="email" value="<?php echo $user_answers[99][11] ?>"  class="input-text required email" />
            </div>
            <div style="float:left">
                <p class="required">* Required Fields</p>
                <input type="text" name="hideit" id="hideit" value="" style="display:none !important;"/>
                <button type="submit" title="Submit" class="button"><span><span>Submit</span></span></button>
            </div>
        </div>
    </form>

<?php
} else {
?>

<br/>
<p id="survey-thanks">Thank you for your interest!</p>

<?php
}
?>

</div>