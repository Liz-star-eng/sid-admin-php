<?php

class Utils {
    public static function convertStringToJson($data)
    {
        // If the input is a string, attempt to decode it as JSON
        if (is_string($data)) {
            $decoded = json_decode($data, true);

            // If decoding is successful, return the decoded value
            if ($decoded !== null) {
                return $decoded;
            }
        }
        
        // If the input is an array or object, recursively apply the function to each element
        if (is_array($data) || is_object($data)) {
            foreach ($data as &$value) {
                $value = self::convertStringToJson($value);
            }
        }

        return $data;
    }

}

