<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* CSS for the sidebar */
        #sidebar {
            height: 100%;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1;
            background-color: #333;
            padding-top: 50px;
            transition: all 0.3s;
        }
        #sidebar.active {
            width: 80px;
        }
        #sidebar .sidebar-link {
            display: block;
            padding: 16px;
            color: #fff;
            transition: all 0.3s;
        }
        #sidebar .sidebar-link:hover {
            background-color: #111;
        }
        #content {
            margin-left: 250px;
            transition: all 0.3s;
        }
        #content.active {
            margin-left: 80px;
        }
    </style>

    <style>
        .spinner {
            width: 24px;
            height: 24px;
            animation: rotate 1s linear infinite;
            border: 6px solid #ccc;
            border-right-color: #333;
            border-radius: 50%;
        }

        #loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loading-container span {
            margin-right: 8px
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <a href="{{ route('theaters.index') }}" class="sidebar-link {{ (request()->is('theaters')) ? 'active' : '' }}">Theaters</a>
        <a href="{{ route('films.index') }}" class="sidebar-link {{ (request()->is('films')) ? 'active' : '' }}">Films</a>
    </div>

    <!-- Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button type="button" id="sidebar-toggle" class="btn btn-info">
                    X
                </button>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        function buildLinks(response, page, paginationContainer) {
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
        }

        $(document).ready(function() {
            // Toggle sidebar
            $("#sidebar-toggle").on("click", function() {
                $("#sidebar").toggleClass("active");
            });

            // Close sidebar when link is clicked
            $("#sidebar a").on("click", function() {
                $("#sidebar").removeClass("active");
            });
        });
    </script>

    @stack('scripts')
</body>
