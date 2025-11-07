<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentRosterExport implements FromArray, WithHeadings
{
	protected array $rows;

	public function __construct(array $rows)
	{
		$this->rows = $rows;
	}

	public function array(): array
	{
		// Map rows to plain arrays in the exact column order
		return array_map(function ($r, $idx) {
			return [
				'#' => $idx + 1,
				'Student No' => $r['student_no'] ?? '',
				'Full Name' => $r['full_name'] ?? '',
				'Program' => $r['program'] ?? '',
				'Gender' => $r['gender'] ?? '',
				'Yr. Level' => $r['year_level'] ?? '',
				'OR No.' => $r['or_no'] ?? '',
				'Validation Date' => $r['validation_date'] ?? '',
				'Email' => $r['email'] ?? '',
				'Contact Number' => $r['contact_number'] ?? '',
			];
		}, $this->rows, array_keys($this->rows));
	}

	public function headings(): array
	{
		return ['#','Student No','Full Name','Program','Gender','Yr. Level','OR No.','Validation Date','Email','Contact Number'];
	}
}

