<!doctype html>>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

    </style>
</head>

<body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
        <tr>
            <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #ffffff; border-bottom: 1px solid #edeff2; border-top: 1px solid #edeff2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #ffffff; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell"
                                        style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                        <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                            Kepada, {{ $nama }}
                                        </h1>
                                        <p style="font-family: Avenir, Helvetica, sans-serif; font-weight: bolder; box-sizing: border-box; color: #74787e; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            PEMBAYARAN RETRIBUSI BERHASIL
                                        </p>
                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            Untuk <span style="color: #74787e !important; font-weight: bolder">{{ $rincian_retribusi }}</span>
                                        </p>
                                        <div class="table" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                            <table style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 30px auto; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                                <tbody style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            Tanggal Bayar
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ Carbon\Carbon::parse($tanggal_bayar)->format('d F Y | H:i:s') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            Nomor Transaksi
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ $nomor_transaksi }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            Nominal
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ $nominal }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            Metode Pembayaran
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ $metode_pembayaran }}
                                                        </td>
                                                    </tr>
                                                    <hr>
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            Nama
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ $nama }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            No. Pendaftaran
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ $no_pendaftaran }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            No. SKRD
                                                        </td>
                                                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 15px; padding: 5px 0;">
                                                            {{ $no_skrd }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            Untuk data selengkapnya bisa dilihat pada link dibawah ini
                                        </p>
                                        <a href="{{ $link }}">{{ $link }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                <tr>
                                    <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #aeaeae; font-size: 14px; text-align: center;">
                                            Copyright © Kota Tangerang Selatan 2022.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
