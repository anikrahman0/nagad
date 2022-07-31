<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nagad Payment Integration Using Laravel</title>
        <link rel="shortcut icon" href="{{asset('images/nagad.png')}}" type="image/x-icon">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="container mt-5 mb-5">
            <div class="">
                <form action="{{route('create-payment')}}" method="POST">
                    @csrf
                    <div class="text-center">            
                        <img width="100" height="100" src="{{asset('images/nagad.png')}}" alt="Nagad Image" title="Nagad Image">
                    </div>
                    <h4 class="text-center mt-4 mb-4">Payment Integration Nagad Using Laravel</h4>
                    <div class="row jumbotron text-center">
                        <div class="col-sm-6 form-group mx-auto mb-4">
                            <input type="hidden" name="payment" value="nagad" checked id="nagad" required>
                            <input type="number" min="10" max="100000" maxlength="6" class="form-control @error('amount') is-invalid @enderror" name="amount" id="name-l" placeholder="Enter amount" required>
                            @error('amount')
                                <span class="invalid-feedback">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> 
                        <div class="col-sm-12 form-group mb-4">
                            <button id="nagad" class="btn btn-success btn-block" type="submit">Make Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            // $(document).on('click','#nagad',function(){
            //     $.ajax
            //     ({
            //         type: "POST",
            //         url: "{{route('create-payment')}}",
            //         data: {
            //             "_token": "{{ csrf_token() }}",
            //         },
            //         success: function (response) {
            //             console.log(response);
            //             window.location.href = response;
            //         }
            //     });
            // });
        </script>
    </body>
</html>
