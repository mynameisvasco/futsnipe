@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark mb-0">Transactions</h3>
        </div>
        <div class="row">
            @if(count($transactions) == 0)
                <div class="col">
                    <p>No transactions yet, try to buy or sell an item first.</p>
                </div>
            @else
                @foreach($transactions as $transaction)
                <div class="col-12 col-sm-6 col-lg-6 col-xl-2 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="col-md-10 offset-md-1 mt-4" style="border-bottom:1px solid #EDF2F7;">
                                <p class="text-center fifa-card"><img src="/storage/fut_cards/{{$transaction->asset_id}}.png" width="75%"></p>
                            </div>
                            <div class="col-md-10 offset-md-1 mt-4">
                                <div class="mb-2">
                                    @if($transaction->type == 'Buy') <p class="text-center text-danger"><i class="fa fa-coins"></i> -{{$transaction->coins}}</p>
                                        @else <p class="text-center text-success"><i class="fa fa-coins"></i> +{{$transaction->coins}}</p>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <p class="text-center" id="transaction{{$transaction->id}}"></p>
                                    <script>
                                        var date = new Date("{{$transaction->created_at}}")
                                        document.getElementById('transaction{{$transaction->id}}').innerHTML = timeSince(date) + " ago"
                                    </script>
                                </div>
                                <div class="mb-2">
                                    <p class="text-center">{{$transaction->account->email}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.js"></script>
@endsection