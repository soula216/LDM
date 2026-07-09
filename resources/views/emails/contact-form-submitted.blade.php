<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Nouveau message de contact</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">

                    {{-- En-tête --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%);border-radius:16px 16px 0 0;padding:32px 36px;text-align:center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <div style="display:inline-block;width:52px;height:52px;background:rgba(255,255,255,0.2);border-radius:14px;line-height:52px;font-size:24px;margin-bottom:16px;">
                                            ✉️
                                        </div>
                                        <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">
                                            Nouveau message de contact
                                        </h1>
                                        <p style="margin:0;font-size:14px;color:rgba(255,255,255,0.88);line-height:1.5;">
                                            Un visiteur a rempli le formulaire sur le site vitrine
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="background-color:#ffffff;padding:36px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">

                            {{-- Badge date --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background:#eff6ff;color:#2563eb;font-size:12px;font-weight:600;padding:6px 14px;border-radius:999px;letter-spacing:0.03em;text-transform:uppercase;">
                                            Reçu le {{ $contact->created_at?->timezone(config('app.timezone'))->format('d/m/Y à H:i') ?? now()->format('d/m/Y à H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Coordonnées --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
                                <tr>
                                    <td width="50%" valign="top" style="padding-right:8px;padding-bottom:12px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                                            <tr>
                                                <td style="padding:16px 18px;">
                                                    <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Nom</p>
                                                    <p style="margin:0;font-size:15px;font-weight:600;color:#0f172a;">{{ $contact->name }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" valign="top" style="padding-left:8px;padding-bottom:12px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                                            <tr>
                                                <td style="padding:16px 18px;">
                                                    <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Email</p>
                                                    <p style="margin:0;font-size:15px;font-weight:600;color:#0f172a;">
                                                        <a href="mailto:{{ $contact->email }}" style="color:#0ea5e9;text-decoration:none;">{{ $contact->email }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @if(filled($contact->phone))
                                <tr>
                                    <td colspan="2" valign="top">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                                            <tr>
                                                <td style="padding:16px 18px;">
                                                    <p style="margin:0 0 4px;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Téléphone</p>
                                                    <p style="margin:0;font-size:15px;font-weight:600;color:#0f172a;">
                                                        <a href="tel:{{ preg_replace('/\s+/', '', $contact->phone) }}" style="color:#0ea5e9;text-decoration:none;">{{ $contact->phone }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>

                            {{-- Message --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <p style="margin:0 0 10px;font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Message</p>
                                        <div style="background:linear-gradient(180deg,#f8fafc 0%,#ffffff 100%);border:1px solid #e2e8f0;border-left:4px solid #0ea5e9;border-radius:0 12px 12px 0;padding:20px 22px;">
                                            <p style="margin:0;font-size:15px;line-height:1.7;color:#334155;white-space:pre-wrap;">{{ $contact->message }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Pièce jointe --}}
                            @if($contact->hasAttachment())
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
                                <tr>
                                    <td style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px 18px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding-right:12px;vertical-align:middle;font-size:22px;line-height:1;">📎</td>
                                                <td style="vertical-align:middle;">
                                                    <p style="margin:0 0 2px;font-size:11px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:0.06em;">Pièce jointe</p>
                                                    <p style="margin:0;font-size:14px;font-weight:600;color:#78350f;">{{ $contact->attachment_name }}</p>
                                                    <p style="margin:4px 0 0;font-size:12px;color:#a16207;">Fichier joint à cet email — téléchargeable depuis votre boîte mail</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- CTA --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-top:4px;">
                                        <a href="{{ $adminUrl }}"
                                           style="display:inline-block;background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%);color:#ffffff;font-size:14px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:12px;box-shadow:0 8px 24px rgba(14,165,233,0.35);">
                                            Voir dans l'administration →
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:16px;">
                                        <p style="margin:0;font-size:13px;color:#64748b;">
                                            Répondez directement à cet email pour contacter
                                            <strong style="color:#0f172a;">{{ $contact->name }}</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Pied de page --}}
                    <tr>
                        <td style="background-color:#0f172a;border-radius:0 0 16px 16px;padding:24px 36px;text-align:center;">
                            <p style="margin:0 0 6px;font-size:14px;font-weight:700;color:#ffffff;letter-spacing:0.02em;">
                                LDM — Digital Max
                            </p>
                            <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.5;">
                                Laboratoire de prothèse dentaire · Notification automatique du site vitrine
                            </p>
                        </td>
                    </tr>

                </table>

                <p style="margin:24px 0 0;font-size:11px;color:#94a3b8;text-align:center;line-height:1.5;">
                    Cet email a été généré automatiquement. Ne répondez pas à cette adresse si vous n'êtes pas le destinataire prévu.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
