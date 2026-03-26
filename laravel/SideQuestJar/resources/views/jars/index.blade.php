@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col"> 
                <div class="row bg-block-blue p-4 align-item-center ">
                    <div class="col-md-6">
                        
                    </div>
                    <div class="col-md-6">
                        <div class="card block-radius">
                        <div class="card-header">
                            <h1>index</h1>
                    </div>

                        <div class="card-body form-text">
                            <form method="POST" action="">
                                @csrf

                                
                                
                            </form>
                        </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
