@extends('admin.layout')

@section('title', 'Edit Coffee Bean')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Edit Coffee Bean</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Update this bean's details.</p>
    </div>

    @include('admin.beans._form')
@endsection
