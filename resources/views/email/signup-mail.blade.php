<!doctype html>
<html>

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{!! $subjectTitle !!}</title>
</head>

<body class="">
    <table border="0" style="border:1px solid #000;" cellspacing="0" align="left">
        <tr>
            <th colspan="2" style="padding:15px 50px;background-color: #ccc;font-size: 22px;">{!! $subjectTitle !!}
            </th>
        </tr>
        <tr>
            <th style="padding:15px;">E mail</th>
            <td style="padding:15px;">{!! $mailContent->email !!}</td>
        </tr>
        <tr>
            <th style="padding:15px;">User name</th>
            <td style="padding:15px;">{!! $mailContent->username !!}</td>
        </tr>
        <tr>
            <th style="padding:15px;">Gender</th>
            <td style="padding:15px;">{!! $mailContent->gender !!}</td>
        </tr>
        <tr>
            <th style="padding:15px;">Phone number</th>
            <td style="padding:15px;">{!! $mailContent->phone !!}</td>
        </tr>

    </table>
</body>

</html>
