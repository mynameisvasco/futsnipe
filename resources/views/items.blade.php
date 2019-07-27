@extends('layouts.app')
@section("content")
<div class="modal" id="editItemModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
    
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Edit item</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="col-12 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <p class="text-center"><img id="editItemImg" src="" width="80%"></p>
                        </div>
                        <div class="col-md-7">
                            <form class="user" action="" id="editItemForm" method="POST"> 
                                {!! csrf_field() !!}
                                <input type="hidden" value="" id="item_id" name="id">
                                <h5><b>XBOX</b></h5>
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="ml-1"> Buy BIN</label>
                                        <input class="form-control form-control-user" type="number" value="" id="xbox_buy_bin" name="xbox_buy_bin">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ml-1">Sell BIN</label>
                                        <input class="form-control form-control-user" type="number" value="" id="xbox_sell_bin" name="xbox_sell_bin">
                                    </div>
                                </div>
                                <hr>
                                <h5><b>Playstation</b></h5>
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="ml-1"> Buy BIN</label>
                                        <input class="form-control form-control-user" type="number" value="" id="ps_buy_bin" name="ps_buy_bin">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ml-1">Sell BIN</label>
                                        <input class="form-control form-control-user" type="number" value="" id="ps_sell_bin" name="ps_sell_bin">
                                    </div>
                                </div>
                                <hr>
                                <h5><b>PC</b></h5>
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="ml-1"> Buy BIN</label>
                                        <input class="form-control form-control-user" type="number" value="" id="pc_buy_bin" name="pc_buy_bin">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ml-1">Sell BIN</label>
                                        <input class="form-control form-control-user" type="number" value="" id="pc_sell_bin" name="pc_sell_bin">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="text-right"><button class="btn btn-primary" type="submit">Save</button></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="newItemModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add items</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="col-12 mb-4">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="name-tab" data-toggle="tab" href="#name" role="tab" aria-controls="home" aria-selected="true">Search by name</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#nationality" role="tab" aria-controls="profile" aria-selected="false">Search by Nationality</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="league-tab" data-toggle="tab" href="#league" role="tab" aria-controls="contact" aria-selected="false">Search by League</a>
                        </li>
                    </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="name" role="tabpanel" aria-labelledby="name-tab">
                                <div class="col-12 mt-4">
                                    <div class="input-group">
                                        <input id="player_name" class="form-control form-control-user" oninput="updatePlayerList()" name="player_name" type="text" placeholder="Player name...">
                                        <div class="input-group-append">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div id="results_div_players" class="row">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nationality" role="tabpanel" aria-labelledby="nationality-tab">
                                <div class="col-12 mt-4">
                                    <div class="input-group">
                                        <input class="form-control form-control-user" oninput="updateNationalityList()" id="nationality_name" type="text" placeholder="Nationality...">
                                        <div class="input-group-append">
                                        </div>
                                    </div>
                                    <small class="ml-1 mt-1">This will add to items all players from certain nationality (useful when some sbc come out)</small>
                                </div>
                                <div class="col-12 mt-4">
                                    <div id="results_div_nationalities" class="row">
                                    </div>
                                </div>
                            </div>
                        <div class="tab-pane fade" id="league" role="tabpanel" aria-labelledby="league-tab">...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h3 class="text-dark mb-0">Items</h3>
            <button data-toggle="modal" data-target="#newItemModal" class="btn btn-primary btn-icon-split" type="button">
                <span class="text-white-50 icon"><i class="far fa-plus-square"></i></span>
                <span class="text-white text">Add items</span>
            </button>
        </div>
        <div class="row">
            @if(count($items) == 0)
                <div class="col">
                    <p>No items on database, please add some first.</p>
                </div>
            @else
                @foreach($items as $item)
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-around">
                                    <div style="margin-left:90%;" class="dropdown no-arrow">
                                        <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" type="button">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                                            <a class="dropdown-item" role="presentation" href="/items/{{$item->id}}/delete"> Remove</a>
                                            <a class="dropdown-item" role="presentation" onclick="editItem({{$item->id}})"> Edit</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-10 offset-md-1 pb-2" style="border-bottom:1px solid #EDF2F7;">
                                        <p class="text-center fifa-card"><img src="/storage/fut_cards/{{$item->asset_id}}.png" width="65%"></p>
                                    </div>
                                    <div class="col-md-12  mt-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-center mb-2"><b>Buy Price</b></h6>
                                                <p class="text-center mb-1" style="font-size:13px;"><i class="fab fa-xbox"></i> {{$item->xbox_buy_bin}}</p>
                                                <p class="text-center mb-1" style="font-size:13px;"><i class="fab fa-playstation"></i> {{$item->ps_buy_bin}}</p>
                                                <p class="text-center mb-1" style="font-size:13px;"><i class="fa fa-desktop"></i> {{$item->pc_buy_bin}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-center mb-2"><b>Sell Price</b></h6>
                                                <p class="text-center mb-1" style="font-size:13px;"><i class="fab fa-xbox"></i> {{$item->xbox_sell_bin}}</p>
                                                <p class="text-center mb-1" style="font-size:13px;"><i class="fab fa-playstation"></i> {{$item->ps_sell_bin}}</p>
                                                <p class="text-center mb-1" style="font-size:13px;"><i class="fa fa-desktop"></i> {{$item->pc_sell_bin}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <script>
        function editItem(id)
        {
            $("#editItemModal").modal()
            let itemToEdit = JSON.parse("{{$items}}".replace(/&quot;/g, '"'))
            item = itemToEdit.find(item => item.id === id)

            document.getElementById('editItemImg').src = "/storage/fut_cards/"+item['asset_id'] + '.png'
            document.getElementById('editItemForm').action = "/items/"+ item['id'] + "/update"
            document.getElementById('xbox_buy_bin').value = item['xbox_buy_bin']
            document.getElementById('ps_buy_bin').value = item['ps_buy_bin']
            document.getElementById('pc_buy_bin').value = item['pc_buy_bin']
            document.getElementById('xbox_sell_bin').value = item['xbox_sell_bin']
            document.getElementById('ps_sell_bin').value = item['ps_sell_bin']
            document.getElementById('pc_sell_bin').value = item['pc_sell_bin']
            document.getElementById('item_id').value = item['id']
        }

        var playerCardsJSON = ""
        function getPlayerCardsList(assetId)
        {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            } else {  // code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) 
                {
                    playerCardsJSON = JSON.parse(this.responseText)
                    console.log(playerCardsJSON)
                    generateCardImg()
                }
            }
            xmlhttp.open("GET","/items/player_cards/"+assetId,true);
            xmlhttp.send();
        }

        function generateCardImg()
        {
            token = document.querySelector('meta[name="csrf-token"]').content;

            for(var i = 0; i < playerCardsJSON['items'].length; i++)
            {
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp=new XMLHttpRequest()
                } else {  // code for IE6, IE5
                    new ActiveXObject("Microsoft.XMLHTTP")
                }
                xmlhttp.onreadystatechange=function() {
                    if (this.readyState==4 && this.status==200) 
                    {
                        showPlayerCardsList()
                    }
                }

                let playerName = playerCardsJSON['items'][i]['commonName'] != "" ? playerCardsJSON['items'][i]['commonName'] : playerCardsJSON['items'][i]['lastName']
                let params = `name=`+ playerName + 
                `&rating=`+ playerCardsJSON['items'][i]['rating'] +
                `&club=`+ playerCardsJSON['items'][i]['club']['id'] +
                `&assetId=`+ playerCardsJSON['items'][i]['baseId'] +
                `&nationality=`+ playerCardsJSON['items'][i]['nation']['id'] + 
                `&position=`+ playerCardsJSON['items'][i]['position'] + 
                `&rarityId=`+ playerCardsJSON['items'][i]['rarityId'] +
                `&definitionId=`+ playerCardsJSON['items'][i]['id']

                xmlhttp.open("POST","/items/card/generate", false)
                xmlhttp.setRequestHeader('X-CSRF-TOKEN', token)
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded")
                xmlhttp.send(params);
            }
        }

        function showPlayerCardsList()
        {
            resultsDiv = document.getElementById('results_div_players')
            resultsDiv.innerHTML = ""

            for(var i = 0; i < playerCardsJSON['items'].length; i++)
            {
                resultsDiv.innerHTML += 
                `
                <div class="fifa-card col-sm-4 col-md-3 mb-4">
                    <form action="/items/store" method="POST">
                        @csrf
                        <input type="hidden" name="item" value='`+ JSON.stringify(playerCardsJSON['items'][i]) +`'> 
                        <input type="image" src="storage/fut_cards/`+ playerCardsJSON['items'][i]['id'] + `.png" width="100%">
                    </form>
                </div>
                `
            }
        }


        var playersJSON = ""
        function getPlayerList()
        {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            } else {  // code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) 
                {
                    playersJSON = JSON.parse(this.responseText)
                }
            }
            xmlhttp.open("GET","/items/players",true);
            xmlhttp.send();
        }
        getPlayerList()
        function updatePlayerList()
        {
            player_name = document.getElementById('player_name').value.toLowerCase();
            console.log(player_name.length)
            if(player_name.length > 3)
            {
                document.getElementById("results_div_players").innerHTML="";
                
                var legendsPlayerTargets = []
                for(var i = 0; i < playersJSON['LegendsPlayers'].length; i++)
                {
                    if(playersJSON['LegendsPlayers'][i]['f'].toLowerCase().includes(player_name))
                    {
                        legendsPlayerTargets.push(playersJSON['LegendsPlayers'][i])
                    }
                    else if(playersJSON['LegendsPlayers'][i]['l'].toLowerCase().includes(player_name))
                    {
                        legendsPlayerTargets.push(playersJSON['LegendsPlayers'][i])
                    }
                    else if(playersJSON['LegendsPlayers'][i]['c'] != undefined)
                    {
                        if(playersJSON['LegendsPlayers'][i]['c'].toLowerCase().includes(player_name))
                        {
                            legendsPlayerTargets.push(playersJSON['LegendsPlayers'][i])
                        }
                    }
                }
                legendsPlayerTargets.sort(function(a, b){
                    return b.r - a.r;
                })
                for(var i = 0; i < legendsPlayerTargets.length; i++)
                {
                    if(legendsPlayerTargets[i]['c'] != null)
                    {
                        var display_name = legendsPlayerTargets[i]['c']
                    }
                    else
                    {
                        var display_name = legendsPlayerTargets[i]['f'] + " " +legendsPlayerTargets[i]['l']
                    }
                    document.getElementById("results_div_players").innerHTML += `
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-left-primary py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-12 mr-2">
                                        <div class="text-dark text-center font-weight-bold h5 mb-0"><span>`+ display_name +`</span></div>
                                        <div class="text-uppercase text-center text-primary font-weight-bold text-xs mb-1"><span class="text-primary">Rating `+ legendsPlayerTargets[i]['r']+` <i class="fa fa-star"></i></span></div>
                                    </div>
                                    <div class="col-12">
                                        <h6 class="text-center">
                                            <img class="rounded-circle" src="https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/mobile/portraits/`+ legendsPlayerTargets[i]['id']+`.png" width="80px">
                                        </h6>
                                    </div>
                                    <div class="col-12">
                                        <h6 class="text-center">
                                            <button type="submit" onclick="getPlayerCardsList(`+ legendsPlayerTargets[i]['id'] +`)" class="btn btn-primary"> Select</button>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `
                }
                var playerTargets = []
                for(var i = 0; i < playersJSON['Players'].length; i++)
                {
                    if(playersJSON['Players'][i]['f'].toLowerCase().includes(player_name))
                    {
                        playerTargets.push(playersJSON['Players'][i])
                    }
                    else if(playersJSON['Players'][i]['l'].toLowerCase().includes(player_name))
                    {
                        playerTargets.push(playersJSON['Players'][i])
                    }
                    else if(playersJSON['Players'][i]['c'] != undefined)
                    {
                        if(playersJSON['Players'][i]['c'].toLowerCase().includes(player_name))
                        {
                            playerTargets.push(playersJSON['Players'][i])
                        }
                    }
                    
                }
                playerTargets.sort(function(a, b){
                    return b.r - a.r;
                })
                for(var i = 0; i < playerTargets.length; i++)
                {
                    if(playerTargets[i]['c'] != null)
                    {
                        var display_name = playerTargets[i]['c']
                    }
                    else
                    {
                        var display_name = playerTargets[i]['f'] + " " + playerTargets[i]['l']
                    }
                    document.getElementById("results_div_players").innerHTML += `
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-left-primary py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-12 mr-2">
                                        <div class="text-dark text-center font-weight-bold h5 mb-0"><span>`+ display_name +`</span></div>
                                        <div class="text-uppercase text-center text-primary font-weight-bold text-xs mb-1"><span class="text-primary">Rating `+ playerTargets[i]['r']+` <i class="fa fa-star"></i></span></div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <h6 class="text-center">
                                            <img class="rounded-circle" src="https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/mobile/portraits/`+ playerTargets[i]['id']+`.png" width="80px">
                                        </h6>
                                    </div>
                                    <div class="col-12">
                                        <h6 class="text-center">
                                            <button type="submit" onclick="getPlayerCardsList(`+ playerTargets[i]['id'] +`)" class="btn btn-primary"> Select</button>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `
                }
            }
        }
        var nationalitiesJSON = ""
        function getNationalityList()
        {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            } else {  // code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) 
                {
                    nationalitiesJSON = JSON.parse(this.responseText)
                }
            }
            xmlhttp.open("GET","/items/nationalities",true);
            xmlhttp.send();
        }
        getNationalityList()
        function updateNationalityList()
        {
            document.getElementById("results_div_nationalities").innerHTML="";
            nationality_name = document.getElementById('nationality_name').value.toLowerCase();
            var nationalities = []
            if(nationality_name.length > 2)
            {
                for(var i = 0; i < nationalitiesJSON.length; i++)
                {
                    if(nationalitiesJSON[i]['nationality'].toLowerCase().includes(nationality_name))
                    {
                        nationalities.push(nationalitiesJSON[i])
                    }
                }
                
                for(var i = 0; i < nationalities.length; i++)
                {
                    document.getElementById("results_div_nationalities").innerHTML += `
                    <div class="col-md-4 mb-4">
                        <div class="card shadow border-left-primary py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-12 mr-2">
                                        <div class="text-uppercase text-center text-primary font-weight-bold text-xs mb-1">
                                            <img class="rounded" src="/flags/`+ nationalities[i]['nationality_id'] +`.png">
                                        </div>
                                        <div class="text-dark text-center font-weight-bold h5 mb-0 mt-2"><span>`+ nationalities[i]['nationality'] +`</span></div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <form action="/items/store" method="post">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="item" value='`+ JSON.stringify(nationalities[i]) +`'>
                                            <h6 class="text-center">
                                                <button type="submit" class="btn btn-primary"> Add Item</button>
                                            </h6>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `
                }
            }
        }
    </script>
@endsection