<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center; }
        .logo { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 16px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; }
        .title { color: white; font-size: 28px; font-weight: bold; margin: 0; }
        .subtitle { color: rgba(255,255,255,0.9); font-size: 16px; margin: 8px 0 0; }
        .content { padding: 40px 30px; }
        .code-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 30px; text-align: center; margin: 30px 0; }
        .code { font-size: 36px; font-weight: bold; color: white; letter-spacing: 8px; margin: 0; font-family: 'Courier New', monospace; }
        .code-label { color: rgba(255,255,255,0.9); font-size: 14px; margin-top: 10px; }
        .warning { background: #fef3cd; border: 1px solid #fecaca; border-radius: 12px; padding: 20px; margin: 30px 0; }
        .footer { background: #f8fafc; padding: 30px; text-align: center; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>
    <div style="padding: 40px 20px;">
        <div class="container">
            <div class="header">
                <div class="logo">
                    <svg width="30" height="30" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h1 class="title">Code de vérification</h1>
                <p class="subtitle">Confirmez votre adresse email</p>
            </div>
            
            <div class="content">
                <p style="font-size: 16px; line-height: 1.6; color: #374151; margin: 0 0 20px;">
                    Bonjour,
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #374151; margin: 0 0 20px;">
                    Voici votre code de vérification pour finaliser la création de votre compte MoneyTransfer :
                </p>
                
                <div class="code-container">
                    <div class="code">{{ $code }}</div>
                    <div class="code-label">Code de vérification</div>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: #374151; margin: 20px 0;">
                    Ce code est valide pendant <strong>{{ $expires_in }} minutes</strong> et ne peut être utilisé qu'une seule fois.
                </p>
                
                <div class="warning">
                    <p style="margin: 0; font-size: 14px; color: #92400e;">
                        <strong>⚠️ Important :</strong> Ne partagez jamais ce code avec qui que ce soit. L'équipe MoneyTransfer ne vous demandera jamais votre code de vérification.
                    </p>
                </div>
                
                <p style="font-size: 14px; line-height: 1.6; color: #6b7280; margin: 20px 0 0;">
                    Si vous n'avez pas demandé ce code, ignorez cet email.
                </p>
            </div>
            
            <div class="footer">
                <p style="margin: 0 0 10px;">© {{ date('Y') }} MoneyTransfer. Tous droits réservés.</p>
                <p style="margin: 0; font-size: 12px;">
                    Cet email a été envoyé à {{ $email }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
