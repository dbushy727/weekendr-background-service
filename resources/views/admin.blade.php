
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Starter Template · Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</head>
<body>


    <main role="main" class="container">

        <div class="starter-template">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">Price</th>
                        <th scope="col">Destination</th>
                        <th scope="col">Origin</th>
                        <th scope="col">Dates</th>
                        <th scope="col">Link</th>
                        <th scope="col">Approve</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($flight_deals as $flight_deal)
                    <tr>
                        <th scope="row">{{ $flight_deal->id }}</th>
                        <td>${{ $flight_deal->price / 100 }}</td>
                        <td>{{ $flight_deal->destination_city }}</td>
                        <td>{{ $flight_deal->departure_origin }}</td>
                        <td>{{ $flight_deal->departure_date->toFormattedDateString() }} - {{ $flight_deal->return_date->toFormattedDateString() }}</td>
                        <td>
                            <a href="{{$flight_deal->link}}"><button class="btn btn-warning">View Flight</button></a>
                        <td>
                            @if($flight_deal->approved)
                                <button class="btn btn-success" disabled>Approve</button>
                            @else
                                <button class="btn btn-success">Approve</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </main><!-- /.container -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script><script src="/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
</body>
</html>
