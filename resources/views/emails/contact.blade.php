


<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue chez nous !</title>
</head>
<body>
   <h2>Nouveau message de contact</h2>
    <p><strong>Nom :</strong> {{ $contact->nom }}</p>
    <p><strong>Email :</strong> {{ $contact->email }}</p>
    <p><strong>Sujet :</strong> {{ $contact->sujet }}</p>
    <p><strong>Message :</strong><br>{{ $contact->message }}</p>
</body>
</html>