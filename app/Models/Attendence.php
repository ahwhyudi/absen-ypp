<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    use HasFactory;

    // Karena nama tabelnya mengikuti standar jamak Laravel (attendances), tidak perlu di-define manual.

    // protected $fillable = [
    //     'user_id',
    //     'date',
    //     'check_in',
    //     'latitude_in',
    //     'longitude_in',
    //     'photo_in',
    //     'check_out',
    //     'latitude_out',
    //     'longitude_out',
    //     'photo_out',
    //     'status_in',
    //     'status_out',
    //     'note_in',      // Baru
    //     'note_out',     // Baru
    //     'ip_address',   // Baru
    //     'user_agent',
    // ];
    protected $fillable = [
        'user_id',
        'date',

        'check_in',
        'latitude_in',
        'longitude_in',
        'photo_in',

        'check_out',
        'latitude_out',
        'longitude_out',
        'photo_out',

        'status_in',
        'status_out',

        'note_in',
        'note_out',

        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'date' => 'date',
    ];


    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    /**
     * Relasi Kebalikan: Satu data absen ini dimiliki oleh satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
