<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Forgot Password : {!! env('APP_NAME') !!}</title>
  </head>
  <body class="">
    <table border="0" style="border:1px solid #000;" cellspacing="0" align="left" width="500">
      <tr>
          <th style="padding:20px 0px;background-color: #ccc;font-size: 30px;">Forgot Password</th>
      </tr>
      <tr>
          <td style="padding:15px;">
              <p>Hello {{ $member->name }},</p>
              <p>We received a request to reset your juju password.
                Enter the following password reset code:.</p>
              <p><strong>{{$member->otp}}</strong></p><br/><br/><br/>
              <p style="margin: 0;"><strong>Regards,</strong></p>
              <p style="margin: 0;"><strong>{!! env('APP_NAME') !!} Team</strong></p>
          </td>
      </tr>
    </table>
  </body>
</html>
