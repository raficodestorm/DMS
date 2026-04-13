@extends('layouts.userlayout')

<x-auth-session-status class="mb-4" :status="session('status')" />

@section('content')

@endsection