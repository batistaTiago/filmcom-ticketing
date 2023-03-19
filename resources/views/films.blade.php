@extends('layouts.app')

@section('title', 'Data')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Films</h1>
                <div id="data-container">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
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
            const url= '{{ route('api.films.index') }}';
            const perPage = 5;
            let currentPage = 1;

            const loadData = async (page = 1) => {
                try {
                    loadingContainer.empty().append(spinnerIcon.clone()).append(' Loading...');
                    loadingContainer.show();
                    errorContainer.hide();
                    tableBody.empty();
                    const response = await $.ajax(`${url}?page=${page}&per_page=${perPage}`);
                    const data = response.data;

                    dataContainer.empty();
                    paginationContainer.empty();

                    data.forEach((item) => {
                        const row = $('<tr>');
                        const indexCell = $('<td>').text((page-1)*perPage + data.indexOf(item) + 1);
                        const nameCell = $('<td>').text(item.name);
                        row.append(indexCell).append(nameCell);
                        dataContainer.append(row);
                    });

                    buildLinks(response, page, paginationContainer);
                } catch (error) {
                    errorContainer.show();
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
