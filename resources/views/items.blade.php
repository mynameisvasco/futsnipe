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
                        <div class="col-md-6">
                            <div id="edit-card-bg" class="fifa-card mb-4">
                                <div class="card-face">
                                    <div class="card-face-inner">
                                        <img src="" id="edit-card-assetId">
                                    </div>
                                </div>
                                <div class="card-badge">
                                    <img src="" id="edit-card-club" alt="Badge">
                                </div>
                                <div class="card-flag">
                                    <img src="" id="edit-card-nationality" alt="Nation">
                                </div>
                                <div class="card-rating" id="edit-card-rating"></div>
                                <div class="card-name" id="edit-card-name"></div>
                                <div class="card-position" id="edit-card-position"></div>
                            </div>  
                        </div>
                        <div class="col-md-6">
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
                            <a class="nav-link active" id="player-tab" data-toggle="tab" href="#player" role="tab" aria-controls="home" aria-selected="true">Players</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="consumable-tab" data-toggle="tab" href="#consumables" role="tab" aria-controls="profile" aria-selected="false">Consumables</a>
                        </li>
                    </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="player" role="tabpanel" aria-labelledby="player-tab">
                                <div class="col-12 mt-4">
                                    <input id="player_name" class="form-control form-control-user mb-4" oninput="updatePlayerList()" name="player_name" type="text" placeholder="Player name...">
                                </div>
                                <hr style="width:95%">
                                <div class="col-12 mt-4" id="nationalityInputDiv">
                                    <form action="/items/store" method="POST">
                                        {!! csrf_field() !!}
                                        <select class="form-control mt-2 mb-1" name="item" id="nationalityInput">
                                            <option value="" disabled selected hidden>Nationality</option>
                                        </select>
                                        <select class="form-control mt-2 mb-2" name="nationalityQuality">
                                            <option value="" disabled selected hidden>Quality</option>
                                            <option value="gold">Gold</option>
                                            <option value="silver">Silver</option>
                                            <option value="bronze">Bronze</option>
                                            <option value="any">Any</option>
                                        </select>
                                        <button class="btn btn-primary mt-2" type="submit">Select</button>
                                    </form>
                                </div>
                                <div class="col-12 mt-4">
                                    <div id="results_div_players" class="row">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="consumables" role="tabpanel" aria-labelledby="consumable-tab">
                                <div class="col-12 mt-4">
                                    <div id="results_div_consumables" class="row">
                                        <div class="col-12">
                                            <form action="/items/store" class="mb-3" method="POST">
                                                {!! csrf_field() !!}
                                                <select class="form-control" id="consumablesInput" name="item" type="text">
                                                </select>
                                                <button class="btn btn-primary mt-3"> Select</button>
                                            </form>
                                            <small>Please edit the prices after you add the item. Automatic price doesn't work with consumables.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        <div class="card h-100 shadow mb-4">
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
                                        <div class="fifa-card mb-4 {{$item->fifaCard->type}}">
                                            <div class="card-face">
                                                <div class="card-face-inner">
                                                    <img src="{{env('EA_PLAYERS_PIC')}}/{{$item->fifaCard->asset_id}}.png">
                                                </div>
                                            </div>
                                            @if($item->fifaCard->club != 0)
                                                <div class="card-badge">
                                                    <img src="{{env('EA_CLUB_BADGE')}}/{{$item->fifaCard->club}}.png" alt="Badge">
                                                </div>
                                            @endif
                                            <div class="card-rating">@if($item->fifaCard->rating > 0){{$item->fifaCard->rating}}@endif</div>
                                            <div class="card-position">{{$item->fifaCard->position}}</div>
                                            <div class="card-name">{{$item->fifaCard->name}}</div>
                                            <div class="card-flag">
                                                <img src="/flags/{{$item->fifaCard->nationality}}.png" alt="Nation">
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-md-12  mt-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-center mb-2"><b>Buy Price</b></h6>
                                                <p id="b-xbox{{$item->id}}" class="text-center mb-1" style="font-size:13px;"></p>
                                                <p id="b-ps{{$item->id}}" class="text-center mb-1" style="font-size:13px;"></p>
                                                <p id="b-pc{{$item->id}}" class="text-center mb-1" style="font-size:13px;"></p>
                                                <script>
                                                    document.getElementById('b-xbox{{$item->id}}').innerHTML = numberWithCommas('<i class="fab fa-xbox"></i> {{$item->xbox_buy_bin}}')
                                                    document.getElementById('b-ps{{$item->id}}').innerHTML = numberWithCommas('<i class="fab fa-playstation"></i> {{$item->ps_buy_bin}}')
                                                    document.getElementById('b-pc{{$item->id}}').innerHTML = numberWithCommas('<i class="fa fa-desktop"></i> {{$item->pc_buy_bin}}')
                                                </script>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-center mb-2"><b>Sell Price</b></h6>
                                                <p id="s-xbox{{$item->id}}" class="text-center mb-1" style="font-size:13px;"></p>
                                                <p id="s-ps{{$item->id}}" class="text-center mb-1" style="font-size:13px;"></p>
                                                <p id="s-pc{{$item->id}}" class="text-center mb-1" style="font-size:13px;"></p>
                                                <script>
                                                    document.getElementById('s-xbox{{$item->id}}').innerHTML = numberWithCommas('<i class="fab fa-xbox"></i> {{$item->xbox_sell_bin}}')
                                                    document.getElementById('s-ps{{$item->id}}').innerHTML = numberWithCommas('<i class="fab fa-playstation"></i> {{$item->ps_sell_bin}}')
                                                    document.getElementById('s-pc{{$item->id}}').innerHTML = numberWithCommas('<i class="fa fa-desktop"></i> {{$item->pc_sell_bin}}')
                                                </script>
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
        const rarityIds = {
            '70': 'champions-teamoftournment',
            '72': 'carnibal',
            '12': 'icon',
            '71': 'futurestars',
            '51': 'flashback',
            '16': 'futties',
            '85': 'headliners',
            '4': 'hero',
            '5': 'toty',
            '30': 'specialitem',
            '28': 'award-item',
            '66': 'tots',
            '18': 'futchampions',
            '1': 'rare',
            '0': 'non-rare',
            '8': 'orange',
            '48': 'champions-rare',
            '49': 'champions-manofmatch',
            '50': 'champions-live',
            '69': 'champions-sbc',
            '3': 'goldif',
            '46': 'europaleague-live',
            '68': 'europaleague-teamoftournment',
            '45': 'europaleague-manofmatch',
            '43': 'premierleague-playerofmonth',
            '32': 'futmas',
            '63': 'sbcsummer'
        }
        function editItem(id)
        {
            $("#editItemModal").modal()
            let itemToEdit = JSON.parse("{{$items}}".replace(/&quot;/g, '"'))
            item = itemToEdit.find(item => item.id === id)

            document.getElementById('edit-card-assetId').src = "{{env('EA_PLAYERS_PIC')}}/" + item['fifa_card']['asset_id'] + ".png"
            document.getElementById('edit-card-club').src = "{{env('EA_CLUB_BADGE')}}/" + item['fifa_card']['club'] + ".png"
            document.getElementById('edit-card-nationality').src = "/flags/" + item['fifa_card']['nationality'] + ".png"
            document.getElementById('edit-card-name').innerHTML = item['fifa_card']['name'] 
            document.getElementById('edit-card-position').innerHTML = item['fifa_card']['position']
            document.getElementById('edit-card-rating').innerHTML = item['fifa_card']['rating']  
            document.getElementById('edit-card-bg').className = "fifa-card mb-4 " + item['fifa_card']['type']  

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
                let displayName = ''
                if(playerCardsJSON['items'][i]['commonName'] != undefined) {displayName = playerCardsJSON['items'][i]['commonName']}
                else {displayName = playerCardsJSON['items'][i]['lastName']}
                let type = ""
                if(playerCardsJSON['items'][i]['rarityId'] == 0) 
                {
                    if(playerCardsJSON['items'][i]['rating'] >= 75) { type = 'gold' }
                    if(playerCardsJSON['items'][i]['rating'] >= 65 && playerCardsJSON['items'][i]['rating'] <= 74) { type = 'silver' }
                    if(playerCardsJSON['items'][i]['rating'] >= 0 && playerCardsJSON['items'][i]['rating'] <= 64 ) { type = 'bronze' }
                }
                else if(playerCardsJSON['items'][i]['rarityId'] == 1)
                {
                    if(playerCardsJSON['items'][i]['rating'] >= 75) { type = 'goldrare' }
                    if(playerCardsJSON['items'][i]['rating'] >= 65 && playerCardsJSON['items'][i]['rating'] <= 74) { type = 'silverrare' }
                    if(playerCardsJSON['items'][i]['rating'] >= 0 && playerCardsJSON['items'][i]['rating'] <= 64 ) { type = 'bronzerare' }
                }
                resultsDiv.innerHTML += 
                `
                <div class="fifa-card col-12 col-sm-6 col-lg-3 mb-4">
                    <form action="/items/store" method="POST">
                        @csrf
                        <input type="hidden" name="item" value='`+ JSON.stringify(playerCardsJSON['items'][i]) +`'> 
                        <button class="card-button" type="submit">
                            <div class="fifa-card mb-4 `+ (type == '' ? rarityIds[playerCardsJSON['items'][i]['rarityId']] : type) +`">
                                <div class="card-face">
                                    <div class="card-face-inner">
                                        <img src="{{env('EA_PLAYERS_PIC')}}/`+ playerCardsJSON['items'][i]['baseId'] + `.png">
                                    </div>
                                </div>
                                <div class="card-badge">
                                    <img src="{{env('EA_CLUB_BADGE')}}/`+ playerCardsJSON['items'][i]['club']['id'] +`.png" alt="Badge">
                                </div>
                                <div class="card-flag">
                                    <img src="/flags/`+ playerCardsJSON['items'][i]['nation']['id'] +`.png" alt="Nation">
                                </div>
                                <div class="card-rating">`+ playerCardsJSON['items'][i]['rating'] +`</div>
                                <div class="card-name">`+ displayName +`</div>
                                <div class="card-position">`+ playerCardsJSON['items'][i]['position'] +`</div>
                            </div>  
                        </button>
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
            if(player_name.length > 3)
            {
                document.getElementById("results_div_players").innerHTML="";
                document.getElementById('nationalityInputDiv').innerHTML = ""
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
        var consumablesJSON = ""
        function getConsumablesList()
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
                    consumablesJSON = JSON.parse(this.responseText)
                    updateConsumablesList()
                }
            }
            xmlhttp.open("GET","/items/consumables",true);
            xmlhttp.send();
        }
        getConsumablesList()
        function updateConsumablesList()
        {
           let consumablesInput = document.getElementById('consumablesInput')

            for(let i = 0; i < consumablesJSON.length; i++)
            {
                consumablesJSON[i]['isConsumable'] = true
                consumablesInput.innerHTML += `<option value='`+JSON.stringify(consumablesJSON[i])+`'>`+ consumablesJSON[i]['name'] +`</option>`
            }
        }

        var nationalatiesJSON = ""
        function getNationalitiesList()
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
                    nationalatiesJSON = JSON.parse(this.responseText)
                    updateNationalitiesList()
                }
            }
            xmlhttp.open("GET","/items/nationalities",true);
            xmlhttp.send();
        }

        getNationalitiesList()

        function updateNationalitiesList()
        {
           let nationalityInput = document.getElementById('nationalityInput')

            for(let i = 0; i < nationalatiesJSON.length; i++)
            {
                nationalityInput.innerHTML += `<option value='`+JSON.stringify(nationalatiesJSON[i])+`'>`+ nationalatiesJSON[i]['nationality'] +`</option>`
            }
        }
    </script>
@endsection