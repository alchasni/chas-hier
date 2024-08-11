<?php
const MONTH_NAME = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const DAY_NAME = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
const NUMBER_NAME = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');

function money_number_format ($number): string
{
    return number_format($number, 0, ',', '.');
}

function money_written_format ($number): string
{
    $number = abs($number);
    $text = '';

    if ($number == 0) {
        return 'nol';
    } elseif ($number < 12) {
        $text = NUMBER_NAME[$number];
    } elseif ($number < 20) {
        $text = money_written_format($number - 10) . ' belas';
    } elseif ($number < 100) {
        $text = money_written_format($number / 10) . ' puluh' . money_written_format($number % 10);
    } elseif ($number < 200) {
        $text = ' seratus' . money_written_format($number - 100);
    } elseif ($number < 1000) {
        $text = money_written_format($number / 100) . ' ratus' . money_written_format($number % 100);
    } elseif ($number < 2000) {
        $text = ' seribu' . money_written_format($number - 1000);
    } elseif ($number < 1000000) {
        $text = money_written_format($number / 1000) . ' ribu' . money_written_format($number % 1000);
    } elseif ($number < 1000000000) {
        $text = money_written_format($number / 1000000) . ' juta' . money_written_format($number % 1000000);
    } elseif ($number < 1000000000000) { // 1.000.000.000 - 999.999.999.999
        $text = money_written_format($number / 1000000000) . ' milyar' . money_written_format($number % 1000000000);
    }

    // Trim any leading or trailing whitespace
    return trim($text);
}

function to_date_string($date, $show_day = true): string
{
    $year = substr($date, 0, 4);
    $month = MONTH_NAME[(int) substr($date, 5, 2)];
    $day = substr($date, 8, 2);

    if ($show_day) {
        $dotw = DAY_NAME[(int) date('w', mktime(0,0,0, substr($date, 5, 2), $day, $year))];
        return "$dotw, $day $month $year";
    } else {
        return "$day $month $year";
    }
}

function add_zero($value, $threshold = null): string
{
    return sprintf("%0". $threshold . "s", $value);
}
