<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgStructureConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_key',
        'staff_per_row',
        'staff_selections',
        'max_levels'
    ];

    protected $casts = [
        'staff_per_row' => 'array',
        'staff_selections' => 'array', // Array of arrays: [[staff_id1, staff_id2], [staff_id3, staff_id4], ...]
        'max_levels' => 'integer'
    ];

    /**
     * Get or create default configuration
     */
    public static function getDefaultConfig()
    {
        return self::firstOrCreate(
            ['config_key' => 'admin_staff_structure'],
            [
                'staff_per_row' => [1, 2, 4, 8, 8], // Default: 1 admin, then 1, 2, 4, 8, 8 staff per row
                'max_levels' => 5
            ]
        );
    }
}
