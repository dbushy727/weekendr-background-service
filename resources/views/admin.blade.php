
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Weekendr · Admin</title>
    <link rel='shortcut icon' type='image/x-icon' href='https://weekendr.io/images/logo.png' />



    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <style>
        .approved { background-color: green !important; }
        .rejected { background-color: red !important; }
    </style>
</head>
<body>

    <main role="main" class="container-fluid">
        <div>{{ $flight_deals }}</div>
        <br>

        <div id="app">
            <email-component></email-component>
            <br>
            <div class="starter-template">
                @if($flight_deals->count() == 0)
                    <h2 class="text-center">No Flight Deals</h2>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Price</th>
                                <th scope="col">Destination</th>
                                <th scope="col">Origin</th>
                                <th scope="col">Dates</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($flight_deals as $flight_deal)
                            <tr>
                                <td>${{ $flight_deal->price / 100 }}</td>
                                <td>{{ $flight_deal->destination_city }}</td>
                                <td>{{ $flight_deal->departure_origin }}</td>
                                <td>{{ $flight_deal->departure_date->format('m/d/y') }} - {{ $flight_deal->return_date->format('m/d/y') }}</td>
                                <td>
                                    <a target="_BLANK" href="{{$flight_deal->link}}"><button class="btn btn-warning"><i class="fas fa-eye"></i></button></a>
                                    <button class="btn btn-success" v-on:click="approve({{$flight_deal->id}}, $event)"><i class="fas fa-check"></i></button>
                                    <button class="btn btn-danger" v-on:click="reject({{$flight_deal->id}}, $event)"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div>{{ $flight_deals }}</div>
    </main><!-- /.container -->

    <script src="/js/app.js"></script>
</body>
</html>
