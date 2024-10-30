<?php
function lws_sms_requiresUnicodeEncoding($content){
    $gsmCodePoints = array_map(
        lws_sms_convertIntoUnicode(),
    [
    '@', '£', '$', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'ç', "\r", 'Ø', 'ø', "\n", 'Å', 'å',
    'Δ', '_', 'Φ', 'Γ', 'Λ', 'Ω', 'Π', 'Ψ', 'Σ', 'Θ', 'Ξ', 'Æ', 'æ', 'ß', 'É',
    ' ', '!', '"', '#', '¤', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/',
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?',
    '¡', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ä', 'Ö', 'Ñ', 'Ü', '§',
    '¿', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
    'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ñ', 'ü', 'à',
    "\f", '^', '{', '}', '\\', '[', '~', ']', '|', '€',
    ]
);

// Split $text into an array in a way that respects multibyte characters.
$textChars = preg_split('//u', $content, null, PREG_SPLIT_NO_EMPTY);

// Array of codepoint values for characters in $text.
$textCodePoints = array_map(lws_sms_convertIntoUnicode(), $textChars);

// Filter the array to contain only codepoints from $text that are not in the set of valid GSM codepoints.
$nonGsmCodePoints = array_diff($textCodePoints, $gsmCodePoints);

// The text contains unicode if the result is not empty.
return !empty($nonGsmCodePoints);
}

function lws_sms_convertIntoUnicode(){
    return function ($char) {
    $k = mb_convert_encoding($char, 'UTF-16LE', 'UTF-8');
    $k1 = ord(substr($k, 0, 1));
    $k2 = ord(substr($k, 1, 1));

    return $k2 * 256 + $k1;
    };
}

if(isset($_POST['text'])){
    $text = sanitize_textarea_field($_POST['text']);
    echo esc_textarea(lws_sms_requiresUnicodeEncoding($text));
}