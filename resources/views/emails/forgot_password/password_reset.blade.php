@extends('emails.layout')
@section('content') 
    <table style="width: 600px;" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td style="padding: 0px 20px 20px 20px;">
                    <p style="margin: 10px 0;">
                        Hi {{title_case($user['name'])}},
                    </p>
                </td>
            </tr>
            <tr>
                <td style="padding: 0px 20px 0px 20px;">
                    <p style="margin: 5px 0;">
                        You are receiving this email because we  received a password reset request for your account.
                    </p>
                </td>
            </tr>

            <tr>
                <td style="padding: 0px 20px 0px 20px;">
                    <p style="margin: 5px 0;">
                        To Reset your password <a href="http://127.0.0.1:8000/api/reset-password/{{$token}}">click here</a>.
                    </p>
                </td>
            </tr>

            <tr>
                <td style="padding: 20px 20px 0px 20px;">
                    <p style="margin: 5px 0;">
                        <strong>Thanks,</strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="padding: 0px 20px 10px 20px;">
                    <p style="margin: 5px 0;">
                        <strong>{{ config('app.name') }}</strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
@endsection