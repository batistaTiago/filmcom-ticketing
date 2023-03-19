@extends('layouts.app')

@section('content')
    <style>
        .seat {
            width: 25px;
            height: 25px;
            margin: 5px;
            border-radius: 4px;
            display: inline-block;
            text-align: center;
            font-size: 0.8em;
        }
        .available {
            background-color: #9dc183;
        }
        .sold {
            background-color: #ff6961;
        }
        .reserved {
            background-color: #FFD700;
        }
        .unavailable {
            background-color: #CCCCCC;
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
                <h1>Room Seat Availability Map</h1>
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
                const url = '{{ route('api.theater-rooms.show-availability', request()->exhibition_id) }}';
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
                        seatDiv.innerText = getSeatTypeAbbreviation(seat.type.name)
                        switch (seat.status.name) {
                            case 'available':
                                seatDiv.classList.add('available');
                                break;
                            case 'sold':
                                seatDiv.classList.add('sold');
                                break;
                            case 'reserved':
                                seatDiv.classList.add('reserved');
                                break;
                            default:
                                seatDiv.classList.add('unavailable');
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
