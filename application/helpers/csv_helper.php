<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('array_to_csv'))
{
    function array_to_csv($array, $download = "")
    {
        if ($download != "")
        {    
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="' . $download . '"');
        }        

        $f = tmpfile() or show_error("Can't open php://output");
        $n = 0;        
        foreach ($array as $line)
        {
            $n++;
            if ( ! fputcsv($f, $line) )
            {
                show_error("Can't write line $n: $line");
            }
        }
        fseek($f, 0);
        $str = fread($f, fstat($f)['size']);
        fclose($f) or show_error("Can't close php://output");

        if ($download == "")
        {
            return $str;    
        }
        else
        {    
            echo $str;
        }        
    }
}

/* End of file csv_helper.php */
/* Location: ./system/helpers/csv_helper.php */