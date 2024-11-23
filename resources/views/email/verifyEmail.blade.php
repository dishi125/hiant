<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Change Email : Me-Talk</title>    
  </head>
  <body class="">
    <table border="0" style="border:1px solid #000;" cellspacing="0" align="left" width="500">
      <tr>
          <th style="padding:20px 0px;background-color: #ccc;font-size: 30px;">Change Email</th>
      </tr>
      <tr>
          <td style="padding:15px;">
              <p>Hello {{ $user_detail->name }},</p>
              <p>Please click the button to change your email</p>
              <a href="{{$user_detail->verify_link}}" class="accept_btn" style="font-size: 15px;letter-spacing: 1px;font-weight: 500;margin: 10px 0;">Change Email</a>
              <p>Thank you.</p>
              <p style="margin: 0;"><strong>Regrads,</strong></p>
              <p style="margin: 0;"><strong>BelleTalk Team</strong></p>
          </td>
      </tr>
    </table>
  </body>
</html>
