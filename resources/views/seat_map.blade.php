@extends('layouts.app')

@section('content')
    <style>
        .seat {
            width: 25px;
            height: 25px;
            margin: 5px;
            border-radius: 4px;
            display: inline-block;
        }
        .large {
            background-color: #ff6961;
        }
        .regular {
            background-color: #9dc183;
        }
        .wheel_chair {
            background-color: #87ceeb;
        }
        .unknown {
            background-color: gray;
        }
        .loading {
            text-align: center;
        }
        .error {
            text-align: center;
            color: red;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Room Seat Map</h1>
                <div id="data-container">
                    <div id="seat-map"></div>
                </div>

                <div id="error-container" style="display: none;">
                    <p>Error loading data. Please try again later.</p>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-container"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(async function() {
            function getSeatTypeAbbreviation($name) {
                switch($name) {
                    case 'large':
                        return 'R';
                    case 'regular':
                        return 'L';
                    case 'wheel_chair':
                        return 'WC';
                    default:
                        return 'U';
                }
            }

            const loadingContainer = $('#loading-container');
            const spinnerIcon = $('<span>').addClass('spinner spinner-border spinner-border-sm').attr('role', 'status').attr('aria-hidden', 'true');
            const errorContainer = $('#error-container');

            try {
                // show loading spinner
                $('#seat-map').html('<i class="fa fa-spinner fa-spin"></i>');

                loadingContainer.empty().append(spinnerIcon.clone()).append(' Loading...');
                loadingContainer.show();

                // fetch seat map data from Laravel endpoint
                const url = '{{ route('api.theater-rooms.show', request()->room_id) }}';
                const response = await $.ajax(url);
                const seatMap = response.rows;

                // create seat map in the view using DOM API
                const seatMapDiv = document.getElementById('seat-map');
                for (const row of seatMap) {
                    const rowDiv = document.createElement('div');
                    rowDiv.classList.add('seat-row');
                    for (const seat of row.seats) {
                        const seatDiv = document.createElement('div');
                        seatDiv.classList.add('seat');
                        seat.innerText = getSeatTypeAbbreviation(seat.type.name)
                        switch (seat.type.name) {
                            case 'large':
                                seatDiv.classList.add('large');
                                break;
                            case 'regular':
                                seatDiv.classList.add('regular');
                                break;
                            case 'wheel_chair':
                                seatDiv.classList.add('wheel_chair');
                                break;
                            default:
                                seatDiv.classList.add('unknown');
                                break;
                        }
                        rowDiv.appendChild(seatDiv);
                    }
                    seatMapDiv.appendChild(rowDiv);
                }
            } catch (error) {
                // show error feedback
                console.error(error);
                errorContainer.show();
            } finally {
                loadingContainer.empty().hide();
            }
        });
    </script>
@endsection
