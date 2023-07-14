<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /* Get All Users */
    public function getAllAdmins(){
        $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->get();

        for($i = 0; $i < count($admins); $i++){
            $admins[$i]["createdAt"] = $admins[$i]->created_at->diffForHumans();
            $admins[$i]["updatedAt"] = $admins[$i]->updated_at->diffForHumans();
        }

        return response()->json($admins, 200);
    }

    /* Get All Customers */
    public function getAllCustomers(){
        $customers = User::when(request('searchKey'), function($query){
                                $query->orWhere('name', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('email', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('phone', 'like', '%'.request('searchKey').'%');
                            })
                            ->whereRole('customer')
                            ->orderBy('created_at', 'desc')
                            ->paginate(5);

        // for($i = 0; $i < count($customers["data"]); $i++){
        //     $customers["data"][$i]["createdAt"] = $customers["data"][$i]->created_at->diffForHumans();
        //     $customers["data"][$i]["updatedAt"] = $customers["data"][$i]->updated_at->diffForHumans();
        // }

        return response()->json($customers, 200);
    }

    /* Get My Profile */
    public function getMyProfile($id){
        $myProfile = User::find($id);

        return response()->json($myProfile, 200);
    }

    /* Update User Data */
    public function updateUser($id, Request $request){
        $user = $this->requestDataForUser($request);

        User::where("id", $id)->update($user);
        $data = User::find($id);
        return response()->json($data, 200);
    }

    /* Delete User Account */
    public function deleteUser($id){
        $oldData = User::where("id", $id)->first();
        $dbImage = $oldData->image;

        if($dbImage != null){
            Storage::delete('public/'.$dbImage);
        }

        User::where("id", $id)->delete();
        return response()->json(["status" => "Delete success"], 200);
    }

    /* Change User Role */
    public function changeRole(Request $request){
        User::where("id", $request->id)->update(["role"=> $request->newRole]);
        $user = User::where("id", $request->id)->first();
        $user["createdAt"] = $user->created_at->diffForHumans();
        $user["updatedAt"] = $user->updated_at->diffForHumans();
        return response()->json($user, 200);
    }

    /* Change User Password */
    public function changePassword(Request $request){
        $user = User::where("id", $request->id)->first();

        if(!Hash::check($request->oldPassword, $user->password)){
            return response()->json([
                "message" => "The old password you entered is not correct."
            ]);
        }

        User::where("id", $request->id)->update([
            'password' => Hash::make($request->newPassword)
        ]);

        return response()->json([
            "message" => "success"
        ]);
    }

    /* Request Data For User Data Update */
    private function requestDataForUser($request){
        return [
            "name" => $request->name,
            "email" => $request->email,
            "role" => $request->role,
            "phone" => $request->phone
        ];
    }
}
