@extends('layouts.app')

@section('content')

<h1 class="text-size-2xl mb-3">Books</h1>

<form action="{{ route('books.index') }}" method="GET" class="flex space-x-2 ">
    <input type="text" name="title" value="{{ request('title') }}" class="mb-3 border-gray-400 rounded-sm w-full">
    <input type="hidden" name="filter" value="{{ request('filter') }}">
    <button type="submit" class="btn">Search</button>
    <a href="{{ route('books.index') }}" class="btn">Cancel</a>
</form>

@php
    $filters = [
        '' => 'Latest',
        'popular_last_month'=>'Popular Last Month',
        'highest_rated_last_month'=>'Highest Rated Last Month',
        'popular_last_six_months'=>'Popolar Last Six Months',
        'highest_rated_last_six_months'=>'Highest Rated Last Six Months'
    ];
@endphp

<div class="filter-container flex mb-3">
    @foreach ($filters as $key=>$label)
        <a href="{{ route('books.index',[...request()->query(),'filter'=>$key]) }}" class={{ request('filter') === $key || ( request('filter') === null && $key === '')  ? "filter-item-active" : "filter-item" }}>{{ $label }}</a>
    @endforeach
</div>

<ul>
    @forelse ($books as $book)
        <li class="mb-4">
            <div class="book-item">
                <div
                class="flex flex-wrap items-center justify-between">
                <div class="w-full grow sm:w-auto">
                    <a href="{{ route('books.show',$book) }}" class="book-title">{{ $book->title }}</a>
                    <span class="book-author">{{ $book->author }}</span>
                </div>
                <div>
                    {{-- <div class="book-rating">
                        {{ number_format($book->reviews_avg_rating,1) }}
                    </div> --}}
                    <x-star-rating :rating="$book->reviews_avg_rating" />
                    <div class="book-review-count">
                    out of {{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }}
                    </div>
                </div>

                </div>
            </div>
        </li>
    @empty
        <li class="mb-4">
            <div class="empty-book-item">
                <p class="empty-text">No books found</p>
                <a href="{{ route('books.index') }}" class="reset-link">Reset criteria</a>
            </div>
        </li>
    @endforelse
</ul>

@endsection
