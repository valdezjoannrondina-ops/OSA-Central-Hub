<?php

namespace App\Helpers;

class EmailHelper
{
    /**
     * Generate email for role 1 users (students)
     * Format: first_name.last_name+student_id@gmail.com
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string $studentId
     * @return string
     */
    public static function generateStudentEmail($firstName, $lastName, $studentId)
    {
        // Clean names: lowercase, remove spaces and special characters, keep only alphanumeric
        $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));
        $cleanLastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $lastName));
        
        // Format: first_name.last_name+student_id@gmail.com
        return $cleanFirstName . '.' . $cleanLastName . '+' . $studentId . '@gmail.com';
    }
}

