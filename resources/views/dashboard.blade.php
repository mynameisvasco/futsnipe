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
                                <div class="text-uppercase text-primary font-weight-bold text-xs mb-1"><span>TOTAL TRANSACTIONS</span></div>
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
                                <div class="text-uppercase text-success font-weight-bold text-xs mb-1"><span>TOTAL EARNINGS</span></div>
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
                                <div class="text-uppercase text-warning font-weight-bold text-xs mb-1"><span>ACCOUNTS</span></div>
                                <div class="text-dark font-weight-bold h5 mb-0"><span>{{$totalAccounts}}</span></div>
                            </div>
                            <div class="col-auto"><i class="fas fa-user-lock fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7 col-xl-8">
                <div class="card shadow mb-4">
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
                        @if(count($transactions) > 0 )
                        <script>
                            function getNPrevDate(prevDays)
                            {
                                var prev_date = new Date();
                                prev_date.setDate(prev_date.getDate() - prevDays);
                                return prev_date.toISOString().split('T')[0];
                            }

                            var days = [getNPrevDate(5), getNPrevDate(4), getNPrevDate(3), getNPrevDate(2), getNPrevDate(1), getNPrevDate(0)]
                            var coinsBalance = [0,0,0,0,0,0]
                        </script>
                        @php $transactionLimit = 0; @endphp
                        @foreach($transactions as $transaction)
                            @if($transactionLimit >= 5)
                                @if($transaction->type == 'Buy') 
                                    <script>
                                        if("{{$transaction->created_at}}".includes(days[0]))
                                        {
                                            coinsBalance[0] -= {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[1]))
                                        {
                                            coinsBalance[1] -= {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[2]))
                                        {
                                            coinsBalance[2] -= {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[3]))
                                        {
                                            coinsBalance[3] -= {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[4]))
                                        {
                                            coinsBalance[4] -= {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[5]))
                                        {
                                            coinsBalance[5] -= {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[6]))
                                        {
                                            coinsBalance[6] -= {{$transaction->coins}}
                                        }
                                    </script>
                                @else 
                                    <script>
                                        if("{{$transaction->created_at}}".includes(days[0]))
                                        {
                                            coinsBalance[0] += {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[1]))
                                        {
                                            coinsBalance[1] += {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[2]))
                                        {
                                            coinsBalance[2] += {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[3]))
                                        {
                                            coinsBalance[3] += {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[4]))
                                        {
                                            coinsBalance[4] += {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[5]))
                                        {
                                            coinsBalance[5] += {{$transaction->coins}}
                                        }
                                        else if("{{$transaction->created_at}}".includes(days[6]))
                                        {
                                            coinsBalance[6] += {{$transaction->coins}}
                                        }
                                    </script>
                                @endif
                            @else
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <p>{{$transaction->name}}</p>
                                    </div>
                                    <div class="col-3">
                                        @if($transaction->type == 'Buy') 
                                            <p class="text-danger">- {{$transaction->coins}}</p>
                                            <script>
                                                if("{{$transaction->created_at}}".includes(days[0]))
                                                {
                                                    coinsBalance[0] -= {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[1]))
                                                {
                                                    coinsBalance[1] -= {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[2]))
                                                {
                                                    coinsBalance[2] -= {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[3]))
                                                {
                                                    coinsBalance[3] -= {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[4]))
                                                {
                                                    coinsBalance[4] -= {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[5]))
                                                {
                                                    coinsBalance[5] -= {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[6]))
                                                {
                                                    coinsBalance[6] -= {{$transaction->coins}}
                                                }
                                            </script>
                                        @else 
                                            <p class="text-success">+ {{$transaction->coins}}</p>
                                            <script>
                                                if("{{$transaction->created_at}}".includes(days[0]))
                                                {
                                                    coinsBalance[0] += {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[1]))
                                                {
                                                    coinsBalance[1] += {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[2]))
                                                {
                                                    coinsBalance[2] += {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[3]))
                                                {
                                                    coinsBalance[3] += {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[4]))
                                                {
                                                    coinsBalance[4] += {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[5]))
                                                {
                                                    coinsBalance[5] += {{$transaction->coins}}
                                                }
                                                else if("{{$transaction->created_at}}".includes(days[6]))
                                                {
                                                    coinsBalance[6] += {{$transaction->coins}}
                                                }
                                            </script>
                                        @endif
                                    </div>
                                    <div class="col-3">
                                        <p id="transaction{{$transaction->id}}"></p>
                                        <script>
                                            var date = new Date("{{$transaction->created_at}}")
                                            document.getElementById('transaction{{$transaction->id}}').innerHTML = timeSince(date) + " ago"
                                        </script>
                                    </div>
                                </div>
                                <hr style="background-color:#ECEDF0; height:1px; border:0; width:100%;" class="mt-0">
                                @php $transactionLimit += 1; @endphp
                            @endif
                        @endforeach
                        @else
                            <p>No transactions yet.</p>
                        @endif
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
