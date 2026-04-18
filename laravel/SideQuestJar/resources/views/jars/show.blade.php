@extends('layouts.app')

@section('content')
    <livewire:show-jar :jar="$jar" :quest="$quest ?? null" />
@endsection