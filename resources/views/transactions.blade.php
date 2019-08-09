@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{env('APP_URL')}}/foopicker.css">
    <script src="{{env('APP_URL')}}/foopicker.js"></script>
    
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
                 <div class="col-md-12 mb-4">
                     <form method="get" action="/transactions/date" autocomplete="off">
                        <div class="row">
                            <div class="col-md-3">
                                <form autocomplete="off">
                                    <input name="date" type="search" id="datepicker" class="form-control" placeholder="Search by date">
                                </form>
                                <script>
                                    var foopicker = new FooPicker({
                                        id: 'datepicker',
                                        dateFormat: 'yyyy/MM/dd'
                                    });
                                </script>
                            </div>
                            <div class="col-md-3 mt-2">
                                <button type="submit" id="dateSearchBtn" class="btn btn-primary"> Search</a>
                            </div>
                        </div>
                    </form>
                </div>
                @foreach($transactions as $transaction)
                <div class="col-12 col-sm-6 col-lg-6 col-xl-3 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="col-md-10 offset-md-1 mt-4" style="border-bottom:1px solid #EDF2F7;">
                                <div class="fifa-card mb-4 {{$transaction->fifaCard->type}}">
                                    @if($transaction->fifaCard->club == 0 && $transaction->fifaCard->nationality == 0 && $transaction->fifaCard->position == "")
                                        <div class="card-face-consumable">
                                            <div class="card-face-inner">
                                                <img src="/assets/consumables/{{$transaction->fifaCard->asset_id}}.png">
                                            </div>
                                        </div>
                                    @else
                                        <div class="card-face">
                                            <div class="card-face-inner">
                                                <img src="{{env('EA_PLAYERS_PIC')}}/{{$transaction->fifaCard->asset_id}}.png">
                                            </div>
                                        </div>
                                    @endif
                                    @if($transaction->fifaCard->club != 0)
                                        <div class="card-badge">
                                            <img src="{{env('EA_CLUB_BADGE')}}/{{$transaction->fifaCard->club}}.png" alt="Badge">
                                        </div>
                                    @endif
                                    <div class="card-rating">@if($transaction->fifaCard->rating > 0){{$transaction->fifaCard->rating}}@endif</div>
                                    <div class="card-position">{{$transaction->fifaCard->position}}</div>
                                    <div class="card-name">{{$transaction->fifaCard->name}}</div>
                                    @if($transaction->fifaCard->nationality != 0)
                                    <div class="card-flag">
                                        <img src="/flags/{{$transaction->fifaCard->nationality}}.png" alt="Nation">
                                    </div>
                                    @endif
                                </div>  
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
                <div class="col-md-12 mb-5 mt-4">
                    {{ $transactions->appends(request()->input())->links() }}
                </div>
            @endif
        </div>
    </div> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.js"></script>
@endsection