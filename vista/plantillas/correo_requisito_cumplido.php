<?php
/**
 * @var string $nombreDestinatario
 * @var string $empresaNombre
 * @var string $requisitoNombre
 * @var string $observaciones
 * @var string $fechaCumplimiento
 * @var string $urlLogin
 */
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
  :root { color-scheme: light; supported-color-schemes: light; }
</style>
</head>
<body style="margin:0;padding:0;background-color:#f2f4f5;font-family:Segoe UI, Arial, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#f2f4f5" style="background-color:#f2f4f5;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="background-color:#ffffff;border-radius:8px;overflow:hidden;max-width:600px;width:100%;">

          <tr>
            <td bgcolor="#22404b" style="background-color:#22404b;background-image:linear-gradient(90deg,#22404b,#1993b8);padding:24px 32px;text-align:center;">
              <img src="cid:logo_zfpe" alt="Zona Franca Internacional de Pereira" style="height:48px;">
            </td>
          </tr>

          <tr>
            <td bgcolor="#ffffff" style="background-color:#ffffff;padding:32px;color:#22404b;">
              <h2 style="margin:0 0 16px;color:#22404b;">✔ Requisito completado</h2>
              <p style="margin:0 0 20px;color:#333;line-height:1.5;">
                Hola<?= !empty($nombreDestinatario) ? ' ' . htmlspecialchars($nombreDestinatario) : '' ?>, en el seguimiento de tu empresa
                <strong><?= htmlspecialchars($empresaNombre) ?></strong> en el
                <strong>Centro de Control Gerencial — Zona Franca Internacional de Pereira</strong>, el siguiente requisito quedó marcado como <strong>cumplido</strong>:
              </p>

              <table role="presentation" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
                <tr>
                  <td bgcolor="#f8fafb" style="border:1px solid #e2e6e8;background-color:#f8fafb;width:40%;color:#22404b;"><strong>Requisito</strong></td>
                  <td bgcolor="#ffffff" style="border:1px solid #e2e6e8;background-color:#ffffff;color:#333;"><?= htmlspecialchars($requisitoNombre) ?></td>
                </tr>
                <tr>
                  <td bgcolor="#f8fafb" style="border:1px solid #e2e6e8;background-color:#f8fafb;color:#22404b;"><strong>Empresa</strong></td>
                  <td bgcolor="#ffffff" style="border:1px solid #e2e6e8;background-color:#ffffff;color:#333;"><?= htmlspecialchars($empresaNombre) ?></td>
                </tr>
                <tr>
                  <td bgcolor="#f8fafb" style="border:1px solid #e2e6e8;background-color:#f8fafb;color:#22404b;"><strong>Fecha de cumplimiento</strong></td>
                  <td bgcolor="#ffffff" style="border:1px solid #e2e6e8;background-color:#ffffff;color:#333;"><?= htmlspecialchars($fechaCumplimiento) ?></td>
                </tr>
              </table>

              <?php if (!empty($observaciones)): ?>
                <div style="background-color:#f0f8fb;border:1px solid #1993b8;border-radius:6px;padding:16px;margin-bottom:20px;">
                  <p style="margin:0 0 8px;color:#22404b;"><strong>Observaciones</strong></p>
                  <p style="margin:0;color:#333;white-space:pre-line;"><?= nl2br(htmlspecialchars($observaciones)) ?></p>
                </div>
              <?php endif; ?>

              <div style="text-align:center;margin-bottom:8px;">
                <a href="<?= htmlspecialchars($urlLogin) ?>"
                   style="background-color:#1993b8;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:6px;display:inline-block;font-weight:bold;">
                  Ingresar al sistema
                </a>
              </div>
            </td>
          </tr>

          <tr>
            <td bgcolor="#f8fafb" style="background-color:#f8fafb;padding:16px 32px;text-align:center;color:#888;font-size:12px;">
              &copy; <?= date('Y') ?> Zona Franca Internacional de Pereira S.A.S. — Todos los derechos reservados.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
