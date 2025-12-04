<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Meteor Shop</title>
    <link rel="canonical" href="https://codepen.io/irwin-basa-sandoval/pen/KKmxVXJ">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">


    <style type="text/css">
        @font-face {
            font-family: Exo;
            font-style: normal;
            font-weight: 100 900;
            src: url(/cf-fonts/v/exo/5.0.16/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Exo;
            font-style: normal;
            font-weight: 100 900;
            src: url(/cf-fonts/v/exo/5.0.16/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Exo;
            font-style: normal;
            font-weight: 100 900;
            src: url(/cf-fonts/v/exo/5.0.16/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Exo;
            font-style: italic;
            font-weight: 100 900;
            src: url(/cf-fonts/v/exo/5.0.16/latin-ext/wght/italic.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Exo;
            font-style: italic;
            font-weight: 100 900;
            src: url(/cf-fonts/v/exo/5.0.16/latin/wght/italic.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Exo;
            font-style: italic;
            font-weight: 100 900;
            src: url(/cf-fonts/v/exo/5.0.16/vietnamese/wght/italic.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }
    </style>

    <style>
        .page_404 {
            padding: 40px 0;
            background: #fff;
            font-family: "exo", serif;
        }

        .page_404 img {
            width: 100%;
        }

        .four_zero_four_bg {
            background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
            height: 400px;
            background-position: center;
        }

        .four_zero_four_bg h1 {
            font-size: 80px;
        }

        .four_zero_four_bg h3 {
            font-size: 80px;
        }

        .link_404 {
            color: #fff !important;
            padding: 10px 20px;
            background: #d21e21;
            margin: 20px 0;
            display: inline-block;
        }

        .contant_box_404 {
            margin-top: -50px;
        }

        @media(max-width:767px) {
            body {
                font-size: 30px !important;
                position: relative;
            }

            .page_404 {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            h2 {
                font-size: 50px;
            }
        }
    </style>

    <script>
        window.console = window.console || function(t) {};
    </script>



</head>

<body translate="no">
    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="col-sm-10 col-sm-offset-1  text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">404</h1>

                        </div>

                        <div class="contant_box_404">
                            <h3 class="h2">
                                Có vẻ như bạn đã đi lạc.
                            </h3>

                            <p>Trang bạn tìm kiếm hiện không có sẵn!</p>

                            <a href="{{ route('client.home') }}" class="link_404">Tới Trang Chủ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
