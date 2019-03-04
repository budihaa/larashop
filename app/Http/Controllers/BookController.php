<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $books = Book::with('categories');

        $keyword = $request->get('keyword');
        $status = $request->get('status');

        if ($keyword) {
            $books = $books->where('title', 'LIKE', "%$keyword%");
        }

        if ($status) {
            $books = $books->where('status', strtoupper($status));
        }

        $books = $books->paginate(10);

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $book = new Book();
        $book->title = $request->get('title');
        $book->description = $request->get('description');
        $book->author = $request->get('author');
        $book->publisher = $request->get('publisher');
        $book->price = $request->get('price');
        $book->stock = $request->get('stock');
        $book->status = $request->get('save_action');
        $book->slug = str_slug($request->get('title'));
        $book->created_by = \Auth::user()->id;
        $cover = $request->file('cover');
        if ($cover) {
            $cover_path = $cover->store('book-covers', 'public');
            $book->cover = $cover_path;
        }
        $book->save();

        # Relationship
        $book->categories()->attach($request->get('categories'));

        $status = $book->status === 'PUBLISH' ? 'Book successfully saved and published' : 'Book saved as draft';

        return redirect()->route('books.create')->with('status', $status);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $book = Book::findOrFail($id);

        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $book->title = $request->input('title');
        $book->slug = $request->input('slug');
        $book->description = $request->input('description');
        $book->author = $request->input('author');
        $book->publisher = $request->input('publisher');
        $book->stock = $request->input('stock');
        $book->price = $request->input('price');

        $cover = $request->file('cover');
        if ($cover) {
            if ($book->cover && file_exists(storage_path('app/public/' . $book->cover))) {
                Storage::delete('public/'. $book->cover);
            }
            $new_cover_path = $cover->store('book-covers', 'public');
            $book->cover = $new_cover_path;
       }

       $book->updated_by = Auth::user()->id;
       $book->status = $request->input('status');
       $book->save();

       $book->categories()->sync($request->input('categories'));

       return redirect()->route('books.edit', ['id'=>$book->id])->with('status', 'Book successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()->route('books.index')->with('status', "Book successfully moved to trash");
    }

    public function trash()
    {
        $books = Book::onlyTrashed()->paginate(10);

        return view('books.trash', ['books' => $books]);
    }

    public function restore($id)
    {
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();

        return redirect()->route('books.trash')->with('status', 'Book successfully restored');
    }

    public function PermanentDelete($id)
    {
        $book = Book::withTrashed()->findOrFail($id);

        $book->categories()->detach(); # Remove relation
        $book->forceDelete();

        return redirect()->route('books.trash')->with('status', 'Book permanently deleted!');
    }

}
