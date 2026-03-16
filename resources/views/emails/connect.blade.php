<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Query Received</title>
</head>

<body
    style="margin:0;padding:0;font-family:Segoe UI, Inter, ui-sans-serif, system-ui, sans-serif;color:#333;line-height:1.6;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="background:#ffffff;border:1px solid #e1e1e1;border-radius:10px;overflow:hidden;">

                    <tr>
                        <td style="background:#08519e;padding:20px;color:#ffffff;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>

                                    <td align="left"
                                        style="font-size:18px;font-weight:500;text-transform:capitalize;padding-right:20px;text-align:justify;">
                                        {{ $query->subject }}
                                    </td>

                                    <td align="right">
                                        <div style="font-size:12px;opacity:0.85;">Date</div>
                                        <div style="font-size:12px;font-weight:500;">
                                            {{ $query->date->format('d/m/Y') }}
                                        </div>
                                    </td>

                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                                <tr>
                                    <td width="140" style="font-size:14px;font-weight:500;color:#666;">Name:</td>
                                    <td style="font-size:14px">{{ $query->name }}</td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                                <tr>
                                    <td width="140" style="font-size:14px;font-weight:500;color:#666;">Email:</td>
                                    <td>
                                        <a href="mailto:{{ $query->email }}"
                                            style="font-size:14px;color:#08519e;text-decoration:none;">
                                            {{ $query->email }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                                <tr>
                                    <td width="140" style="font-size:14px;font-weight:500;color:#666;">Phone:</td>
                                    <td style="font-size:14px;">{{ $query->phone }}</td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:14px;font-weight:500;color:#666;padding-bottom:5px;">
                                        Message:
                                    </td>
                                </tr>

                                <tr>
                                    <td style="font-size:14px;background:#f9f9f9;border-radius:6px;padding:12px;">
                                        {{ $query->message }}
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f9f9f9;padding:15px;text-align:center;font-size:12px;color:#999;">
                            This is an automated email generated from the ORION website.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>