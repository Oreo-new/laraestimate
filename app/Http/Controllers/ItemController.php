<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
class ItemController extends Controller
{
    public function update(Request $request, $id)
    {
        $data = Item::where('id', $id)->first();
        $data->confirmed_by = $request->get('confirmed_by');
        $data->obligatory = 1;
        $data->save();
        return response()->json(true);
    }
}
