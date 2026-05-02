@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-10">
                <div class="card rounded-5 bg-block-blue m-3">
                    <div class="text-center card-body border-0">

                        {{-- Header (for viewed user) --}}
                        <livewire:user.profile-header :user="$user" />

                        <div class="row m-2">
                            <div class="col-12 col-lg-12 col-xl-12">
                                {{-- optional: put some “View only” text or buttons here --}}
                            </div>
                        </div>

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
                title="Jars"
                wire:key="show-user-jars-carousel"
            />
        @endif
    </div>

    <div id="joined-jars" class="container-fluid mt-5">
        @if($joinedJars->isEmpty())
            <p class="text-center text-light">This user hasn’t joined any jars yet.</p>
        @else
            <livewire:jar-carousel
                :jars="$joinedJars"
                title="Joined Jars"
                wire:key="show-user-joined-jars-carousel"
            />
        @endif
    </div>
@endsection