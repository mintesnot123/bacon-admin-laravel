<?php

namespace App;
   
use App\Medicine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
    
class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Medicine([
            'name'     => $row['name'],
            'detail'    => $row['detail'], 
            'posted_by'    => Auth::user()->id, 
            'status'    => 1, 
         
        ]);
    }
}