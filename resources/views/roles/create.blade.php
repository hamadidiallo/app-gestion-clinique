@extends('layout')
@section('title','Creation Role')
@section('content')
    <section>
        <h1>Création Rôle</h1>
        <form action="{{route('roles.store')}}" method="post">
            @csrf
            <x-form.input type='text' name='nom' value="{{old('nom')}}" label="Nom : " />
            <x-form.input type='textarea' name='description' value="{{old('description')}}" label="Description : " />
            <button class="btn btn-outline-warning">Créer</button>
        </form>
    </section>
@endsection
