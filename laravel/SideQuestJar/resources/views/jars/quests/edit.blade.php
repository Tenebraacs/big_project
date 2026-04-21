@extends('layouts.app')

@section('content')

    <livewire:edit-quest :jar="$jar" :jarQuest="$jarQuest" />

@endsection