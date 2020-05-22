<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Digital Spark</title>
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Bootstrap -->
    <!-- <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet" media="all" type="text/css" /> -->
</head>

<body>
    <table cellpadding="2" cellspacing="0" border="0" width="600" style="background:#000000; margin:auto;width:600px;" align="center">
        <tbody>
            <tr>
                <td style="color: #444444; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; line-height: 20px;">
                    <table cellpadding="4" cellspacing="0" border="0" width="100%">
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#fff;border: 0;background-repeat: no-repeat;background-size: cover; background-color:#000000;">
                                    <tr>
                                        <td width="50%" style="line-height: 0;padding-bottom: 2px;">
                                            <?php $logo_img = url('/').'/img/logo.jpg'; ?>
                                            <img src="<?php echo $logo_img; ?>">
                                        </td>
                                        <td width="50%" style="text-align: right; color: #fff; font-weight: bold; letter-spacing: 2px; font-size: 17px;font-family: Helvetica, Arial, sans-serif;">
                                            {{ trans('mailer.header.app_name') }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="background: #fff;width:100%;">
                                @yield('content')
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @include('emails.footer')
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>