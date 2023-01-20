<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{   
    /* Get All Users */
    public function getAllUsers(){
        $users = User::get();
        foreach($users as $user){
            $user["createdAt"] = $user->created_at->format('M d, Y - h:s A');
            $user["updatedAt"] = $user->updated_at->format('M d, Y - h:s A');
        }
        return response()->json($users, 200);
    }

    /* Update User Data */
    public function updateUser($id, Request $request){
        $user = $this->requestDataForUser($request);

        User::where("id", $id)->update($user);
        $data = User::where("id", $id)->first();
        $data["createdAt"] = $data->created_at->format('M d, Y - h:s A');
        $data["updatedAt"] = $data->updated_at->format('M d, Y - h:s A');
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
    public function changeRole($id, Request $request){
        User::where("id", $id)->update(["role"=> $request->newRole]);
        $user = User::where("id", $id)->first();
        $user["createdAt"] = $user->created_at->format('M d, Y - h:s A');
        $user["updatedAt"] = $user->updated_at->format('M d, Y - h:s A');
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
