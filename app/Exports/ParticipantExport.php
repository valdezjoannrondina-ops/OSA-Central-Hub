<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParticipantExport implements FromCollection, WithHeadings
{
    protected $participants;

    public function __construct($participants)
    {
        $this->participants = $participants;
    }

    public function collection()
    {
        return $this->participants->map(function ($part) {
            return [
                'Student ID' => $part->user->user_id,
                'Full Name' => $part->user->first_name . ' ' . $part->user->last_name,
                'Department' => $part->user->department->name ?? 'N/A',
                'Course' => $part->user->course->name ?? 'N/A',
                'Year Level' => $part->user->year_level,
                'Event' => $part->event->title,
                'Event Date' => $part->event->event_date,
                'Attendance Status' => $part->qr_scanned ? 'Attended' : 'Registered',
                'Scanned By' => $part->scanner ? $part->scanner->first_name . ' ' . $part->scanner->last_name : 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Full Name',
            'Department',
            'Course',
            'Year Level',
            'Event',
            'Event Date',
            'Attendance Status',
            'Scanned By'
        ];
    }
}