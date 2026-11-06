@extends('layouts.app')

@section('content')

<h1 class="text-2xl mb-3">Slow down!</h1>
<p class="mb-4">You can only add 3 reviews per hour. Please try again later.</p>
<a href="{{ route('books.index') }}" class="reset-link">Back to books</a>

@endsection
