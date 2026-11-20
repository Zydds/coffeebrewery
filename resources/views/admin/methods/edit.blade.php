@extends('admin.layout')

@section('title', 'Edit Brew Method')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Edit Brew Method</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Update this brewing method.</p>
    </div>

    @include('admin.methods._form')
@endsection
