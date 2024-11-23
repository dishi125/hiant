<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
   protected $table = 'cards';
   protected $fillable = [
      'start','end', 'card_number', 'created_at','updated_at'
   ];

   const Level_400 = 400;
}
