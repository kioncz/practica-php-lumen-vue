<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    //
    protected $table = 'tickets';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function getTicketsByUserId($userId)
    {
        return $this->where('user_id', $userId)->get();
    }


    public function getTicketsByStatus($status)
    {
        return $this->where('status', $status)->get();
    }

    public function getAllTickets()
    {
        return $this->select('title', 'description', 'priority', 'status')
            ->get();
    }
}
