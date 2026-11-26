<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kode OTP Reset Kata Sandi</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f3f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0f3f8; padding: 40px 15px;">
    <tr>
      <td align="center">
        <!-- Main Card Container -->
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 540px; background-color: #ffffff; border-radius: 24px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
          
          <!-- Header Banner -->
          <tr>
            <td style="background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%); padding: 32px 30px; text-align: center;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                <tr>
                  <td style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; text-align: center; vertical-align: middle;">
                    <span style="font-size: 22px; color: #ffffff;">📖</span>
                  </td>
                  <td style="padding-left: 12px; text-align: left;">
                    <div style="font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: -0.3px;">DigiLib</div>
                    <div style="font-size: 11.5px; color: rgba(255, 255, 255, 0.85); font-weight: 500;">Sistem Perpustakaan Modern</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Body Content -->
          <tr>
            <td style="padding: 36px 32px 28px 32px; text-align: left;">
              <h2 style="font-size: 19px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 12px;">
                Permintaan Reset Kata Sandi
              </h2>
              <p style="font-size: 14px; line-height: 1.6; color: #475569; margin-top: 0; margin-bottom: 20px;">
                Halo <strong>{{ $userName }}</strong>, kami menerima permintaan untuk mereset kata sandi akun DigiLib Anda. Gunakan kode One-Time Password (OTP) berikut untuk melanjutkan:
              </p>

              <!-- OTP Highlight Box -->
              <div style="background-color: #f8fafc; border: 2px dashed #93c5fd; border-radius: 16px; padding: 22px; text-align: center; margin-bottom: 24px;">
                <div style="font-size: 11.5px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 1px; margin-bottom: 8px;">
                  Kode Verifikasi OTP Anda
                </div>
                <div style="font-size: 34px; font-weight: 900; letter-spacing: 8px; color: #1d4ed8; font-family: monospace;">
                  {{ $otp }}
                </div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">
                  Berlaku selama <strong>15 menit</strong> sejak email ini dikirimkan
                </div>
              </div>

              <!-- Warning Notice -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
                <tr>
                  <td style="font-size: 12.5px; color: #991b1b; line-height: 1.5;">
                    <strong>Penting:</strong> Jangan pernah membagikan kode OTP ini kepada siapa pun, termasuk pihak perpustakaan atau administrator DigiLib.
                  </td>
                </tr>
              </table>

              <p style="font-size: 13px; line-height: 1.5; color: #64748b; margin: 0;">
                Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini. Akun dan kata sandi Anda tetap aman.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center;">
              <p style="font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.5;">
                &copy; {{ date('Y') }} DigiLib Perpustakaan Digital. Seluruh hak cipta dilindungi undang-undang.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
