@extends('layouts.master')
@section('title', 'review')
@section('content')
<div class="d-flex jus
<h2>Reviews for {{ $product->name }}</h2> @if($product->reviews->count() > 0) <ul> @foreach($product->reviews as $review) <li>{{ $review->content }} (by {{ $review->user->name }})</li> @endforeach </ul> @else <p>No reviews yet.</p> @endif