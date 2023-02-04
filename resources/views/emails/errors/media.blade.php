@extends('emails.model')

@section('content')
<table cellpadding="0" cellspacing="0" border="0" align="left" width="100%">
    <tr>
        <td valign="top">
        Media error : {{ $file }}<br>
        Media client mime : {{ $clientMime }}<br>
        Media  mime : {{ $mime }}<br>

        <h2>PHP environment:</h2>
        <pre style="max-width:600px;">

        $_REQUEST = {{ print_r($_REQUEST, true) }}

        Laravel sessions = {{ print_r(session()->all(), true) }}
        </pre>
        </td>
    </tr>
</table>
@stop