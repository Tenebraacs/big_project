@extends('layouts.app')

@section('content')
<div class="container-fluid px-0 px-md-3">

    <livewire:home-main-quest 
        :mainJar="$mainJar"
        :mainQuest="$mainQuest"
    />

    <div class="container-fluid px-0 px-md-3 mt-4">
    <livewire:proof-feed />
    </div>

    <div class="container-fluid px-0 px-md-3 mt-5">
        <div class="mb-5">
            <livewire:jar-carousel 
                :jars="$mostPopularJars"
                title="Most Popular Jars"
                wire:key="popular-jars-carousel" 
            />
        </div>

        <div class="mb-5">
            <livewire:jar-carousel 
                :jars="$mostLikedJars"
                title="Most Liked Jars"
                wire:key="liked-jars-carousel" 
            />
        </div>

        @auth
            <div class="mb-5">
                <livewire:jar-carousel 
                    :jars="$myJars"
                    title="My Jars"
                    wire:key="my-jars-carousel" 
                />
            </div>
        @endauth
    </div>

</div>
@endsection