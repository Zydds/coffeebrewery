@extends('admin.layout')

@section('title', 'Add Coffee Bean')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Coffee Bean</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Add a new bean to the catalog.</p>
    </div>

    @include('admin.beans._form')
@endsection
