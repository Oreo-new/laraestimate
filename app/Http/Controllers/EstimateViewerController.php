<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Mail\EstimateLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class EstimateViewerController extends Controller
{
    public function getData(Request $request, Estimate $estimate)
    {
        return response()->json($estimate->load('sections'));
    }
    public function getUser(Request $request, Estimate $estimate)
    {
        $user = $estimate->user;
        $username = User::find($user);
        return response()->json($username);
    }

    public function show(Request $request, Estimate $estimate)
    {
        return view('estimates.show', compact('estimate'));
    }

    public function share(Request $request, Estimate $estimate)
    {
        $data = $request->validate([
            'email' => 'email|required'
        ]);

        Mail::to($data['email'])->send(
            new EstimateLink($estimate)
        );

        return response()->json(true);
    }
}
