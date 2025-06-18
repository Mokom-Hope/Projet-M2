<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code de vérification ImmoChain</title>
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
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            margin: 30px 0;
            color: #000;
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
            <h2>Code de vérification</h2>
            <p>Bonjour,</p>
            <p>Voici votre code de vérification pour vous connecter à votre compte ImmoChain :</p>
            
            <div class="code">{{ $code }}</div>
            
            <p>Ce code est valable pendant 10 minutes.</p>
            <p>Si vous n'avez pas tenté de vous connecter, veuillez ignorer cet email ou contacter notre support.</p>
            <p>Cordialement,<br>L'équipe ImmoChain</p>
        </div>
        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} ImmoChain. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>

