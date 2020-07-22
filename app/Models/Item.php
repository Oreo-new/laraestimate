<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasUUID;
    
    protected $fillable = [
        'description',
        'duration',
        'price',
        'obligatory',
        'confirmed_by',
    ];

    protected $casts = [
        'obligatory' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($item) {
            $item->position = $item->section->getNextItemPosition();
        });
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    // public function confirmUpdate($section_id){
    //     return \DB::update("UPDATE `{$table}` SET `value` = CASE `id` {$cases} END, `updated_at` = ? WHERE `id` in ({$ids})", $params);
    // }
}
