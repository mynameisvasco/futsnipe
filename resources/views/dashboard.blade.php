@extends('layouts.app')

@section('content')
<div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark mb-0">Dashboard</h3>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-left-primary py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <div class="text-uppercase text-primary font-weight-bold text-xs mb-1"><span>Total transactions</span></div>
                                <div class="text-dark font-weight-bold h5 mb-0"><span>{{$totalTransactions}}</span></div>
                            </div>
                            <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-left-success py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <div class="text-uppercase text-success font-weight-bold text-xs mb-1"><span>Total earnings</span></div>
                                <div class="text-dark font-weight-bold h5 mb-0"><span>{{$totalEarnings}}</span></div>
                            </div>
                            <div class="col-auto"><i class="fas fa-coins fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-left-info py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <div class="text-uppercase text-info font-weight-bold text-xs mb-1"><span>Items in snipe list</span></div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="text-dark font-weight-bold h5 mb-0 mr-3"><span>{{$totalItems}}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-left-warning py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <div class="text-uppercase text-warning font-weight-bold text-xs mb-1"><span>Accounst</span></div>
                                <div class="text-dark font-weight-bold h5 mb-0"><span>{{$totalAccounts}}</span></div>
                            </div>
                            <div class="col-auto"><i class="fas fa-user-lock fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-lg-7 col-xl-8 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-primary font-weight-bold m-0">Earnings Overview</h6>
                    </div>
                    <div class="card-body">
                        <div id="wrapper" style="position: relative; height: 50vh">
                            <canvas id="earningsChart" width="50"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-xl-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-primary font-weight-bold m-0">Latest Transactions</h6>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <div class="row mt-3 align-items-center">
                                <script>
                                    function getNPrevDate(prevDays)
                                    {
                                        var prev_date = new Date();
                                        prev_date.setDate(prev_date.getDate() - prevDays);
                                        return prev_date.toISOString().split('T')[0];
                                    }

                                    var days = [getNPrevDate(5), getNPrevDate(4), getNPrevDate(3), getNPrevDate(2), getNPrevDate(1), getNPrevDate(0)]
                                    var coinsBalance = [0,0,0,0,0,0]
                                    var k = 5
                                </script>
                                @foreach($stats as $stat)
                                    <script>
                                        coinsBalance[k] = "{{$stat->coins_balance}}"
                                        k -= 1;
                                    </script>
                                @endforeach
                                @if(count($transactions) > 0 )
                                    @foreach($transactions as $transaction)
                                        <div class="col-12 col-md-6 col-lg-6 b-2 mb-4">
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
                                            <p id="transaction{{$transaction->id}}Time" class="text-center" style="font-size:12px;" ></p>
                                            @if($transaction->type == 'Buy') 
                                                <p class="text-danger text-center"><i class="fa fa-coins"></i> - {{$transaction->coins}}</p>
                                                
                                            @else 
                                                <p class="text-success text-center"><i class="fa fa-coins"></i> + {{$transaction->coins}}</p>

                                            @endif
                                            <script>
                                                var date = new Date("{{$transaction->created_at}}")
                                                document.getElementById('transaction{{$transaction->id}}Time').innerHTML = timeSince(date) + " ago"
                                            </script>
                                        </div>                                        
                                    @endforeach
                                @else
                                    <p>No transactions yet.</p>
                                @endif
                            </div>                             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
            
        Chart.defaults.scale.gridLines.display = false;
        Chart.defaults.global.legend.display = false;
        new Chart(document.getElementById("earningsChart"), {
            type: 'line',
            data: {
                labels: days,
                datasets: [{ 
                    data: coinsBalance,
                    label: "Coins",
                    borderColor: "#22C88A",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                display: true,
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            padding: 10,

                        }
                    }],
                }
            }
        });

    </script>
@endsection
