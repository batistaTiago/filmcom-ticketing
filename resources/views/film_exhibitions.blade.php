@extends('layouts.app')

@section('title', 'Data')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Exhibitions</h1>
                <div id="data-container">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Film ID</th>
                            <th>Theater Room ID</th>
                            <th>Starts At</th>
                            <th>Day of Week</th>
                        </tr>
                        </thead>
                        <tbody id="data-body">
                        <!-- Data will be added here -->
                        </tbody>
                    </table>
                    <div id="loading-container"></div>
                    <nav>
                        <ul class="pagination justify-content-center">
                        </ul>
                    </nav>
                </div>

                <div id="error-container" style="display: none;">
                    <p>Error loading data. Please try again later.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(() => {
            const dataContainer = $('#data-container tbody');
            const paginationContainer = $('#data-container nav ul.pagination');
            const loadingContainer = $('#loading-container');
            const errorContainer = $('#error-container');
            const tableBody = $('tbody');
            const spinnerIcon = $('<span>').addClass('spinner spinner-border spinner-border-sm').attr('role', 'status').attr('aria-hidden', 'true');
            const film_id = '{{ request()->film_id }}';
            const url= '{{ route('api.film_exhibitions.index') }}';
            const perPage = 5;
            let currentPage = 1;

            const loadData = async (page = 1) => {
                try {
                    loadingContainer.empty().append(spinnerIcon.clone()).append(' Loading...');
                    loadingContainer.show();
                    errorContainer.hide();
                    tableBody.empty();
                    const response = await $.ajax(`${url}?film_id=${film_id}&page=${page}&per_page=${perPage}`);
                    const data = response;

                    dataContainer.empty();
                    paginationContainer.empty();

                    data.forEach((item) => {
                        const row = $('<tr>');
                        const indexCell = $('<td>').text((page-1)*perPage + data.indexOf(item) + 1);
                        const filmIdCell = $('<td>').text(item.film_id);
                        const theaterRoomIdCell = $('<td>').text(item.theater_room_id);
                        const startsAtCell = $('<td>').text(item.starts_at);
                        const dayOfWeekCell = $('<td>').text(item.day_of_week);
                        row.append(indexCell)
                            .append(filmIdCell)
                            .append(theaterRoomIdCell)
                            .append(startsAtCell)
                            .append(dayOfWeekCell);
                        dataContainer.append(row);
                    });

                    const totalPages = response.last_page;
                    const visiblePages = 8;
                    const halfVisiblePages = Math.floor(visiblePages / 2);

                    let startPage = Math.max(1, page - halfVisiblePages);
                    let endPage = Math.min(totalPages, startPage + visiblePages - 1);
                    startPage = Math.max(1, endPage - visiblePages + 1);

                    if (startPage > 1) {
                        const first = $('<li>').addClass('page-item');
                        const firstLink = $('<a>').addClass('page-link').attr('href', '#').data('page', 1).text('1');
                        first.append(firstLink).appendTo(paginationContainer);

                        if (startPage > 2) {
                            const dots = $('<li>').addClass('page-item disabled');
                            const dotsLink = $('<a>').addClass('page-link').attr('href', '#').text('...');
                            dots.append(dotsLink).appendTo(paginationContainer);
                        }
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        const li = $('<li>').addClass('page-item').toggleClass('active', i === page);
                        const link = $('<a>').addClass('page-link').attr('href', '#').data('page', i).text(i);
                        li.append(link).appendTo(paginationContainer);
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            const dots = $('<li>').addClass('page-item disabled');
                            const dotsLink = $('<a>').addClass('page-link').attr('href', '#').text('...');
                            dots.append(dotsLink).appendTo(paginationContainer);
                        }

                        const last = $('<li>').addClass('page-item');
                        const lastLink = $('<a>').addClass('page-link').attr('href', '#').data('page', totalPages).text(totalPages);
                        last.append(lastLink).appendTo(paginationContainer);
                    }
                } catch (error) {
                    errorContainer.show();
                    console.error(error);
                } finally {
                    loadingContainer.empty().hide();
                }
            };

            paginationContainer.on('click', 'a.page-link', function (event) {
                event.preventDefault();
                currentPage = parseInt($(this).data('page'));
                loadData(currentPage);
            });

            loadData();
        });
    </script>
@endpush
