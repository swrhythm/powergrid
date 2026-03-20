<!DOCTYPE html>
<html lang="en">
<head>
    <title>Moderator View</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script></head>
<body>
<div class="container-fluid">
    <div class="row justify-content-center">
        <h1 class="text-center">Moderator View</h1>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-12 p-2 border border-info">
            <h2>Transaksi</h2>
            @if($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{$error}}
                    </div>
                @endforeach
            @endif
            <form action="/inputPlayerTransaction" method="post">
                @csrf
                @method('POST')
                <input type="text" class="form-control" id="moderatorId" name="moderatorId" value="{{$moderatorId}}" hidden>
                <input type="text" class="form-control" id="moderatorPasscode" name="moderatorPasscode" value="{{$moderatorPasscode}}" hidden>
                <div class="form-group">
                    <label for="email">Player:</label>

                    @foreach($playerList as $player)
                    <div class="form-check">
                        <label class="form-check-label" style="background-color:{{$player->color}};padding: 5px 5px 5px 25px;width: 100%">
                            <input type="radio" id="playerId" name="playerId"  class="form-check-input" value="{{$player->id}}" required>{{$player->name}} - {{$player->color}}
                        </label>
                    </div>
                    @endforeach
                </div>
                <div class="form-group">
                    <label for="text">Total:</label>
                    <input type="text" class="form-control" placeholder="Enter Amount" id="total" name="total" value="0">
                </div>
                <div class="form-group">
                    <label for="text">Description:</label>
                    <input type="text" class="form-control" placeholder="Enter Description" id="description" name="description">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-danger">Reset</button>
            </form>
            <hr>
            @if($moderatorType == "ModOpen")
            <h2>Current Stat</h2>
            <table class="table table-sm table-striped">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Passcode</th>
                    <th>Name</th>
                    <th>Color</th>
                    <th>Cash</th>
                    <th>House</th>
                    <th>P.Plant</th>
                </tr>
                </thead>
                <tbody>
                @foreach($playerList as $player)
                    <tr>
                        <td>{{$player->id}}</td>
                        <td onclick="alert('{{$player->passCode}}')">Click</td>
                        <td>{{$player->name}}</td>
                        <td style="background-color: {{$player->color}};color: {{$player->color}}">{{$player->color}}</td>
                        @php
                            $totalElektron = \App\Models\playerTransaction::where('playerId',$player->id)->sum('total');
                        @endphp
                        <td>{{$totalElektron}}</td>
                        <td>{{$player->houseCount}}</td>
                        <td>{{$player->largestPowerplant}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
        <div class="col-md-8 col-sm-12 p-0">
            <div  class="col-12 border border-danger">
                <h2>Expense</h2>
                <div class="row p-2">
                    <h3>Powerplant</h3>
                </div>
                <div class="row p-2">
                    Number: <input style="width:50px;height:26px;padding:0px" type="number" id="powerplantNumber">
                    Value: <input style="width:50px;height:26px;padding:0px" type="number" id="powerplantValue">
                    <button class="btn btn-sm btn-outline-danger" onclick="buyPowerplant()">Buy Powerplant</button>
                </div>
                <div class="row p-2">
                    <h3>Resource</h3>
                </div>
                <div class="row p-2">
                    <div class="form-check">
                        <label class="form-check-label text-light" style="background-color:chocolate;padding: 10px 10px 10px 25px;width: 100px">
                            <input type="radio" id="resourceType" name="resourceType" class="form-check-input" value="Coal">Coal
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label bg-dark text-light" style="padding: 10px 10px 10px 25px;width: 100px">
                            <input type="radio" id="resourceType" name="resourceType" class="form-check-input" value="Oil">Oil
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label bg-warning" style="padding: 10px 10px 10px 25px;width: 100px">
                            <input type="radio" id="resourceType" name="resourceType" class="form-check-input" value="Trash">Trash
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label bg-danger" style="padding: 10px 10px 10px 25px;width: 100px">
                            <input type="radio" id="resourceType" name="resourceType" class="form-check-input" value="Nuclear">Nuclear
                        </label>
                    </div>
                </div>
                <div class="row p-2">
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(1,'[Buy 1 ')">1</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(2,'[Buy 2 ')">2</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(3,'[Buy 3 ')">3</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(4,'[Buy 4 ')">4</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(5,'[Buy 5 ')">5</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(6,'[Buy 6 ')">6</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(7,'[Buy 7 ')">7</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(8,'[Buy 8 ')">8</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(9,'[Buy 9 ')">9</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(10,'[Buy 10 ')">10</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(11,'[Buy 11 ')">11</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(12,'[Buy 12 ')">12</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(13,'[Buy 13 ')">13</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(14,'[Buy 14 ')">14</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(15,'[Buy 15 ')">15</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyResource(16,'[Buy 16 ')">16</button>
                </div>

                <div class="row p-2">
                    <h3>Rumah</h3>
                </div>
                <div class="row p-2">
                    <button class="btn btn-md btn-outline-danger" onclick="buyHouse(-10,'[Rumah Step 1] ')">Rumah 10</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyHouse(-15,'[Rumah Step 2] ')">Rumah 15</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyHouse(-20,'[Rumah Step 3] ')">Rumah 20</button>
                    <button class="btn btn-md btn-outline-danger" onclick="buyHouse(0,'[Subtract 1 Rumah] ')">Subtract rumah</button>
                </div>
                <div class="row p-2">
                    <h3>connection</h3>
                </div>
                <div class="row p-2">
                    <input type="number" id="conectorValue">
                    <button class="btn btn-md btn-outline-danger" onclick="payConnector()">Pay Connector</button>
                </div>
            </div>
            <div  class="col-12 border border-success">
                <h2>Income</h2>
            <div class="row p-2">
                <h3>House lit</h3>
            </div>
            <div class="row p-2">
                <button class="btn btn-md btn-success" onclick="income(10,'[0 house turned on] ')">0</button>
                <button class="btn btn-md btn-success" onclick="income(22,'[1 house turned on] ')">1</button>
                <button class="btn btn-md btn-success" onclick="income(33,'[2 house turned on] ')">2</button>
                <button class="btn btn-md btn-success" onclick="income(44,'[3 house turned on] ')">3</button>
                <button class="btn btn-md btn-success" onclick="income(54,'[4 house turned on] ')">4</button>
                <button class="btn btn-md btn-success" onclick="income(64,'[5 house turned on] ')">5</button>
                <button class="btn btn-md btn-success" onclick="income(73,'[6 house turned on] ')">6</button>
                <button class="btn btn-md btn-success" onclick="income(82,'[7 house turned on] ')">7</button>
                <button class="btn btn-md btn-success" onclick="income(90,'[8 house turned on] ')">8</button>
                <button class="btn btn-md btn-success" onclick="income(98,'[9 house turned on] ')">9</button>
                <button class="btn btn-md btn-success" onclick="income(105,'[10 house turned on] ')">10</button>
                <button class="btn btn-md btn-success" onclick="income(112,'[11 house turned on] ')">11</button>
                <button class="btn btn-md btn-success" onclick="income(118,'[12 house turned on] ')">12</button>
                <button class="btn btn-md btn-success" onclick="income(124,'[13 house turned on] ')">13</button>
                <button class="btn btn-md btn-success" onclick="income(129,'[14 house turned on] ')">14</button>
                <button class="btn btn-md btn-success" onclick="income(134,'[15 house turned on] ')">15</button>
                <button class="btn btn-md btn-success" onclick="income(138,'[16 house turned on] ')">16</button>
                <button class="btn btn-md btn-success" onclick="income(142,'[17 house turned on] ')">17</button>
                <button class="btn btn-md btn-success" onclick="income(145,'[18 house turned on] ')">18</button>
                <button class="btn btn-md btn-success" onclick="income(148,'[19 house turned on] ')">19</button>
                <button class="btn btn-md btn-success" onclick="income(150,'[20 house turned on] ')">20</button>
            </div>
                <div class="row p-2">
                    <h3>Other</h3>
                </div>
                <div class="row p-2">
                    <button class="btn btn-md btn-success" onclick="income(50,'[Starting Money] ')">Starting Money</button>
                </div>
        </div>
        <div  class="col-12 border border-success">
                <h2>Last Transaction</h2>
            <table class="tabble tabble-responsive table-bordered">
                <thead>
                    <tr>
                        <th>nama</th>
                        <th>total</th>
                        <th>desc</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lastTransaction as $transaction)
                    
                    <tr>
                        <td style="color:{{$transaction->color}}">{{$transaction->name}}</td>
                        <td>{{$transaction->total}}</td>
                        <td>{{$transaction->description}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
        </div>
        </div>
    </div>
</div>
<script>
    function buyHouse(total,desc) {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) + parseInt(total);
        document.getElementById("description").value = document.getElementById("description").value + desc;
    }
    function buyPowerplant() {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(document.getElementById("powerplantValue").value);
        document.getElementById("description").value = document.getElementById("description").value + "[Buy Powerplant <" + document.getElementById("powerplantNumber").value + ">]";
    }
    function buyResource(price,desc) {
        var radioValue = $("input[name='resourceType']:checked").val();
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(price);
        document.getElementById("description").value = document.getElementById("description").value + desc + radioValue + "]" ;
    }
    function payConnector() {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(document.getElementById("conectorValue").value);
        document.getElementById("description").value = document.getElementById("description").value + "[Pay Connector " + parseInt(document.getElementById("conectorValue").value) + "]";
    }
    function income(value,desc) {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) + value;
        document.getElementById("description").value = document.getElementById("description").value + desc;
    }
</script>
</body>
</html>
