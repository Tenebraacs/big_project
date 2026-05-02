@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mb-3">
        <div class="col-10">
            <div class="card rounded-5 bg-block-blue m-3">
                <div class="text-center card-body border-0">

                    {{-- Reuse the same header UI as profile page --}}
                    <livewire:user.profile-header :user="$user" />

                </div>
            </div>
        </div>
    </div>
</div>

<div id="my-jars" class="container-fluid mt-5">
    @if($myJars->isEmpty())
        <p class="text-center text-light">This user doesn’t have any jars yet.</p>
    @else
        <livewire:jar-carousel
            :jars="$myJars"
            title="Jars by {{ $user->username ? '@'.$user->username : $user->name }}"
            wire:key="user-show-my-jars-carousel-{{ $user->id }}"
        />
    @endif
</div>

<div id="joined-jars" class="container-fluid mt-5">
    @if($joinedJars->isEmpty())
        <p class="text-center text-light">This user hasn’t joined any jars yet.</p>
    @else
        <livewire:jar-carousel
            :jars="$joinedJars"
            title="Joined jars"
            wire:key="user-show-joined-jars-carousel-{{ $user->id }}"
        />
    @endif
</div>
@endsection