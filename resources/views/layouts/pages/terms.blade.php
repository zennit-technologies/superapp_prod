@extends('layouts.auth')
@section('title', __('Terms & Condition'))
@section('content')
<div class="w-10/12 mx-auto my-10">
    {!! setting('terms', "") !!}
</div>
@endsection
