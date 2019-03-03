<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::paginate(10);

        $filterKeyword = $request->get('name');

        if ($filterKeyword) {
             $categories = Category::where("name", "LIKE", "%$filterKeyword%")->paginate(10);
        }

        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Search category in create book page
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->get('q');
        $categories = Category::where("name", "LIKE", "%$keyword%")->get();

        return $categories;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');

        $category = new Category();
        $category->name = $name;

        if ($request->file('image')) {
            $filename = $request->file('image')->store('categories', 'public');
            $category->image = $filename;
        }

        $category->created_by = Auth::user()->id;
        $category->slug = str_slug($name, '-');
        $category->save();

        return redirect()->route('categories.create')->with('status', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return view('categories.show', ['category' => $category]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $name = $request->get('name');

        $category = Category::findOrFail($id);

        $category->name = $name;

        if ($request->file('image')) {
            if ($category->image && file_exists(storage_path('app/public/' . $category->image))) {
                Storage::delete('public/' . $category->name);
            }

            $filename = $request->file('image')->store('category_images', 'public');
            $category->image = $filename;
        }

        $category->updated_by = Auth::user()->id;
        $category->slug = str_slug($name);
        $category->save();

        return redirect()->route('categories.edit', ['id' => $id])->with('status', 'Category successfully updated.');
    }

    /**
     * Move the specified resource from storage to trash.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = \App\Category::findOrFail($id);

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Category successfully moved to trash');
    }

    /**
     * Show deleted category in trash.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        $deletedCategories = Category::onlyTrashed()->paginate(10);

        return view('categories.trash', ['categories' => $deletedCategories]);
    }

    /**
     * Restore deleted category from trash.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('categories.index')->with('status', 'Category successfully restored');
    }

    /**
     * Permanent delete on selected category
     *
     * @return \Illuminate\Http\Response
     */
    public function permanentDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()->route('categories.index')->with('status', 'Category permanently deleted');
    }

}
