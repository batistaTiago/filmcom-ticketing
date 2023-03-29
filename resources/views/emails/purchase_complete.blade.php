<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Complete - FilmCom</title>
    <style>
        :root {
            /* Color scheme 1 */
            /*--primary-color: #89CFF0; !* Baby Blue *!*/
            /*--secondary-color: #A0CAB5; !* Soft Green *!*/
            /*--tertiary-color: #F0EDEE; !* Off White *!*/
            /*--text-color: #1A535C; !* Dark Blue *!*/
            /*--background-color: #F0EDEE; !* Off White *!*/

            /* Color scheme 2 */
            --primary-color: #A2D5F2; /* Light Sky Blue */
            --secondary-color: #97C1A9; /* Muted Green */
            --tertiary-color: #F5E5DE; /* Warm White */
            --text-color: #1B3A4B; /* Navy Blue */
            --background-color: #F5E5DE; /* Warm White */

            /* Reddish Tones Color Schemes */

            /* Color scheme 3 */
            /*--primary-color: #FAC8C8; !* Soft Red *!*/
            /*--secondary-color: #FFD5A5; !* Light Orange *!*/
            /*--tertiary-color: #F2E8E6; !* Light Beige *!*/
            /*--text-color: #6C2B2B; !* Maroon *!*/
            /*--background-color: #F2E8E6; !* Light Beige *!*/

            /* Color scheme 4 */
            /*--primary-color: #FF9AA2; !* Mellow Red *!*/
            /*--secondary-color: #FFDAC1; !* Peach *!*/
            /*--tertiary-color: #E2F0CB; !* Pale Green *!*/
            /*--text-color: #7C3B3B; !* Dark Red *!*/
            /*--background-color: #E2F0CB; !* Pale Green *!*/

            /* Brown, Green, and White Color Scheme */

            /* Color scheme 5 */
            /*--primary-color: #4A3C31; !* Dark Brown *!*/
            /*--secondary-color: #3D5E46; !* Dark Green *!*/
            /*--tertiary-color: #FFFFFF; !* White *!*/
            /*--text-color: #4A3C31; !* Dark Brown *!*/
            /*--background-color: #FFFFFF; !* White *!*/
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 1em;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--secondary-color);
        }

        .header h1 {
            font-size: 24px;
            color: var(--primary-color);
            margin: 0;
        }

        .content {
            padding: 20px 0;
        }

        .cart-summary {
            background-color: #f2f2f2;
            padding: 1em;
            margin-top: 1em;
            border-radius: 4px;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid var(--secondary-color);
        }

        .footer a {
            color: var(--secondary-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>FilmCom Purchase Complete</h1>
    </div>
    <div class="content">
        <p>Hi {{ $cart_state->user->name }},</p>
        <p>Thank you for your recent purchase on FilmCom! Here are the details of your purchase:</p>

        <h2>Ticket Information</h2>
        <table>
            <thead>
            <tr>
                <th>Seat</th>
                <th>Exhibition Date</th>
                <th>Ticket Type</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($cart_state->tickets as $ticket)
                @php
                    $seat = $ticket->seat;
                    $exhibition = $ticket->exhibition;
                    $ticketTypeExhibitionInfo = $ticket->ticketTypeExhibitionInfo;
                    $price = $ticketTypeExhibitionInfo->price / 100;
                @endphp
                <tr>
                    <td>{{ $seat->name }} ({{ $seat->type->name }})</td>
                    <td>{{ \Carbon\Carbon::parse($exhibition->starts_at)->format('F j, Y, g:i A') }} ({{ \Carbon\Carbon::parse($exhibition->starts_at)->dayName }})</td>
                    <td>{{ $ticket->type->name }}</td>
                    <td>${{ number_format($price, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="cart-summary">
            <h2>Cart Summary</h2>
            <p>Total tickets: {{ count($cart_state->tickets) }}</p>
            <p>Total price: ${{ number_format(array_sum(array_map(function($ticket) { return $ticket->ticketTypeExhibitionInfo->price / 100; }, $cart_state->tickets->toArray())), 2) }}</p>
        </div>

        <p>Feel free to explore more great content on our platform. If you have any questions or concerns, please do not hesitate to contact our support team.</p>
        <p>Happy watching!</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} FilmCom. All rights reserved.</p>
        <p><a href="#">Unsubscribe</a> from these emails.</p>
    </div>
</div>
</body>
</html>
