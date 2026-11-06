@extends('layouts.app')

@section('content')

<h1 class="mb-4 text-2xl">Add review for the {{ $book->title }}</h1>

<form action="{{ route('books.reviews.store',['book'=>$book]) }}" method="post">
    @csrf
    <label for="review">Review:</label>
    <input type="text" class="input mb-4" name="review">
    <label for="rating">Rating</label>
    <select name="rating" id="rating" class="input mb-4">
        <option value="">Select an Rating</option>
        @for ($i=1;$i<=5;$i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
    <button type="submit" class="btn">Add a review</button>
</form>

@endsection
