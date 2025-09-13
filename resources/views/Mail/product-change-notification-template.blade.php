<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Product Notification</title>
    <meta name="description" content="Product Notification.">
    <style type="text/css">
        a:hover {
            text-decoration: underline !important;
        }
    </style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <!--100% body table-->
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
        style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                    align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="padding: 20px; max-width:670px;background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06); text-align:center;">
                                <tr>
                                    <td>
                                        <h1
                                            style="color:#1e1e2d; font-weight:600; margin:0;font-size:26px;font-family:'Rubik',sans-serif;">
                                            {{ ucfirst($action) }}</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:40px;"><b>Product: </b> {{ $product->product_code }}</td>
                                </tr>
                                <tr>
                                    <td style="height:40px;"><b>Action: </b> {{ ucfirst($action) }}</td>
                                </tr>
                                @if (!empty($changes))
                                <tr>
                                    <td style="height:40px;"><b>Change Details: </b></td>
                                </tr>
                                <tr>
                                    <td style="height:30px; word-break: break-all;">
                                        <ul>
                                            @foreach ($changes as $field => $values)
                                            <li class="word-break: break-all;">
                                                <b>{{ ucfirst(str_replace('_', ' ', $field)) }}:</b>
                                                @if(is_array($values))
                                                <b>Old:</b> {{ json_encode($values['old']) ?? 'N/A' }} <b>→ New:</b> {{
                                                json_encode($values['new']) ?? 'N/A' }}
                                                @else
                                                {{ $values }}
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td>
                                        <p><span><b>Message :</b> </span> {{ $customMessage ?? 'No additional
                                            message.'}}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="{{env('APP_URL')}}"
                                            style="background:#5646c4;text-decoration:none !important; font-weight:500; margin-top:10px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">View
                                            Product</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>Thanks,<br>Growthkul Support</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        <tr>
            <td style="height:20px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="text-align:center;">
                <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">
                    &copy; <strong>www.Growthkul.com</strong></p>
            </td>
        </tr>
        <tr>
            <td style="height:80px;">&nbsp;</td>
        </tr>
    </table>
    </td>
    </tr>
    </table>
    <!--/100% body table-->
</body>

</html>