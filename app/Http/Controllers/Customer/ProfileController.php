<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Get My Profile Data
    public function getProfileData() {
        $data = Auth::user();
        return response()->json(['user' => $data]);
    }
}
