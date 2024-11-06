<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue {{ $user->prenom }} {{ $user->nom }}</h1>
        </div>
        
        <div class="content">
            <p>Nous sommes ravis de vous accueillir sur notre plateforme de transfert d'argent.</p>
            
            <div class="qr-code">
                <p>Voici votre code QR personnel :</p>
                <img src="{{ $qrUrl }}" alt="QR Code">
            </div>
            
            <p>Informations importantes :</p>
            <ul>
                <li>Votre email : {{ $user->email }}</li>
                <li>Votre téléphone : {{ $user->telephone }}</li>
                <li>Code de vérification : {{ $user->code }}</li>
            </ul>
            
            <p>Pour des raisons de sécurité, veuillez garder ces informations confidentielles.</p>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
        </div>
    </div>
</body>
</html>