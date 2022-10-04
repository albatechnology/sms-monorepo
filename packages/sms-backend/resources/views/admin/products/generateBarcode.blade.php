<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Products Barcode</title>
        <style type="text/css">
            .center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="center">
            @foreach ($products as $product)
            <h1>Test {{$product->name}}</h1></br><img src="data:image/png;base64, {{\DNS2D::getBarcodePNG(env('MOVES_PRODUCT_URL') . $product->id, 'QRCODE', 10, 10)}}" alt="barcode" />
            @endforeach
        </div>
    </body>
</html>
