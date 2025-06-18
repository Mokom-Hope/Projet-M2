<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialisation de mot de passe ImmoChain</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #000;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .button {
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ImmoChain</h1>
        </div>
        <div class="content">
            <h2>Réinitialisation de mot de passe</h2>
            <p>Bonjour,</p>
            <p>Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
            
            <p style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Réinitialiser mon mot de passe</a>
            </p>
            
            <p>Ce lien de réinitialisation expirera le {{ $expiresAt->format('d/m/Y à H:i') }}.</p>
            <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action n'est requise.</p>
            <p>Cordialement,<br>L'équipe ImmoChain</p>
        </div>
        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} ImmoChain. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>

