<?php
// Patch for pdf2txt() posted Sven Schuberth
// Add/replace following code (cannot post full program, size limitation)

// handles the verson 1.2
// New version of handleV2($data), only one line changed
function handleV2($data){

    // grab objects and then grab their contents (chunks)
    $a_obj = getDataArray($data,"obj","endobj");
		$j=0;
		$result_data=false;
		$a_chunks=array();

    foreach($a_obj as $obj){

        $a_filter = getDataArray($obj,"<<",">>");

				echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$a_filter</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
				var_export($a_filter);
				echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

        if (is_array($a_filter)){
            $j++;
            $a_chunks[$j]["filter"] = $a_filter[0];

            $a_data = getDataArray($obj,"stream","endstream");
//						echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$a_data</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
//						var_export($a_data);
//						echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
            if (is_array($a_data)){
							$data=trim(str_replace(array('stream','endstream'), false, $a_data[0]));
							$a_chunks[$j]["data"] = substr($a_data[0],
							strlen("stream"),
							strlen($a_data[0])-strlen("stream")-strlen("endstream"));
            }
        }
    }
		echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$a_chunks</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
		var_export($a_chunks);
		echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
    // decode the chunks
    foreach($a_chunks as $chunk){

        // look at each chunk and decide how to decode it - by looking at the contents of the filter
        $a_filter = explode("/",$chunk["filter"]);
        if (!empty($chunk["data"])){
            // look at the filter to find out which encoding has been used
            if (substr($chunk["filter"],"FlateDecode")!==false){
                $data =@ gzuncompress($chunk["data"]);
                if (trim($data)!=""){
            // CHANGED HERE, before: $result_data .= ps2txt($data);
                    $result_data .= PS2Text_New($data);
                } else {

                    //$result_data .= "x";
                }
            }
        }
    }
    return $result_data;
}

// New function - Extract text from PS codes
function ExtractPSTextElement($SourceString)
{
$CurStartPos = 0;
$Result=false;
while (($CurStartText = strpos($SourceString, '(', $CurStartPos)) !== FALSE)
    {
    // New text element found
    if ($CurStartText - $CurStartPos > 8) $Spacing = ' ';
    else    {
        $SpacingSize = substr($SourceString, $CurStartPos, $CurStartText - $CurStartPos);
        if ($SpacingSize < -25) $Spacing = ' '; else $Spacing = '';
        }
    $CurStartText++;

    $StartSearchEnd = $CurStartText;
    while (($CurStartPos = strpos($SourceString, ')', $StartSearchEnd)) !== FALSE)
        {
        if (substr($SourceString, $CurStartPos - 1, 1) != '\\') break;
        $StartSearchEnd = $CurStartPos + 1;
        }
    if ($CurStartPos === FALSE) break; // something wrong happened

    // Remove ending '-'
    if (substr($Result, -1, 1) == '-')
        {
        $Spacing = '';
        $Result = substr($Result, 0, -1);
        }

    // Add to result
    $Result .= $Spacing . substr($SourceString, $CurStartText, $CurStartPos - $CurStartText);
    $CurStartPos++;
    }
// Add line breaks (otherwise, result is one big line...)
return $Result . "\n";
}

// Global table for codes replacement
$TCodeReplace = array ('\(' => '(', '\)' => ')');

// New function, replacing old "pd2txt" function
function PS2Text_New($PS_Data)
{
global $TCodeReplace;

// Catch up some codes
if (ord($PS_Data[0]) < 10) return '';
if (substr($PS_Data, 0, 8) == '/CIDInit') return '';

// Some text inside (...) can be found outside the [...] sets, then ignored
// => disable the processing of [...] is the easiest solution

$Result = ExtractPSTextElement($PS_Data);

// echo "Code=$PS_Data\nRES=$Result\n\n";

// Remove/translate some codes
return strtr($Result, $TCodeReplace);
}

function getDataArray($data,$start_word,$end_word){

	$start = 0;
	$end = 0;
	$a_result=false;

	while ($start!==false && $end!==false){
		$start = strpos($data,$start_word,$end);
		if ($start!==false){
			$end = strpos($data,$end_word,$start);
			if ($end!==false){
				// data is between start and end
				$a_result[] = substr($data,$start,$end-$start+strlen($end_word));
			}
		}
	}
	echo 'start: '.$start;
	echo ', end: '.$end.'<br>';
	if(strpos($start_word, 'stream')!==false){
//		echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$data</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
//		var_export($data);
//		echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

		echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$a_result</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
		var_export($a_result);
		echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
	}
	return $a_result;
}