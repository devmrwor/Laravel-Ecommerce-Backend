<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /* Create New Category */
    public function createCategory(Request $request){
        $newCategory = Category::create(['title' => $request->title]);
        $data = Category::where('id', $newCategory->id)->first();
        return response()->json($data, 200);
    }

    /* Get All Categories */
    public function getAllCategories(){
        $categories = Category::when(request('searchKey'), function($query){
                                    $query->orWhere('title', 'like', '%'.request('searchKey').'%');
                                })
                                ->get();
        return response()->json($categories, 200);
    }

    public function takeCategories(){
        $categories = Category::select('id', 'title')->get();
        return response()->json($categories, 200);
    }

    /* Delete Category */
    public function deleteCategory($id){
        Category::where('id', $id)->delete();
        return response()->json([
            'status' => 'delete success'
        ], 200);
    }

    /* Take Category To Edit */
    public function takeDataToEdit($id){
        $category = Category::where('id', $id)->first();
        return response()->json($category, 200);
    }

    /* Update Category */
    public function updateCategory($id, Request $request){
        Category::where('id', $id)->update([
            'title' => $request->title
        ]);

        $data = Category::where('id', $id)->first();
        return response()->json($data, 200);
    }
}
