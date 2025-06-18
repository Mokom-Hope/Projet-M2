<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert d'argent reçu</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 40px 30px; text-align: center; }
        .logo { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 16px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; }
        .title { color: white; font-size: 28px; font-weight: bold; margin: 0; }
        .subtitle { color: rgba(255,255,255,0.9); font-size: 16px; margin: 8px 0 0; }
        .content { padding: 40px 30px; }
        .amount-container { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 30px; text-align: center; margin: 30px 0; }
        .amount { font-size: 36px; font-weight: bold; color: white; margin: 0; }
        .amount-label { color: rgba(255,255,255,0.9); font-size: 14px; margin-top: 10px; }
        .code-container { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; margin: 30px 0; }
        .code { font-size: 24px; font-weight: bold; color: #1f2937; letter-spacing: 4px; font-family: 'Courier New', monospace; }
        .info-box { background: #fef3cd; border: 1px solid #fbbf24; border-radius: 12px; padding: 20px; margin: 30px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: bold; margin: 20px 0; }
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
                <h1 class="title">💰 Transfert reçu !</h1>
                <p class="subtitle">Vous avez reçu un transfert d'argent</p>
            </div>
            
            <div class="content">
                <p style="font-size: 16px; line-height: 1.6; color: #374151; margin: 0 0 20px;">
                    Bonjour,
                </p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #374151; margin: 0 0 20px;">
                    <strong>{{ $sender_name }}</strong> vous a envoyé un transfert d'argent via MoneyTransfer.
                </p>
                
                <div class="amount-container">
                    <div class="amount">{{ $amount }}</div>
                    <div class="amount-label">Montant à récupérer</div>
                </div>
                
                <div class="info-box">
                    <p style="margin: 0 0 15px; font-weight: bold; color: #92400e;">
                        🔐 Question de sécurité :
                    </p>
                    <p style="margin: 0; color: #92400e;">
                        {{ $security_question }}
                    </p>
                </div>
                
                <div class="code-container">
                    <p style="margin: 0 0 10px; font-weight: bold; color: #374151;">Code de récupération :</p>
                    <div class="code">{{ $transfer_code }}</div>
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ $claim_url }}" class="cta-button">
                        🎯 Récupérer mon argent
                    </a>
                </div>
                
                <p style="font-size: 14px; line-height: 1.6; color: #6b7280; margin: 20px 0 0;">
                    <strong>Expire le :</strong> {{ $expires_at }}<br>
                    <strong>Important :</strong> Gardez ce code secret et ne le partagez avec personne.
                </p>
            </div>
            
            <div class="footer">
                <p style="margin: 0 0 10px;">© {{ date('Y') }} MoneyTransfer. Tous droits réservés.</p>
                <p style="margin: 0; font-size: 12px;">
                    Si vous n'attendiez pas ce transfert, ignorez cet email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
