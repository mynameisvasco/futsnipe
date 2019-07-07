@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark mb-0">Transactions</h3>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card shadow py-2">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-3">
                                <p class="text-primary font-weight-bold"><i class="fa fa-user"></i> Account</p>
                            </div>
                            <div class="col-3">
                                <p class="text-primary font-weight-bold"><i class="fa fa-coins"></i> Coins</p>
                            </div>
                            <div class="col-3">
                                <p class="text-primary font-weight-bold"><i class="fa fa-box"></i> Item</p>
                            </div>
                            <div class="col-3">
                                <p class="text-primary font-weight-bold"><i class="fa fa-calendar"></i> Date</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-0 pt-1">
                        @if(count($transactions) == 0)
                        <div class="col-12 mt-4">
                            <p>No transactions yet, try to buy or sell an item first.</p>
                        </div>
                        @else
                            @foreach($transactions as $transaction)
                            <div class="row mt-3">
                                <div class="col-3">
                                    <p class="ml-2">{{$transaction->account->email}}</p>
                                </div>
                                <div class="col-3">
                                    @if($transaction->type == 'Buy') <p class="text-danger">- {{$transaction->coins}}</p>
                                    @else <p class="text-success">+ {{$transaction->coins}}</p>
                                    @endif
                                </div>
                                <div class="col-3">
                                    <p>{{$transaction->name}}</p>
                                </div>
                                <div class="col-3">
                                    <p>{{$transaction->created_at}}</p>
                                </div>
                            </div>
                            <hr style="background-color:#ECEDF0; height:1px; border:0; width:100%;" class="mt-0">
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>  
@endsection