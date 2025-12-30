<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
     protected $fillable=['name','image','space','address','numberOfRooms','Rent','description','city','user_id'];
     protected $casts = ['image' => 'array'];

     
     public function reservations()
{
    return $this->hasMany(Reservation::class);
}
public function user() { 
    return $this->belongsTo(User::class); }

}
