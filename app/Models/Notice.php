<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['title', 'content', 'target_role', 'notice_category_id'];

    public function category()
    {
        return $this->belongsTo(NoticeCategory::class, 'notice_category_id');
    }
}