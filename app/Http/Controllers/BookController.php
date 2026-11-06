<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

use function PHPSTORM_META\map;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter','');

        $books = Book::when($title,
            fn(Builder $query,$title) => $query->title($title)
        );

        $books = match($filter){
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_six_months' => $books->popularLastSixMonths(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_six_months' => $books->highestRatedLastSixMonths(),
            default => $books->latest()->withReviewsCount()->withAverageRating()
        };

        $cacheKey = 'books:'. $filter . ':' . $title;
        // $books = $books->get();
        $books = cache()->remember($cacheKey,3600,function() use($books){
            // dd("This page is not cached yet");
            return $books->get();
        });

        return View('books.index',['books'=>$books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cacheKey = 'books:'. $id;
        $book = cache()->remember(
            $cacheKey,
            3600,
            fn()=>
            Book::with('reviews')->withReviewsCount()->withAverageRating()->findOrFail($id));

        return view('books.show',[
            'book'=>$book
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
