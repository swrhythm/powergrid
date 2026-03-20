<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Player View</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color: {{$player->color}}">
<div class="rounded" style="background-color: white;position: absolute;width: 90vw;height: 90vh;opacity: 60%;z-index: -900;margin-left: 5vw;margin-top: 5vh">

</div>
<div style="position: absolute;width: 90vw;height: 90vh;margin-left: 5vw;margin-top: 5vh">
    <div class="row justify-content-center">
        <h3 onclick="window.location.reload()">{{$player->name}}</h3>
    </div>
    <div class="row justify-content-center">
        <h1 class="bg-light rounded-pill pl-3 pr-3 pt-1 pb-1 border-secondary">{{$total}} Elektron</h1>
        <h1 class="bg-light rounded-pill pl-3 pr-3 pt-1 pb-1 border-secondary">{{$player->houseCount}} House</h1>
    </div>
    <hr>

    <div class="row justify-content-center">
        <h4>Last 10 transaction</h4>
    </div>
    <div class="container">
        <div class="row" style="height: 70vh;overflow: scroll">
            <div class="col-12">
                <table class="table  table-dark table-sm table-striped" style="margin-left: auto;margin-right: auto">
                    <thead>
                    <tr>
                        <td>Value</td>
                        <td>Desc</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transaction as $transaction)
                        <tr>
                            <td>{{$transaction->total}}</td>
                            <td>{{$transaction->description}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    setTimeout(function() {
        location.reload();
    }, 5000);
</script>
</body>
</html>


