<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Confirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Http\Requests\SectionStoreRequest;
use App\Models\Item;
use App\Models\Section;

class ConfirmationController extends Controller
{
    public function store(Request $request, $id)
    {
    
        if(Confirmation::where('section', '=', $id)->first()) {
            Confirmation::where('section', '=', $id)
            ->update(['user' => Auth::user()->name,
            'created_at'  => Carbon::now()]);
        } else {
            $data = new Confirmation();
        $data->section = $id;
        $data->user = Auth::user()->name;
        $data->created_at = Carbon::now();
        $data->save();
        }
        
        
        $update_details = array(
            'obligatory' => 1,
            'confirmed_by'  => Auth::user()->name
        );

        return Item::where('section_id', $id)
                ->update(['obligatory' => 1,
                'confirmed_by'  => Auth::user()->name]);
    
    }
    public function show(Request $request, $id)
    {
        $confirmee = Confirmation::where('section', $id)->get();
        $a = $confirmee[0]->user;
        return response()->json($confirmee);
    
    }
}
