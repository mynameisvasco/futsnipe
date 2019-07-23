@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark mb-0">Configurations</h3>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card shadow py-2">
                    <div class="card-body mt-1">
                        <form class="user" action="/configurations/{{auth()->user()->id}}/save" method="POST">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="ml-2">Requests per minute (EA Servers)</label>
                                    <input name="rpm" value="{{$configuration->rpm}}" class="form-control form-control-user" type="number">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="ml-2">Snipe cooldown (minutes)</label>
                                    <input name="snipe_cooldown" value="{{$configuration->snipe_cooldown}}" class="form-control form-control-user" type="number">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="ml-2">Price update cooldown (minutes)</label>
                                    <input name="price_update_cooldown" value="{{$configuration->price_update_cooldown}}" class="form-control form-control-user" type="number">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="ml-2">Buy percentage</label>
                                    <input name="buy_percentage" value="{{$configuration->buy_percentage}}" class="form-control form-control-user" type="number">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="ml-2">Sell percentage</label>
                                    <input name="sell_percentage" value="{{$configuration->sell_percentage}}" class="form-control form-control-user" type="number">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 mt-3">
                                    <button class="btn btn-primary btn-block text-white btn-user" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection