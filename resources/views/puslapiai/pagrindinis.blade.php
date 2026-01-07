@extends('layouts.app')

@section('content')
<h1>Auto renginiai</h1>
<p class="muted">Frontend: Blade + HTML+JS. Duomenys imami iš REST API.</p>

<div class="card">
    <h3>Greitos nuorodos</h3>
    <ul>
        <li><a href="{{ route('renginiai') }}">Renginių sąrašas</a></li>
        <li><a href="{{ route('prisijungti') }}">Prisijungti</a></li>
        <li><a href="{{ route('registruotis') }}">Registruotis</a></li>
        <li><a href="{{ route('xml') }}">XML eksportas</a></li>
    </ul>
</div>
@endsection