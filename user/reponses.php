<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-family:'calibri';
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            resize: vertical;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .attention {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        button, .dashboard-button {
            padding: 10px 20px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: inline-block;
            font-size: 14px;
            text-decoration: none;
        }
        button {
            background-color: #4CAF50;
        }
        .dashboard-button {
            background-color:#4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulaire de Questions</h1>
        <p class="attention">Il faut choisir 5 parmi ces questions dont les réponses vont être en MAJUSCULE et en UN seul mot!</p>
        <form method="post" action="traitement_form.php">
            <label for="q01">Q01 : Le métier de votre grand-père ?</label>
            <input type="text" id="q01" name="q01" required>
            
            <label for="q02">Q02 : Le modèle de votre premier véhicule ?</label>
            <input type="text" id="q02" name="q02" required>
            
            <label for="q03">Q03 : Le métier que vous auriez rêvé d'exercer ?</label>
            <input type="text" id="q03" name="q03" required>
            
            <label for="q04">Q04 : Le personnage historique que vous auriez aimé être ?</label>
            <input type="text" id="q04" name="q04" required>
            
            <label for="q05">Q05 : Le titre de votre livre préféré ?</label>
            <input type="text" id="q05" name="q05" required>
            
            <label for="q06">Q06 : La ville où vous aimeriez le plus vivre ?</label>
            <input type="text" id="q06" name="q06" required>
            
            <label for="q07">Q07 : Le plus beau cadeau que vous avez reçu ?</label>
            <input type="text" id="q07" name="q07" required>
            
            <label for="q08">Q08 : Le prénom que vous refusez de donner à votre enfant ?</label>
            <input type="text" id="q08" name="q08" required>
            
            <label for="q09">Q09 : Le nom de votre premier professeur ?</label>
            <input type="text" id="q09" name="q09" required>
            
            <label for="q10">Q10 : Quel est le nombre composé de six chiffres choisissez-vous ?</label>
            <input type="text" id="q10" name="q10" required>
            
            <div class="button-container">
                <button type="submit" name="submit">Envoyer</button>
                <a href="dashboard.php" class="dashboard-button">Retour vers le Dashboard</a>
            </div>
        </form>
        
    </div>
</body>
</html>
